<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int)($_POST['id'] ?? 0);
  $type = $_POST['type'] ?? '';

  if (!$id || !in_array($type, ['task', 'withdrawal'])) {
    exit('invalid input');
  }

  if ($type === 'task') {
    $stmt = $pdo->prepare("UPDATE submissions SET seen = 1 WHERE id = ?");
  } else {
    $stmt = $pdo->prepare("UPDATE withdrawals SET seen = 1 WHERE id = ?");
  }

  echo $stmt->execute([$id]) ? 'success' : 'error';
}
