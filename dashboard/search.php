<?php
session_start();
require '../includes/db.php';

$q = trim($_GET['q'] ?? '');

if (empty($q)) {
    exit;
}

$qLike = "%" . $q . "%";

$stmt = $pdo->prepare("SELECT * FROM tasks WHERE title LIKE :q OR description LIKE :q");
$stmt->execute(['q' => $qLike]);
$tasks = $stmt->fetchAll();

if ($tasks) {
    foreach ($tasks as $task) {
        $taskId = htmlspecialchars($task['id']);
        $title = htmlspecialchars($task['title']);
        $desc  = htmlspecialchars($task['description']);


        echo "<a href='../task.php?id=$taskId' class='task-card' style='text-decoration: none; color: inherit; display: block;'>
                <div class='task-title'>$title</div>
                <div class='task-desc'>$desc</div>
              </a>";
              
    }
} else {
    echo "<div>No tasks found.</div>";
}
?>
