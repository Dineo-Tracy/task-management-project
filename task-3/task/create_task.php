<?php
session_start();

// Check if the user is logged in. If not, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect if not logged in
    exit;
}

include('db.php');

$error = ""; // Initialize error message variable

// Handle form submission for creating a task
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $taskTitle = htmlspecialchars(trim($_POST['task-title']));
    $taskDescription = htmlspecialchars(trim($_POST['task-description']));
    $assignedTo = $_POST['assigned-to'];
    $status = $_POST['status'];
    $createdBy = $_SESSION['user_id']; // Get the current user ID from the session

    // Validate input
    if (empty($taskTitle) || empty($taskDescription) || empty($assignedTo) || empty($status)) {
        $error = "All fields are required.";
    } else {
        try {
            // Prepare and execute the SQL statement to insert the task
            $stmt = $pdo->prepare("
                INSERT INTO tasks (title, description, assigned_to, status, created_by, created_at)
                VALUES (:title, :description, :assigned_to, :status, :created_by, NOW())
            ");
            $stmt->execute([
                'title' => $taskTitle,
                'description' => $taskDescription,
                'assigned_to' => $assignedTo,
                'status' => $status,
                'created_by' => $createdBy
            ]);

            // Redirect to the view task page after successful creation
            header('Location: view_task.php');
            exit;
        } catch (PDOException $e) {
            $error = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task</title>
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
            margin-bottom: 10px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-view {
            background-color: #28a745;
        }

        .btn-view:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Create Task</h1>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="create_task.php" method="POST">
            <div class="form-group">
                <label for="task-title">Task Title:</label>
                <input type="text" id="task-title" name="task-title" required>
            </div>
            <div class="form-group">
                <label for="task-description">Task Description:</label>
                <textarea id="task-description" name="task-description" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="assigned-to">Assigned To:</label>
                <select id="assigned-to" name="assigned-to" required>
                    <option value="">Select User</option>
                    <?php
                    // Fetch the list of users from the database
                    try {
                        $stmt = $pdo->prepare("SELECT id, username FROM users");
                        $stmt->execute();
                        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($users as $user) {
                            echo "<option value='{$user['id']}'>{$user['username']}</option>";
                        }
                    } catch (PDOException $e) {
                        echo "<option value=''>Error loading users</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <button type="submit" class="btn">Create Task</button>
        </form>
        <a href="view_task.php" class="btn btn-view">View Existing Tasks</a>
    </div>
</body>
</html>
