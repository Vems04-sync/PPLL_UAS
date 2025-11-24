<?php
// process/task_edit.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../config/database.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard.php');
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if ($user_id <= 0) {
    header('Location: ../dashboard.php?error=not_logged_in');
    exit;
}

$task_id = intval($_POST['task_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$priority = strtolower(trim($_POST['priority'] ?? 'medium'));
$category_name = trim($_POST['category'] ?? 'Uncategorized');
$deadline = trim($_POST['deadline'] ?? '');
$status = strtolower(trim($_POST['status'] ?? 'pending'));

if ($task_id <= 0 || $title === '' || $deadline === '') {
    header('Location: ../dashboard.php?error=invalid_input');
    exit;
}

$allowed_priorities = ['low','medium','high'];
if (!in_array($priority, $allowed_priorities, true)) $priority = 'medium';
$allowed_status = ['pending','completed'];
if (!in_array($status, $allowed_status, true)) $status = 'pending';

try {
    // ensure task belongs to user
    $stmt = $pdo->prepare('SELECT id FROM tasks WHERE id = ? AND user_id = ? LIMIT 1');
    $stmt->execute([$task_id, $user_id]);
    if (!$stmt->fetch()) {
        header('Location: ../dashboard.php?error=not_found');
        exit;
    }

    // find or create category
    $stmt = $pdo->prepare('SELECT id FROM categories WHERE user_id = ? AND name = ? LIMIT 1');
    $stmt->execute([$user_id, $category_name]);
    $cat = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($cat) {
        $category_id = $cat['id'];
    } else {
        $stmt = $pdo->prepare('INSERT INTO categories (user_id, name, created_at, updated_at) VALUES (?, ?, NOW(), NOW())');
        $stmt->execute([$user_id, $category_name]);
        $category_id = $pdo->lastInsertId();
    }

    // update
    $stmt = $pdo->prepare('UPDATE tasks SET category_id = ?, title = ?, description = ?, priority = ?, deadline = ?, status = ?, updated_at = NOW() WHERE id = ? AND user_id = ?');
    $stmt->execute([$category_id, $title, $description, $priority, $deadline, $status, $task_id, $user_id]);

    header('Location: ../dashboard.php?success=task_updated');
    exit;
} catch (Exception $e) {
    error_log("task_edit error: " . $e->getMessage());
    header('Location: ../dashboard.php?error=server_error');
    exit;
}