<?php
// session_start();
require_once "../config/database.php";

// $user_id = $_SESSION['user_id'] ?? 1;
$user_id = 1;

// Validasi input
$name = trim($_POST['name'] ?? '');
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($name)) {
    // $_SESSION['error_message'] = 'Nama tidak boleh kosong!';
    header("Location: ../profile.php");
    exit;
}

try {
    // Ambil data user saat ini
    $user_query = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $user_query->execute([$user_id]);
    $user = $user_query->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // $_SESSION['error_message'] = 'User tidak ditemukan!';
        header("Location: ../profile.php");
        exit;
    }

    // Update nama
    $update_query = $pdo->prepare("UPDATE users SET name = ?, updated_at = NOW() WHERE id = ?");
    $update_query->execute([$name, $user_id]);

    // Update password jika diisi
    if (!empty($current_password) && !empty($new_password)) {
        // Verifikasi password lama
        if (!password_verify($current_password, $user['password'])) {
            // $_SESSION['error_message'] = 'Password saat ini salah!';
            header("Location: ../profile.php");
            exit;
        }

        // Validasi password baru
        if ($new_password !== $confirm_password) {
            // $_SESSION['error_message'] = 'Password baru dan konfirmasi tidak cocok!';
            header("Location: ../profile.php");
            exit;
        }

        if (strlen($new_password) < 6) {
            // $_SESSION['error_message'] = 'Password baru minimal 6 karakter!';
            header("Location: ../profile.php");
            exit;
        }

        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_password = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        $update_password->execute([$hashed_password, $user_id]);

        // $_SESSION['success_message'] = 'Profil dan password berhasil diperbarui!';
    } else {
        // $_SESSION['success_message'] = 'Profil berhasil diperbarui!';
    }

    // Update session name jika ada
    // if (isset($_SESSION['user_id'])) {
    //     $_SESSION['name'] = $name;
    // }

} catch (PDOException $e) {
    // $_SESSION['error_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
}

header("Location: ../profile.php");
exit;

