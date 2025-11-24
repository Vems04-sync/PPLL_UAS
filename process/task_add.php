<?php
session_start();
require_once __DIR__."/../config/database.php";

$user_id = $_SESSION['user_id'] ?? 0;
if($user_id <= 0){
    echo json_encode(['success'=>false,'message'=>'User tidak valid']); exit;
}

$title = $_POST['title'] ?? '';
$desc = $_POST['description'] ?? '';
$priority = $_POST['priority'] ?? 'medium';
$category = $_POST['category'] ?? 'Uncategorized';
$deadline = $_POST['deadline'] ?: null;

try {
    // Cek category, insert jika belum ada
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name=? AND user_id=?");
    $stmt->execute([$category,$user_id]);
    $cat = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$cat){
        $stmt = $pdo->prepare("INSERT INTO categories (name,user_id) VALUES (?,?)");
        $stmt->execute([$category,$user_id]);
        $cat_id = $pdo->lastInsertId();
    } else {
        $cat_id = $cat['id'];
    }

    // Insert task
    $stmt = $pdo->prepare("INSERT INTO tasks (user_id,title,description,priority,category_id,deadline,status,created_at) VALUES (?,?,?,?,?,?,?,NOW())");
    $stmt->execute([$user_id,$title,$desc,$priority,$cat_id,$deadline,'pending']);
    $task_id = $pdo->lastInsertId();

    echo json_encode([
        'success'=>true,
        'task'=>[
            'id'=>$task_id,
            'title'=>$title,
            'description'=>$desc,
            'priority'=>$priority,
            'category'=>$category,
            'deadline'=>$deadline,
            'status'=>'pending'
        ]
    ]);
} catch(Exception $e){
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}