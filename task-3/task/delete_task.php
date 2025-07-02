<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $taskId = $_POST['id'];

    // Delete the task from the database
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id AND created_by = :userId");
    $stmt->execute(['id' => $taskId, 'userId' => $_SESSION['user_id']]);

    // Redirect back to the task list
    header('Location: view_task.php');
    exit;
}