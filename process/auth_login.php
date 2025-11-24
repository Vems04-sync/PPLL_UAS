<?php
session_start();
require_once '../config/database.php'; // Pastikan ini koneksi PDO

// Ambil action dari URL (?action=register / login / logout)
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    if ($action == 'register') {
        // --- PROSES REGISTER ---

        // PERBAIKAN 1: Ambil 'name' sesuai input name di form register.php
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // 1. Validasi Password
        if ($password !== $confirm_password) {
            echo "<script>alert('Password tidak sama!'); window.location='../register.php';</script>";
            exit();
        }

        // 2. Cek apakah No WA sudah terdaftar
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE phone_number = ?");
        $stmtCheck->execute([$phone]);

        if ($stmtCheck->rowCount() > 0) {
            echo "<script>alert('Nomor WhatsApp sudah terdaftar!'); window.location='../register.php';</script>";
            exit();
        }

        // 3. Hash Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 4. Insert ke Database
        // PERBAIKAN 2: Insert ke kolom 'name' (bukan full_name)
        $sql = "INSERT INTO users (name, phone_number, password) VALUES (?, ?, ?)";
        $stmtInsert = $pdo->prepare($sql);
        $saved = $stmtInsert->execute([$name, $phone, $hashed_password]);

        if ($saved) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='../login.php';</script>";
        }
    } elseif ($action == 'login') {
        // --- PROSES LOGIN ---
        $phone = $_POST['phone'];
        $password = $_POST['password'];

        // 1. Cek User berdasarkan No WA
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone_number = ?");
        $stmt->execute([$phone]);

        // Ambil data user
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Jika user ditemukan
        if ($user) {
            // Verifikasi Password
            if (password_verify($password, $user['password'])) {

                // Login Sukses -> Buat Session
                $_SESSION['user_id'] = $user['id'];

                // PERBAIKAN 3: Ambil kolom 'name' dan simpan ke session 'name'
                // Ini agar cocok dengan dashboard.php yang memanggil $_SESSION['name']
                $_SESSION['name'] = $user['name'];

                $_SESSION['status'] = "login";

                // Redirect ke Dashboard
                header("Location: ../dashboard.php");
                exit();
            } else {
                echo "<script>alert('Password salah!'); window.location='../login.php';</script>";
            }
        } else {
            echo "<script>alert('Nomor WhatsApp tidak ditemukan!'); window.location='../login.php';</script>";
        }
    } elseif ($action == 'logout') {
        // --- PROSES LOGOUT ---
        session_destroy();
        header("Location: ../login.php");
        exit();
    }
} catch (PDOException $e) {
    // Tangkap error database
    die("Database Error: " . $e->getMessage());
}
