<?php
session_start();
require_once __DIR__."/../config/database.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success'=>false,'message'=>'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? 0;
$task_id = intval($_POST['task_id'] ?? 0);

if($user_id <= 0 || $task_id <= 0){
    echo json_encode(['success'=>false,'message'=>'User atau task tidak valid']);
    exit;
}

try {
    // Cek task milik user
    $stmt = $pdo->prepare("SELECT id FROM tasks WHERE id=? AND user_id=? LIMIT 1");
    $stmt->execute([$task_id, $user_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$task){
        echo json_encode(['success'=>false,'message'=>'Task not found']);
        exit;
    }

    // Delete task
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id=? AND user_id=?");
    $stmt->execute([$task_id, $user_id]);

    echo json_encode(['success'=>true, 'task_id'=>$task_id]);

} catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}