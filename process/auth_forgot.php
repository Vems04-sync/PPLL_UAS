<?php
session_start();
require_once '../config/database.php';
require_once '../functions/wa_api.php';

// --- TAMBAHAN PENTING: SET TIMEZONE ---
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST['phone'];

    try {
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE phone_number = ?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $otp = rand(100000, 999999);

            // Waktu sekarang (Sudah WIB)
            $now = date('Y-m-d H:i:s');
            // Waktu expired (10 menit kedepan)
            $expired_time = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // Hapus OTP lama
            $stmtDel = $pdo->prepare("DELETE FROM phone_verifications WHERE phone_number = ?");
            $stmtDel->execute([$phone]);

            // Insert OTP Baru
            $sqlInsert = "INSERT INTO phone_verifications 
                          (phone_number, token, expires_at, created_at, updated_at) 
                          VALUES (?, ?, ?, ?, ?)";

            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([$phone, $otp, $expired_time, $now, $now]);

            send_wa_otp($phone, $otp);

            $_SESSION['reset_phone'] = $phone;

            echo "<script>
                alert('Kode OTP dikirim ke WhatsApp! Berlaku 10 menit.'); 
                window.location='../verification.php'; 
            </script>";
        } else {
            echo "<script>alert('Nomor WhatsApp tidak terdaftar!'); window.location='../forgot_password.php';</script>";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: ../forgot_password.php");
    exit();
}
