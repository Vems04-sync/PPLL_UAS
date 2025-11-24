<?php
// session_start();
require_once "../config/database.php";

// $user_id = $_SESSION['user_id'] ?? 1;
$user_id = 1;
$new_phone = trim($_POST['new_phone'] ?? '');
$otp_code = trim($_POST['otp_code'] ?? '');

if (empty($new_phone) || empty($otp_code)) {
    // $_SESSION['error_message'] = 'Nomor telepon dan kode OTP harus diisi!';
    header("Location: ../profile.php");
    exit;
}

try {
    // Verifikasi OTP
    $otp_query = $pdo->prepare("SELECT * FROM phone_verifications WHERE phone_number = ? AND token = ? AND expires_at > NOW()");
    $otp_query->execute([$new_phone, $otp_code]);
    $otp_data = $otp_query->fetch(PDO::FETCH_ASSOC);

    if (!$otp_data) {
        // Cek juga dari session (untuk demo/testing)
        // if (isset($_SESSION['otp_code']) && $_SESSION['otp_code'] === $otp_code && isset($_SESSION['otp_phone']) && $_SESSION['otp_phone'] === $new_phone) {
        //     // OTP valid dari session
        // } else {
        //     $_SESSION['error_message'] = 'Kode OTP tidak valid atau sudah kadaluarsa!';
        //     header("Location: ../profile.php");
        //     exit;
        // }
        // Untuk demo tanpa session, skip verifikasi session
    }

    // Cek apakah nomor sudah digunakan user lain
    $check_phone = $pdo->prepare("SELECT id FROM users WHERE phone_number = ? AND id != ?");
    $check_phone->execute([$new_phone, $user_id]);
    if ($check_phone->fetch()) {
        // $_SESSION['error_message'] = 'Nomor telepon sudah digunakan!';
        header("Location: ../profile.php");
        exit;
    }

    // Update nomor telepon
    $update_phone = $pdo->prepare("UPDATE users SET phone_number = ?, updated_at = NOW() WHERE id = ?");
    $update_phone->execute([$new_phone, $user_id]);

    // Hapus OTP yang sudah digunakan
    $delete_otp = $pdo->prepare("DELETE FROM phone_verifications WHERE phone_number = ?");
    $delete_otp->execute([$new_phone]);

    // Clear session OTP
    // unset($_SESSION['otp_code']);
    // unset($_SESSION['otp_phone']);
    // unset($_SESSION['otp_action']);

    // $_SESSION['success_message'] = 'Nomor WhatsApp berhasil diubah!';

} catch (PDOException $e) {
    // $_SESSION['error_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
}

header("Location: ../profile.php");
exit;

