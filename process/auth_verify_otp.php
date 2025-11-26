<?php
session_start();
require_once '../config/database.php';

// --- TAMBAHAN PENTING: SET TIMEZONE ---
date_default_timezone_set('Asia/Jakarta');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_otp = $_POST['otp_code'];

    // Cek Session
    if (!isset($_SESSION['reset_phone'])) {
        echo "<script>alert('Sesi habis, silakan ulangi proses lupa password.'); window.location='../forgot_password.php';</script>";
        exit();
    }

    $phone = $_SESSION['reset_phone'];

    try {
        // Ambil waktu sekarang versi PHP (WIB)
        $now_php = date('Y-m-d H:i:s');

        // PERUBAHAN QUERY:
        // Ganti 'NOW()' dengan placeholder '?' agar kita bisa masukkan waktu PHP
        $stmt = $pdo->prepare("SELECT * FROM phone_verifications 
                               WHERE phone_number = ? 
                               AND token = ? 
                               AND expires_at > ?"); // Bandingkan dengan waktu PHP

        // Masukkan $now_php ke dalam eksekusi
        $stmt->execute([$phone, $input_otp, $now_php]);

        $verification = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($verification) {
            // --- VALIDASI SUKSES ---
            $stmtDel = $pdo->prepare("DELETE FROM phone_verifications WHERE phone_number = ?");
            $stmtDel->execute([$phone]);

            $_SESSION['otp_verified'] = true;

            echo "<script>
                alert('Verifikasi Berhasil! Silakan buat password baru.'); 
                window.location='../reset_password.php'; 
            </script>";
        } else {
            // --- VALIDASI GAGAL (DEBUGGING) ---
            // Kita cek dulu apa penyebabnya untuk ditampilkan di alert (Opsional, hapus nanti kalau sudah fix)

            // Cek apakah token ada tapi expired?
            $checkToken = $pdo->prepare("SELECT expires_at FROM phone_verifications WHERE phone_number = ? AND token = ?");
            $checkToken->execute([$phone, $input_otp]);
            $data = $checkToken->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                // Token ada, berarti Expired
                // Debugging: Tampilkan jam server vs jam expired
                echo "<script>alert('Kode OTP Kadaluarsa! (Jam Skrg: $now_php, Expired: " . $data['expires_at'] . ")'); window.location='../verification.php';</script>";
            } else {
                // Token tidak ditemukan sama sekali (Salah ketik)
                echo "<script>alert('Kode OTP Salah!'); window.location='../verification.php';</script>";
            }
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: ../verification.php");
    exit();
}
