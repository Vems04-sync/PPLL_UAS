<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config/database.php";

// Set timezone
date_default_timezone_set('Asia/Jakarta');

$user_id = (int)($_SESSION['user_id'] ?? 0);
$otp_code = trim($_POST['otp_code'] ?? '');

if ($user_id <= 0) {
    echo "<script>alert('Session tidak valid, silakan login ulang.'); window.location='../login.php';</script>";
    exit;
}

if (empty($otp_code)) {
    echo "<script>alert('Kode OTP harus diisi!'); window.location='../profile.php';</script>";
    exit;
}

try {
    // Ambil nomor telepon user
    $user_query = $pdo->prepare("SELECT phone_number FROM users WHERE id = ?");
    $user_query->execute([$user_id]);
    $user = $user_query->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<script>alert('User tidak ditemukan!'); window.location='../profile.php';</script>";
        exit;
    }

    $phone_number = $user['phone_number'];

    // Verifikasi OTP
    $otp_query = $pdo->prepare("SELECT * FROM phone_verifications WHERE phone_number = ? AND token = ? AND expires_at > NOW()");
    $otp_query->execute([$phone_number, $otp_code]);
    $otp_data = $otp_query->fetch(PDO::FETCH_ASSOC);

    if (!$otp_data) {
        echo "<script>alert('Kode OTP tidak valid atau sudah kadaluarsa!'); window.location='../profile.php';</script>";
        exit;
    }

    // Mulai transaksi untuk memastikan semua data terhapus atau tidak ada yang terhapus
    $pdo->beginTransaction();

    try {
        // Hapus semua tasks milik user
        $delete_tasks = $pdo->prepare("DELETE FROM tasks WHERE user_id = ?");
        $delete_tasks->execute([$user_id]);

        // Hapus semua categories milik user
        $delete_categories = $pdo->prepare("DELETE FROM categories WHERE user_id = ?");
        $delete_categories->execute([$user_id]);

        // Hapus phone verifications milik user
        $delete_phone_verifications = $pdo->prepare("DELETE FROM phone_verifications WHERE phone_number = ?");
        $delete_phone_verifications->execute([$phone_number]);

        // Hapus user
        $delete_user = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $delete_user->execute([$user_id]);

        // Commit transaksi jika semua berhasil
        $pdo->commit();

        // Hapus session agar user benar-benar logout
        session_destroy();

        // Redirect ke halaman login dengan pesan sukses
        header("Location: ../login.php?deleted=1");
        exit;

    } catch (PDOException $e) {
        // Rollback jika ada error
        $pdo->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    echo "<script>alert('Terjadi kesalahan: " . addslashes($e->getMessage()) . "'); window.location='../profile.php';</script>";
    exit;
}

