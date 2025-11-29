<?php
// config/database.php
// Deteksi otomatis: Apakah di Laptop (Localhost) atau di InfinityFree (Hosting)
$whitelist = array('127.0.0.1', '::1');
if (in_array($_SERVER['REMOTE_ADDR'], $whitelist) || $_SERVER['SERVER_NAME'] == 'localhost') {
    // --- SETTINGAN LAPTOP (LOKAL) ---
    $host = "localhost";
    $dbname = "project_management_task_db"; // Sesuaikan nama DB di laptop
    $username = "root";
    $password = "";
} else {
    // --- SETTINGAN INFINITYFREE (HOSTING) ---

    $host = "sql100.infinityfree.com"; 
    
    $dbname = "if0_40537993_management_task"; 
    
    $username = "if0_40537993";      
    
    $password = "LBTcu6Acv7e7Rcm"; 
}

try {
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
die("Koneksi database gagal: " . $e->getMessage());
}
?>