<?php
// proces/send_notifications.php
require_once '../config/database.php'; // Sesuaikan path jika perlu

// --- SECURITY KEY ---
// Ganti 'rahasia123' dengan kata sandi unik pilihan Anda
$secretKey = "rahasia123";

// Cek apakah ada kunci di URL untuk keamanan
if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    http_response_code(403);
    die("Akses Ditolak: Kunci keamanan salah.");
}

$fonnteToken = "D6BcmLVP7rUgYZgFagvD"; 

// 1. UPDATE QUERY
// Logika Baru: Ambil tugas dari H-3 (Masa Lalu/Telat) sampai H+3 (Masa Depan/Reminder)
// DATE_SUB = Mundur 3 hari, DATE_ADD = Maju 3 hari
$sql = "
    SELECT 
        t.title,
        t.description,
        t.priority,
        t.deadline,
        c.name as category_name,
        u.name,
        u.phone_number
    FROM tasks t
    JOIN users u ON t.user_id = u.id
    LEFT JOIN categories c ON t.category_id = c.id
    WHERE 
        u.is_wa_notification_active = 1 
        AND t.status = 'pending'
        AND t.deadline BETWEEN DATE_SUB(CURDATE(), INTERVAL 3 DAY) AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($tasks) > 0) {
        $count = 0;
        foreach ($tasks as $task) {
            
            // --- LOGIKA WAKTU (Reset jam ke 00:00:00 agar hitungan hari akurat) ---
            $deadlineDate = new DateTime($task['deadline']);
            $deadlineDate->setTime(0, 0, 0); 
            
            $today = new DateTime();
            $today->setTime(0, 0, 0);
            
            // Hitung selisih
            $interval = $today->diff($deadlineDate);
            $daysDiff = $interval->days; // Jumlah selisih hari (positif)
            $isPast = $interval->invert; // 1 jika deadline sudah lewat (Masa Lalu), 0 jika belum

            // --- FORMAT DATA ---
            $category = $task['category_name'] ?? 'Uncategorized';
            
            // Prioritas & Emoticon
            $priority = strtolower($task['priority']);
            $prioDisplay = "";
            if ($priority == 'high') {
                $prioDisplay = "🔴 High (Tinggi)";
            } elseif ($priority == 'medium') {
                $prioDisplay = "🟡 Medium (Sedang)";
            } else {
                $prioDisplay = "🟢 Low (Rendah)";
            }

            // Deskripsi Pendek
            $cleanDesc = strip_tags($task['description']);
            if (strlen($cleanDesc) > 100) {
                $cleanDesc = substr($cleanDesc, 0, 97) . "...";
            }
            $descDisplay = !empty($cleanDesc) ? $cleanDesc : "-";

            // --- SUSUN PESAN DINAMIS ---
            $message = "Halo *{$task['name']}*! 👋\n\n";

            // A. LOGIKA HEADER PESAN (Beda kondisi, beda pesan)
            if ($daysDiff == 0) {
                // HARI INI
                $message .= "🚨 *PERINGATAN DEADLINE HARI INI!*\n";
                $message .= "Jangan lupa selesaikan tugas ini sebelum besok ya.\n\n";
                $timeStatus = "⏰ Deadline: *HARI INI* ({$task['deadline']})";
                
            } elseif ($isPast == 1) {
                // SUDAH LEWAT (TERLAMBAT)
                $message .= "⚠️ *KAMU TERLAMBAT {$daysDiff} HARI!*\n";
                $message .= "Tugas ini seharusnya sudah selesai. Segera kerjakan sekarang!\n\n";
                $timeStatus = "❌ Telat: *{$daysDiff} hari* (Sejak {$task['deadline']})";
                
            } else {
                // MASA DEPAN (REMINDER)
                $message .= "🔔 *REMINDER TUGAS*\n";
                $message .= "Mengingatkan tugas berikut mendekati deadline:\n\n";
                $timeStatus = "⏰ Deadline: *{$daysDiff} hari lagi* ({$task['deadline']})";
            }

            // B. ISI DETAIL TUGAS
            $message .= "📌 *JUDUL*: {$task['title']}\n";
            $message .= "📂 *Kategori*: {$category}\n";
            $message .= "⚡ *Prioritas*: {$prioDisplay}\n";
            $message .= "📝 *Deskripsi*: \n_{$descDisplay}_\n\n";
            
            // C. STATUS WAKTU DI BAWAH
            $message .= $timeStatus . "\n\n";
            $message .= "Semangat! Jangan ditunda-tunda! 💪";

            // Kirim WA via Fonnte
            sendFonnte($task['phone_number'], $message, $fonnteToken);
            
            $count++;
            sleep(1); // Jeda antrian agar tidak spamming server
        }
        echo "Berhasil memproses notifikasi untuk " . $count . " tugas (Reminder & Overdue).";
    } else {
        echo "Tidak ada tugas dalam rentang H-3 s.d H+3.";
    }

} catch (PDOException $e) {
    echo "Error Database: " . $e->getMessage();
}

// Fungsi Kirim (Tetap sama)
function sendFonnte($target, $message, $token) {
    // Normalisasi nomor HP (08xx -> 628xx)
    if (substr($target, 0, 1) == '0') {
        $target = '62' . substr($target, 1);
    }
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.fonnte.com/send',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
        'target' => $target,
        'message' => $message,
      ),
      CURLOPT_HTTPHEADER => array(
        "Authorization: $token"
      ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
}
?>