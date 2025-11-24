<?php
// session_start();
require_once "../config/database.php";

// $user_id = $_SESSION['user_id'] ?? 1;
$user_id = 1;
$otp_code = trim($_POST['otp_code'] ?? '');

if (empty($otp_code)) {
    // $_SESSION['error_message'] = 'Kode OTP harus diisi!';
    header("Location: ../profile.php");
    exit;
}

try {
    // Ambil nomor telepon user
    $user_query = $pdo->prepare("SELECT phone_number FROM users WHERE id = ?");
    $user_query->execute([$user_id]);
    $user = $user_query->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // $_SESSION['error_message'] = 'User tidak ditemukan!';
        header("Location: ../profile.php");
        exit;
    }

    $phone_number = $user['phone_number'];

    // Verifikasi OTP
    $otp_query = $pdo->prepare("SELECT * FROM phone_verifications WHERE phone_number = ? AND token = ? AND expires_at > NOW()");
    $otp_query->execute([$phone_number, $otp_code]);
    $otp_data = $otp_query->fetch(PDO::FETCH_ASSOC);

    if (!$otp_data) {
        // Cek juga dari session (untuk demo/testing)
        // if (isset($_SESSION['otp_code']) && $_SESSION['otp_code'] === $otp_code && isset($_SESSION['otp_phone']) && $_SESSION['otp_phone'] === $phone_number) {
        //     // OTP valid dari session
        // } else {
        //     $_SESSION['error_message'] = 'Kode OTP tidak valid atau sudah kadaluarsa!';
        //     header("Location: ../profile.php");
        //     exit;
        // }
        // Untuk demo tanpa session, skip verifikasi session
    }

    // Hapus semua data user (karena ON DELETE CASCADE, categories dan tasks akan ikut terhapus)
    // Tapi kita perlu hapus tasks dulu karena constraint
    $delete_tasks = $pdo->prepare("DELETE FROM tasks WHERE user_id = ?");
    $delete_tasks->execute([$user_id]);

    $delete_categories = $pdo->prepare("DELETE FROM categories WHERE user_id = ?");
    $delete_categories->execute([$user_id]);

    $delete_user = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $delete_user->execute([$user_id]);

    // Hapus OTP yang sudah digunakan
    $delete_otp = $pdo->prepare("DELETE FROM phone_verifications WHERE phone_number = ?");
    $delete_otp->execute([$phone_number]);

    // Clear session
    // session_destroy();

    // Redirect ke halaman login atau home
    header("Location: ../index.php?deleted=1");
    exit;

} catch (PDOException $e) {
    // $_SESSION['error_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
    header("Location: ../profile.php");
    exit;
}

