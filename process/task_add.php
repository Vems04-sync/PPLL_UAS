<?php
session_start();
require_once __DIR__."/../config/database.php";

$user_id = $_SESSION['user_id'] ?? 0;
if($user_id <= 0){
    echo json_encode(['success'=>false,'message'=>'User tidak valid']); exit;
}

// 1. UBAH INI: Ambil ID, bukan Nama
$title = $_POST['title'] ?? '';
$desc = $_POST['description'] ?? '';
$priority = $_POST['priority'] ?? 'medium';
$category_input_id = $_POST['category_id'] ?? ''; // Input dari form select
$deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;

try {
    // 2. LOGIKA BARU: Cek ID Kategori
    $final_cat_id = null;
    $final_cat_name = 'Uncategorized'; // Default nama untuk response JSON

    if (!empty($category_input_id)) {
        // Cek apakah kategori ini benar milik user tersebut (Security Check)
        $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE id = ? AND user_id = ?");
        $stmt->execute([$category_input_id, $user_id]);
        $catData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($catData) {
            $final_cat_id = $catData['id'];
            $final_cat_name = $catData['name']; // Simpan nama untuk dikirim balik ke JS
        }
        // Jika tidak ditemukan (misal user iseng ganti value inspect element),
        // otomatis akan jadi NULL (Uncategorized)
    }

    // 3. INSERT TASK (Gunakan $final_cat_id)
    // Hapus logika insert category yang lama, langsung insert task saja
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, priority, category_id, deadline, status, created_at) VALUES (?,?,?,?,?,?,?,NOW())");
    $stmt->execute([$user_id, $title, $desc, $priority, $final_cat_id, $deadline, 'pending']);
    
    $task_id = $pdo->lastInsertId();

    // 4. RESPONSE
    echo json_encode([
        'success' => true,
        'task' => [
            'id' => $task_id,
            'title' => $title,
            'description' => $desc,
            'priority' => $priority,
            
            // Kirim balik ID dan Nama agar JS bisa update tabel tanpa refresh
            'category_id' => $final_cat_id, 
            'category' => $final_cat_name, // Penting: Kirim Nama, bukan ID, untuk tampilan tabel
            
            'deadline' => $deadline,
            'status' => 'pending'
        ]
    ]);

} catch(Exception $e){
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}
?>