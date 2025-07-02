<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <nav>
            <a href="Create_task.php">Create Task</a>
            <a href="Assign_task.php">Assign Task</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <h2>Your Tasks</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $tasks = $pdo->prepare("SELECT * FROM tasks WHERE created_by = ? OR assigned_to = ?");
                $tasks->execute([$user_id, $user_id]);

                foreach ($tasks as $task) {
                    echo "<tr>
                            <td>" . htmlspecialchars($task['title']) . "</td>
                            <td>" . htmlspecialchars($task['description']) . "</td>
                            <td>" . htmlspecialchars($task['status']) . "</td>
                            <td>
                                <a href='Update_task.php?id={$task['id']}'>Edit</a> |
                                <a href='Delete_task.php?id={$task['id']}' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
    <script src="scripts.js"></script>

</body>
</html>
