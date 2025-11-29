<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/database.php";

$user_id = $_SESSION['user_id'] ?? 1;

// Ambil data POST
$name = trim($_POST['name'] ?? '');
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// [BARU] Ambil status notifikasi (Jika dicentang nilainya 1, jika tidak 0)
$is_wa_active = isset($_POST['wa_notification']) ? 1 : 0; //

if ($user_id <= 0 || empty($name)) {
    $_SESSION['error_message'] = "Nama tidak boleh kosong.";
    header("Location: ../profile.php");
    exit;
}

try {
    $user_query = $pdo->prepare("SELECT id, password FROM users WHERE id = ?");
    $user_query->execute([$user_id]);
    $user = $user_query->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: ../profile.php");
        exit;
    }

    // [BARU] Query Update sekarang menyertakan is_wa_notification_active
    $update_name = $pdo->prepare("UPDATE users SET name = ?, is_wa_notification_active = ?, updated_at = NOW() WHERE id = ?");
    $update_name->execute([$name, $is_wa_active, $user_id]); //


    // Update password bila semua field diisi
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $_SESSION['error_message'] = "Password saat ini salah.";
            header("Location: ../profile.php");
            exit;
        }

        if ($new_password !== $confirm_password || strlen($new_password) < 6) {
            $_SESSION['error_message'] = "Konfirmasi password tidak cocok atau kurang dari 6 karakter.";
            header("Location: ../profile.php");
            exit;
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_password = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        $update_password->execute([$hashed_password, $user_id]);
    }

    if (isset($_SESSION['user_id'])) {
        $_SESSION['name'] = $name;
    }
    
    $_SESSION['success'] = "Profil berhasil diperbarui.";

} catch (PDOException $e) {
    $_SESSION['error_message'] = "Terjadi kesalahan database.";
    // error_log($e->getMessage());
}

// [PERBAIKAN KRUSIAL]: Tambahkan parameter ?t=time() untuk mencegah caching browser
header("Location: ../profile.php?t=" . time());
exit;
?>