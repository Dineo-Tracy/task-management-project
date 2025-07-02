<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $taskId = $_POST['task-id'];
    $taskTitle = $_POST['task-title'];
    $taskDescription = $_POST['task-description'];
    $assignedTo = $_POST['assigned-to'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("
        UPDATE tasks
        SET title = :title, description = :description, assigned_to = :assigned_to, status = :status
        WHERE id = :id
    ");
    $stmt->execute([
        'title' => $taskTitle,
        'description' => $taskDescription,
        'assigned_to' => $assignedTo,
        'status' => $status,
        'id' => $taskId
    ]);

    header('Location: view_task.php');
    exit;
}

// Fetch the task details from the database
$taskId = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
$stmt->execute(['id' => $taskId]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Task</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .form-group textarea {
            resize: vertical;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Update Task</h1>
        <?php
        include('db.php');

        // Fetch the task details from the database
        $taskId = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $taskId]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <form action="update_task.php" method="POST">
            <input type="hidden" name="task-id" value="<?php echo $task['id']; ?>">
            <div class="form-group">
                <label for="task-title">Task Title:</label>
                <input type="text" id="task-title" name="task-title" value="<?php echo $task['title']; ?>" required>
            </div>
            <div class="form-group">
                <label for="task-description">Task Description:</label>
                <textarea id="task-description" name="task-description" rows="3" required><?php echo $task['description']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="assigned-to">Assigned To:</label>
                <select id="assigned-to" name="assigned-to" required>
                    <option value="">Select User</option>
                    <?php
                    // Fetch the list of logged-in users from the database
                    $stmt = $pdo->prepare("SELECT id, username FROM users");
                    $stmt->execute();
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($users as $user) {
                        $selected = ($user['id'] == $task['assigned_to']) ? 'selected' : '';
                        echo "<option value='{$user['id']}' $selected>{$user['username']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="Pending" <?php echo ($task['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="In Progress" <?php echo ($task['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                    <option value="Completed" <?php echo ($task['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                </select>
            </div>
            <button type="submit" class="btn">Update Task</button>
        </form>
    </div>
</body>
</html>