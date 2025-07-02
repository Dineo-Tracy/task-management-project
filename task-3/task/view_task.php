<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include('db.php');
$userId = $_SESSION['user_id'];

// Fetch all tasks created by the logged-in user
$stmt = $pdo->prepare("
    SELECT
        t.id,
        t.title,
        t.description,
        t.assigned_to,
        t.status,
        t.created_at,
        u.username AS assigned_to_username
    FROM tasks t
    LEFT JOIN users u ON t.assigned_to = u.id
    WHERE t.created_by = :userId
    ORDER BY t.created_at DESC
");
$stmt->execute(['userId' => $userId]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Tasks</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .task-list {
            max-width: 900px;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .task-list h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .task-list table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .task-list th, .task-list td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .task-list th {
            background-color: #f4f4f4;
        }

        .status {
            font-weight: bold;
        }

        .status.Pending {
            color: #ff9800;
        }

        .status.Completed {
            color: #4caf50;
        }

        .status.In-Progress {
            color: #2196f3;
        }

        .actions a,
        .actions button {
            display: inline-block;
            padding: 8px 12px;
            margin-right: 5px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }

        .actions a {
            background-color: #007bff;
            color: white;
        }

        .actions a:hover {
            background-color: #0056b3;
        }

        .actions button {
            background-color: #e74c3c;
            color: white;
        }

        .actions button:hover {
            background-color: #c0392b;
        }

        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        @media screen and (max-width: 768px) {
            .task-list {
                padding: 15px;
            }

            .task-list table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>View Tasks</h1>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </header>

    <div class="task-list">
        <h2>Task List</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($tasks) > 0): ?>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['title']) ?></td>
                            <td><?= htmlspecialchars($task['description']) ?></td>
                            <td><?= htmlspecialchars($task['assigned_to_username'] ?? 'Unassigned') ?></td>
                            <td class="status <?= htmlspecialchars($task['status']) ?>">
                                <?= htmlspecialchars($task['status']) ?>
                            </td>
                            <td><?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($task['created_at']))) ?></td>
                            <td class="actions">
                                <a href="update_task.php?id=<?= htmlspecialchars($task['id']) ?>">Update</a>
                                <form action="delete_task.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($task['id']) ?>">
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this task?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">No tasks found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <p>&copy; <?= date('Y') ?> Task Management System</p>
    </footer>
</body>
</html>
