
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
    exit(0);
}

require 'includes/db.php';

$error = "";

// detect API request
$isApi = strpos($_SERVER["CONTENT_TYPE"] ?? '', "application/json") !== false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($isApi) {
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");

        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
    } else {
        $email = $_POST['email'];
        $password = $_POST['password'];
    }

    // secure query
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {

        // API response
        if ($isApi) {
            echo json_encode([
                "status" => "success",
                "message" => "Login successful",
                "user" => $user
            ]);
            exit();
        }

        // normal login
        $_SESSION['user'] = $user;

        if ($user['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard/user_dashboard.php");
        }
        exit();

    } else {

        if ($isApi) {
            echo json_encode([
                "status" => "error",
                "message" => "Invalid email or password"
            ]);
            exit();
        }

        $error = "Invalid email or password.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Wallet App</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
 <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    html, body {
      height: 100%;
      background: linear-gradient(to right, #2196f3, #673ab7);
    }
    .loginbody{
    width: 300px;
    height: 551px;
    background-color: white;
    display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
}
    
    
  </style>
<nav class="navbar">

    <a href="index.php"><div class="d"> <img src="image.png" alt="">
    <div class="logo">WalletApp</div></div></a>
    <label class="lab">
        <input type="checkbox">
    <div class="toggle">
            <span class="top common"></span>
            <span class="mid common"></span>
            <span class="bottom common"></span>
        </div>
        
        <div class="menu">
    <h1>menu</h1>
    <ul>
        <li><a href="#">Regiser</a></li>
        <li><a href="login.php">Login</a></li>
    </ul>
    </div>
    
</label>
  </nav>
<div class="login-body">
      <div class="loginbody">
      <h2>Welcome Back</h2>
     <a href="index.html"> <img src="image.png" alt=""></a>
     <form method="POST">
  <input name="email" type="email" placeholder="Email Address" required />
  <input name="password" type="password" placeholder="Password" required />
  <button class="a" type="submit">Login</button>

  <!-- Error message shown here -->
  <?php if (!empty($error)): ?>
      <p style="color:red; margin-top:10px;"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <p>Don't have an account? <a href="register.php">Sign up</a></p>
</form>

    </div>
    </div>
   

  <footer class="footer">
    <p>© 2025 WalletApp. All rights reserved.</p>
  </footer>
</body>
</html>


