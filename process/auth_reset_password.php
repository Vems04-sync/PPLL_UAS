<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 1. Ambil data
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_SESSION['reset_phone'];

    // 2. Validasi Session (Keamanan Ganda)
    if (!isset($_SESSION['otp_verified']) || !isset($_SESSION['reset_phone'])) {
        header("Location: ../login.php");
        exit();
    }

    // 3. Validasi Password sama
    if ($new_password !== $confirm_password) {
        echo "<script>alert('Konfirmasi password tidak cocok!'); window.location='../reset_password.php';</script>";
        exit();
    }

    try {
        // 4. Hash Password Baru
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // 5. Update Password di Database
        // Menggunakan kolom 'phone_number' sebagai kunci update
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE phone_number = ?");
        $updated = $stmt->execute([$hashed_password, $phone]);

        if ($updated) {
            // 6. Hapus semua session reset (Bersih-bersih)
            unset($_SESSION['reset_phone']);
            unset($_SESSION['reset_name']);
            unset($_SESSION['otp_verified']);

            // 7. Sukses! Arahkan ke Login
            echo "<script>
                alert('Password berhasil diubah! Silakan login dengan password baru.'); 
                window.location='../login.php'; 
            </script>";
        } else {
            echo "<script>alert('Gagal mengupdate password. Silakan coba lagi.'); window.location='../reset_password.php';</script>";
        }
    } catch (PDOException $e) {
        die("Error Database: " . $e->getMessage());
    }
} else {
    header("Location: ../reset_password.php");
    exit();
}
