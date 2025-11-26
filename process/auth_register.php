<?php
// Load koneksi database (PDO)
require_once '../config/database.php';

// Cek apakah data dikirim via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Ambil data form
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Validasi Password
    if ($password !== $confirm_password) {
        echo "<script>alert('Password tidak sama!'); window.location='../register.php';</script>";
        exit();
    }

    try {
        // 2. Cek Nomor WA
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE phone_number = ?");
        $stmtCheck->execute([$phone]);

        if ($stmtCheck->rowCount() > 0) {
            echo "<script>alert('Nomor WhatsApp sudah terdaftar!'); window.location='../register.php';</script>";
            exit();
        }

        // 3. Hash Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // --- BAGIAN BARU (Cara 2) ---
        // Ambil waktu sekarang (Format Tahun-Bulan-Tanggal Jam:Menit:Detik)
        $now = date('Y-m-d H:i:s');

        // 4. Insert Data
        // Tambahkan kolom created_at dan updated_at
        $sql = "INSERT INTO users (name, phone_number, password, is_wa_notification_active, created_at, updated_at) 
                VALUES (?, ?, ?, 1, ?, ?)";

        $stmtInsert = $pdo->prepare($sql);

        // Eksekusi query insert
        // Masukkan variabel $now dua kali (untuk created_at dan updated_at)
        $saved = $stmtInsert->execute([$name, $phone, $hashed_password, $now, $now]);

        if ($saved) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='../login.php';</script>";
        }
    } catch (PDOException $e) {
        die("Error Registrasi: " . $e->getMessage());
    }
} else {
    header("Location: ../register.php");
    exit();
}
