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

    // --- 1. Validasi Format Nomor HP ---

    // Cek Panjang
    if (strlen($phone) > 15 || strlen($phone) < 10) {
        echo "<script>alert('Nomor WhatsApp tidak valid (harus 10-15 digit)!'); window.location='../register.php';</script>";
        exit();
    }

    // Cek Awalan (+62 atau 0)
    if (!preg_match('/^(\+62|0)/', $phone)) {
        echo "<script>alert('Format Nomor Salah! Harus diawali 08... atau +628... (Region Indonesia)'); window.location='../register.php';</script>";
        exit();
    }

    // --- 2. Validasi Password ---
    if ($password !== $confirm_password) {
        echo "<script>alert('Password tidak sama!'); window.location='../register.php';</script>";
        exit();
    }

    try {
        // --- 3. CEK APAKAH NOMOR SUDAH ADA DI DATABASE (Duplikat) ---
        // Ini adalah bagian yang memunculkan pop up jika nomor sudah dipakai
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE phone_number = ?");
        $stmtCheck->execute([$phone]);

        if ($stmtCheck->rowCount() > 0) {
            // PESAN POP UP MUNCUL DISINI
            echo "<script>
                    alert('Nomor WhatsApp tersebut SUDAH DIGUNAKAN! Silakan gunakan nomor lain atau Login.'); 
                    window.location='../register.php';
                  </script>";
            exit(); // Stop proses agar tidak lanjut menyimpan
        }

        // --- 4. Proses Simpan Data (Jika lolos semua cek diatas) ---

        // Hash Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Ambil waktu sekarang
        $now = date('Y-m-d H:i:s');

        // Insert Data
        $sql = "INSERT INTO users (name, phone_number, password, is_wa_notification_active, created_at, updated_at) 
                VALUES (?, ?, ?, 1, ?, ?)";

        $stmtInsert = $pdo->prepare($sql);
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
