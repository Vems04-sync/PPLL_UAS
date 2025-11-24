<?php
// Load koneksi database (PDO)
require_once '../config/database.php';

// Cek apakah data dikirim via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Ambil data form (Sesuaikan dengan input name di HTML)
    // Ubah dari full_name jadi name karena kolom DB kamu 'name'
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
        // 2. Cek Nomor WA (Gunakan Prepared Statement)
        // Kolom database: phone_number
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE phone_number = ?");
        $stmtCheck->execute([$phone]);

        // Cek jumlah baris
        if ($stmtCheck->rowCount() > 0) {
            echo "<script>alert('Nomor WhatsApp sudah terdaftar!'); window.location='../register.php';</script>";
            exit();
        }

        // 3. Hash Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 4. Insert Data
        // PERBAIKAN: Ubah 'full_name' menjadi 'name' sesuai tabel database kamu
        // Kolom is_wa_notification_active biasanya default 1 di database, atau kita bisa set manual (opsional)
        $sql = "INSERT INTO users (name, phone_number, password, is_wa_notification_active) VALUES (?, ?, ?, 1)";
        $stmtInsert = $pdo->prepare($sql);

        // Eksekusi query insert
        $saved = $stmtInsert->execute([$name, $phone, $hashed_password]);

        if ($saved) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='../login.php';</script>";
        }
    } catch (PDOException $e) {
        // Menangkap error jika koneksi atau query bermasalah
        die("Error Registrasi: " . $e->getMessage());
    }
} else {
    // Jika file ini diakses langsung tanpa kirim form
    header("Location: ../register.php");
    exit();
}
