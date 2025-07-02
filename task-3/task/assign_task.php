<?php
include 'db.php';
session_start();

// Check if user is logged in and if the user has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect non-admin users away from the page
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'] ?? null;
    $assigned_to = $_POST['assigned_to'] ?? null;

    if ($task_id && $assigned_to) {
        $sql = "UPDATE tasks SET assigned_to = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$assigned_to, $task_id])) {
            echo "<script>alert('Task assigned successfully!'); window.location.href='dashboard.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error assigning task. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Please select both a task and a user.');</script>";
    }
}

// Fetch unassigned tasks and users
$tasks = $pdo->query("SELECT id, title FROM tasks WHERE assigned_to IS NULL")->fetchAll();
$users = $pdo->query("SELECT id, username FROM users")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Task</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .no-data {
            text-align: center;
            margin-top: 20px;
            color: #888;
        }

        .restricted {
            background-color: #f7f7f7;
            border: 1px solid #ddd;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <form action="Assign_task.php" method="POST">
        <h2>Assign a Task</h2>

        <!-- Task Dropdown -->
        <?php if (!empty($tasks)): ?>
            <label for="task_id">Select Task:</label>
            <select name="task_id" required>
                <option value="" disabled selected>Select a task</option>
                <?php foreach ($tasks as $task): ?>
                    <option value="<?= htmlspecialchars($task['id']) ?>"><?= htmlspecialchars($task['title']) ?></option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <p class="no-data">No unassigned tasks available.</p>
        <?php endif; ?>

        <!-- User Dropdown -->
        <?php if (!empty($users)): ?>
            <label for="assigned_to">Assign To:</label>
            <select name="assigned_to" required>
                <option value="" disabled selected>Select a user</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= htmlspecialchars($user['id']) ?>"><?= htmlspecialchars($user['username']) ?></option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <p class="no-data">No users available to assign tasks.</p>
        <?php endif; ?>

        <!-- Submit Button -->
        <?php if ($_SESSION['role'] === 'admin' && !empty($tasks) && !empty($users)): ?>
            <button type="submit">Assign Task</button>
        <?php else: ?>
            <button type="button" class="restricted" disabled>Assign Task (Admin Only)</button>
        <?php endif; ?>
    </form>
</body>
</html>
