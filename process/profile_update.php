<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/database.php";

// Gunakan user_id dari session jika ada, fallback 1 untuk demo/testing
$user_id = $_SESSION['user_id'] ?? 1;

$name = trim($_POST['name'] ?? '');
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($user_id <= 0 || empty($name)) {
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

    // Update nama
    $update_name = $pdo->prepare("UPDATE users SET name = ?, updated_at = NOW() WHERE id = ?");
    $update_name->execute([$name, $user_id]);

    // Update password bila semua field diisi
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if (!password_verify($current_password, $user['password'])) {
            header("Location: ../profile.php");
            exit;
        }

        if ($new_password !== $confirm_password || strlen($new_password) < 6) {
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

} catch (PDOException $e) {
    // Bisa tambahkan logging jika diperlukan
}

header("Location: ../profile.php");
exit;