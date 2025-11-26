<?php
session_start();
require_once __DIR__."/../config/database.php";
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 0;
$task_id = intval($_POST['task_id'] ?? 0);
$status  = $_POST['status'] ?? 'pending'; // 'pending' atau 'completed'

if($user_id <= 0 || $task_id <= 0){
    echo json_encode(['success'=>false, 'message'=>'Invalid data']);
    exit;
}

try {
    // Update hanya status saja
    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$status, $task_id, $user_id]);

    echo json_encode(['success'=>true]);
} catch(Exception $e){
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}
?>