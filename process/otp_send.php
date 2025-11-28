<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config/database.php";
require_once "../functions/wa_api.php";

header('Content-Type: application/json');

// Set timezone
date_default_timezone_set('Asia/Jakarta');

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'] ?? (int)($_POST['user_id'] ?? 0);

try {
    if ($action === 'change_phone') {
        $phone_number = trim($_POST['phone_number'] ?? '');
        
        if (empty($phone_number)) {
            echo json_encode(['success' => false, 'message' => 'Nomor telepon tidak boleh kosong!']);
            exit;
        }

        // Cek apakah nomor sudah digunakan user lain
        $check_phone = $pdo->prepare("SELECT id FROM users WHERE phone_number = ? AND id != ?");
        $check_phone->execute([$phone_number, $user_id]);
        if ($check_phone->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Nomor telepon sudah digunakan!']);
            exit;
        }

    } elseif ($action === 'delete_account') {
        if ($user_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Session user tidak ditemukan!']);
            exit;
        }

        // Ambil nomor telepon user
        $user_query = $pdo->prepare("SELECT phone_number FROM users WHERE id = ?");
        $user_query->execute([$user_id]);
        $user = $user_query->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User tidak ditemukan!']);
            exit;
        }
        
        $phone_number = $user['phone_number'];
    } else {
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid!']);
        exit;
    }

    // Generate OTP 6 digit
    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Hapus OTP lama untuk nomor ini
    $delete_old = $pdo->prepare("DELETE FROM phone_verifications WHERE phone_number = ?");
    $delete_old->execute([$phone_number]);
    
    // Simpan OTP baru (expires in 10 minutes)
    $now = date('Y-m-d H:i:s');
    $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));
    $insert_otp = $pdo->prepare("INSERT INTO phone_verifications (phone_number, token, expires_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
    $insert_otp->execute([$phone_number, $otp, $expires_at, $now, $now]);
    
    // Kirim OTP via WhatsApp API
    send_wa_otp($phone_number, $otp);
    
    // Untuk testing, return OTP di response (HAPUS INI DI PRODUCTION!)
    echo json_encode([
        'success' => true, 
        'message' => 'OTP berhasil dikirim ke WhatsApp!',
        'otp' => $otp // HAPUS INI DI PRODUCTION! Hanya untuk testing
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}

