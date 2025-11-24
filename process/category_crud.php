<?php
// session_start();
require_once "../config/database.php";

// Untuk demo/testing tanpa session, gunakan user_id = 1 sebagai default
// Jika ada session, gunakan session, jika tidak gunakan default
// $user_id = $_SESSION['user_id'] ?? 1;
$user_id = 1;

// Pastikan user dengan id = 1 ada (untuk demo/testing)
$check_user = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$check_user->execute([$user_id]);
if (!$check_user->fetch()) {
    // Buat user dummy jika belum ada
    $create_user = $pdo->prepare("INSERT INTO users (id, name, phone_number, password, created_at, updated_at) VALUES (1, 'Demo User', '081234567890', ?, NOW(), NOW())");
    $hashed_password = password_hash('demo123', PASSWORD_DEFAULT);
    $create_user->execute([$hashed_password]);
}

$action = $_POST['action'] ?? '';

// Validasi action
if (!in_array($action, ['create', 'update', 'delete'])) {
    // $_SESSION['error_message'] = 'Aksi tidak valid!';
    header("Location: ../categories.php");
    exit;
}

try {
    switch ($action) {
        case 'create':
            // Validasi input
            $name = trim($_POST['name'] ?? '');
            
            if (empty($name)) {
                // $_SESSION['error_message'] = 'Nama kategori tidak boleh kosong!';
                header("Location: ../categories.php");
                exit;
            }

            // Cek apakah kategori dengan nama yang sama sudah ada untuk user ini
            $check_query = $pdo->prepare("SELECT id FROM categories WHERE user_id = ? AND name = ?");
            $check_query->execute([$user_id, $name]);
            if ($check_query->fetch()) {
                // $_SESSION['error_message'] = 'Kategori dengan nama tersebut sudah ada!';
                header("Location: ../categories.php");
                exit;
            }

            // Insert kategori baru
            $insert_query = $pdo->prepare("INSERT INTO categories (user_id, name, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $insert_query->execute([$user_id, $name]);
            
            // $_SESSION['success_message'] = 'Kategori berhasil ditambahkan!';
            break;

        case 'update':
            // Validasi input
            $category_id = $_POST['category_id'] ?? 0;
            $name = trim($_POST['name'] ?? '');
            
            if (empty($name)) {
                // $_SESSION['error_message'] = 'Nama kategori tidak boleh kosong!';
                header("Location: ../categories.php");
                exit;
            }

            if (empty($category_id) || !is_numeric($category_id)) {
                // $_SESSION['error_message'] = 'ID kategori tidak valid!';
                header("Location: ../categories.php");
                exit;
            }

            // Verifikasi bahwa kategori milik user ini
            $verify_query = $pdo->prepare("SELECT id FROM categories WHERE id = ? AND user_id = ?");
            $verify_query->execute([$category_id, $user_id]);
            if (!$verify_query->fetch()) {
                // $_SESSION['error_message'] = 'Kategori tidak ditemukan atau Anda tidak memiliki akses!';
                header("Location: ../categories.php");
                exit;
            }

            // Cek apakah kategori dengan nama yang sama sudah ada (selain kategori yang sedang diedit)
            $check_query = $pdo->prepare("SELECT id FROM categories WHERE user_id = ? AND name = ? AND id != ?");
            $check_query->execute([$user_id, $name, $category_id]);
            if ($check_query->fetch()) {
                // $_SESSION['error_message'] = 'Kategori dengan nama tersebut sudah ada!';
                header("Location: ../categories.php");
                exit;
            }

            // Update kategori
            $update_query = $pdo->prepare("UPDATE categories SET name = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
            $update_query->execute([$name, $category_id, $user_id]);
            
            // $_SESSION['success_message'] = 'Kategori berhasil diperbarui!';
            break;

        case 'delete':
            // Validasi input
            $category_id = $_POST['category_id'] ?? 0;
            
            if (empty($category_id) || !is_numeric($category_id)) {
                // $_SESSION['error_message'] = 'ID kategori tidak valid!';
                header("Location: ../categories.php");
                exit;
            }

            // Verifikasi bahwa kategori milik user ini
            $verify_query = $pdo->prepare("SELECT id, name FROM categories WHERE id = ? AND user_id = ?");
            $verify_query->execute([$category_id, $user_id]);
            $category = $verify_query->fetch(PDO::FETCH_ASSOC);
            
            if (!$category) {
                // $_SESSION['error_message'] = 'Kategori tidak ditemukan atau Anda tidak memiliki akses!';
                header("Location: ../categories.php");
                exit;
            }

            // Hitung jumlah tugas yang menggunakan kategori ini
            $task_count_query = $pdo->prepare("SELECT COUNT(*) as count FROM tasks WHERE category_id = ?");
            $task_count_query->execute([$category_id]);
            $task_count = $task_count_query->fetch(PDO::FETCH_ASSOC)['count'];

            // Delete kategori (dengan ON DELETE SET NULL, tugas akan otomatis menjadi NULL/Uncategorized)
            $delete_query = $pdo->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
            $delete_query->execute([$category_id, $user_id]);
            
            $message = 'Kategori "' . htmlspecialchars($category['name']) . '" berhasil dihapus!';
            if ($task_count > 0) {
                $message .= ' ' . $task_count . ' tugas yang menggunakan kategori ini sekarang menjadi "Uncategorized".';
            }
            // $_SESSION['success_message'] = $message;
            break;
    }

} catch (PDOException $e) {
    // $_SESSION['error_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
}

// Redirect kembali ke halaman categories
header("Location: ../categories.php");
exit;

