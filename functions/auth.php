<?php
// functions.php
session_start();

$base_path = dirname(__DIR__);
require_once $base_path . '/config/database.php'; // load PDO

// fungsi mengecek apakah user sudah login
function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        // Deteksi path berdasarkan lokasi file yang memanggil
        $current_path = $_SERVER['PHP_SELF'];
        
        if (strpos($current_path, '/process/') !== false) {
            // Jika dipanggil dari folder process
            header("Location: ../login.php");
        } else {
            // Jika dipanggil dari root directory
            header("Location: login.php");
        }
        exit;
    }
}

// Fungsi Cek Sudah Login (Dipakai di Login & Register)
// Tujuannya: Mencegah orang yang SUDAH login masuk ke halaman daftar lagi
function checkAlreadyLogin()
{
    if (isset($_SESSION['user_id'])) {
        // Deteksi path berdasarkan lokasi file yang memanggil
        $current_path = $_SERVER['PHP_SELF'];
        
        if (strpos($current_path, '/process/') !== false) {
            // Jika dipanggil dari folder process
            header("Location: ../dashboard.php");
        } else {
            // Jika dipanggil dari root directory
            header("Location: dashboard.php");
        }
        exit;
    }
}

// contoh fungsi login
function login($phone_number, $password)
{
    global $pdo;

    $query = $pdo->prepare("SELECT * FROM users WHERE phone_number = ?");
    $query->execute([$phone_number]);
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        return true;
    }
    return false;
}

// fungsi logout
function logout()
{
    session_destroy();
    header("Location: ../process/auth_login.php");
    exit;
}
