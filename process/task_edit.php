<?php
// process/task_edit.php
session_start();
require_once __DIR__ . "/../config/database.php";

// PENTING: Header ini memberi tahu JS bahwa balasan adalah JSON
header('Content-Type: application/json'); 

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id <= 0) { echo json_encode(['success'=>false, 'message'=>'Unauthorized']); exit; }

$task_id = intval($_POST['task_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$desc = trim($_POST['description'] ?? '');
$priority = $_POST['priority'] ?? 'medium';
$category_id = $_POST['category_id'] ?? ''; 
$deadline = !empty($_POST['deadline']) ? $_POST['deadline'] : null;
$status = $_POST['status'] ?? 'pending';

try {
    // 1. Cek apakah Task ada dan milik User
    $stmt = $pdo->prepare("SELECT id FROM tasks WHERE id=? AND user_id=?");
    $stmt->execute([$task_id, $user_id]);
    if(!$stmt->fetch()){ echo json_encode(['success'=>false, 'message'=>'Task not found']); exit; }

    // 2. Cek Kategori (Untuk mengambil Nama Kategori terbaru)
    $final_cat_id = null;
    $final_cat_name = 'Uncategorized';
    
    if(!empty($category_id)){
        $stmt = $pdo->prepare("SELECT id, name FROM categories WHERE id=? AND user_id=?");
        $stmt->execute([$category_id, $user_id]);
        $c = $stmt->fetch(PDO::FETCH_ASSOC);
        if($c) { 
            $final_cat_id = $c['id']; 
            $final_cat_name = $c['name']; 
        }
    }

    // 3. Update Database
    $stmt = $pdo->prepare("UPDATE tasks SET title=?, description=?, priority=?, category_id=?, deadline=?, status=?, updated_at=NOW() WHERE id=? AND user_id=?");
    $result = $stmt->execute([$title, $desc, $priority, $final_cat_id, $deadline, $status, $task_id, $user_id]);

    if($result){
        // 4. Kirim Balasan Sukses ke JS
        echo json_encode([
            'success' => true,
            'task' => [
                'id' => $task_id,
                'title' => $title,
                'description' => $desc,
                'priority' => $priority,
                'category_id' => $final_cat_id,
                'category' => $final_cat_name, // Penting: Kirim nama untuk update tampilan tabel
                'deadline' => $deadline,
                'status' => $status
            ]
        ]);
    } else {
        echo json_encode(['success'=>false, 'message'=>'Gagal update database']);
    }

} catch (Exception $e) {
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}
?>