<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST['phone'];

    try {
        // 1. Cek apakah Nomor WA terdaftar di database
        // PERBAIKAN: Mengubah 'full_name' menjadi 'name' sesuai database
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE phone_number = ?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // JIKA USER DITEMUKAN

            // Simpan info user sementara di session
            $_SESSION['reset_phone'] = $phone;

            // PERBAIKAN: Mengambil data dari kolom 'name'
            $_SESSION['reset_name'] = $user['name'];

            // Tampilkan alert sukses (Simulasi)
            echo "<script>
                alert('Kode OTP (Simulasi) telah dikirim ke nomor " . $phone . "'); 
                // Di sini nanti diarahkan ke halaman input OTP atau Reset Password
                // Sementara kita arahkan balik ke login saja
                window.location='../login.php'; 
            </script>";
        } else {
            // JIKA USER TIDAK DITEMUKAN
            echo "<script>alert('Nomor WhatsApp tidak terdaftar!'); window.location='../forgot_password.php';</script>";
        }
    } catch (PDOException $e) {
        die("Error Database: " . $e->getMessage());
    }
} else {
    header("Location: ../forgot_password.php");
    exit();
}
