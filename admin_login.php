<?php
session_start();

$ADMIN_ID = 'admin123';           // Your special admin ID
$ADMIN_PASS = '111';   // Your secure admin password

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['admin_id'] ?? '';
  $pass = $_POST['password'] ?? '';

  if ($id === $ADMIN_ID && $pass === $ADMIN_PASS) {
    $_SESSION['is_admin'] = true;
    header("Location: admin_dashboard.php");
    exit;
  } else {
    $error = "Invalid Admin ID or Password";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Login</title>
  <style>
    body { font-family: Arial; background: #f2f2f2; padding: 30px; }
    .login-box {
      background: white;
      padding: 20px;
      max-width: 400px;
      margin: auto;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    input, button {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
    }
    .error { color: red; }
  </style>
</head>
<body>
  <div class="login-box">
    <a href="index.php"><h2>Admin Login</h2></a>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST">
      <input type="text" name="admin_id" placeholder="Admin ID" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
