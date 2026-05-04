<?php
session_start();
require 'includes/db.php';

$userId = $_SESSION['id'] ?? null;


// Fetch user email from database (adjust table/column names)


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $taskId = $_POST['task_id'] ?? null;
  $taskTitle = $_POST['task_title'] ?? null;
  $taskemail = $_POST['email'] ?? null;
  $taskcost = $_POST['reward'] ?? null;



  if (!$taskId || !$taskTitle || !$taskemail || !$taskcost) {
    die("Task information missing.");
  }

  if (!isset($_FILES['screenshot']) || $_FILES['screenshot']['error'] !== UPLOAD_ERR_OK) {
    die("Screenshot upload failed.");
  }

  $uploadDir = 'uploads/';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  $filename = time() . '-' . basename($_FILES['screenshot']['name']);
  $filepath = $uploadDir . $filename;

  $check = getimagesize($_FILES['screenshot']['tmp_name']);
  if ($check === false) {
    die("File is not a valid image.");
  }

  if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $filepath)) {
    $stmt = $pdo->prepare("INSERT INTO submissions ( email,reward, task_id, task_title, file_path, status) VALUES ( ?,?, ?, ?, ?, 'pending')");
    $stmt->execute([ $taskemail,$taskcost, $taskId, $taskTitle, $filepath]);

    header("Location: dashboard/approval.php");
   exit;
  } else {
    die("File upload failed.");
  }
}
?>














