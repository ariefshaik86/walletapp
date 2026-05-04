<?php
session_start();
require 'includes/db.php';

$message = ''; // For showing messages in HTML

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userOtp = $_POST['otp'];
    $realOtp = $_SESSION['reg_otp'] ?? null;

    if ($userOtp == $realOtp) {
        // Get user data from session
        $data = $_SESSION['temp_reg'];

        // Check if email already exists
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$data['email']]);
        if ($check->rowCount() > 0) {
            $message = "❌ Email already exists. <a href='login.php'>Login instead</a>";
        } else {
            // Insert user with referral code
            $referralCode = strtoupper(substr(md5(uniqid()), 0, 8));
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, referral_code, referred_by, wallet)
                                   VALUES (?, ?, ?, ?, ?, 0)");
            $stmt->execute([
                $data['name'], $data['email'], $data['password'],
                $referralCode, $data['referral']
            ]);

            // Step: Apply referral bonus
            if (!empty($data['referral'])) {
                $refStmt = $pdo->prepare("SELECT id FROM users WHERE referral_code = ?");
                $refStmt->execute([$data['referral']]);

                if ($refStmt->rowCount() > 0) {
                    // Add ₹10 to referrer
                    $pdo->prepare("UPDATE users SET wallet = wallet + 10 WHERE referral_code = ?")
                        ->execute([$data['referral']]);

                    // Add ₹5 to new user
                    $pdo->prepare("UPDATE users SET wallet = wallet + 5 WHERE email = ?")
                        ->execute([$data['email']]);
                }
            }

            $message = "✅ Registration complete! <a href='login.php'>Login now</a>";
            session_destroy();
        }
    } else {
        $message = "❌ Invalid OTP. Please try again.";
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
  <style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Inter', sans-serif;
}

body {
  background-color: #f9f9f9;
  color: #333;
}
.navbar{
    height: 80px;
}

.navbar,.d,.nav-icons {
  background-color: #1e1e2f;
  padding: 1rem 2rem;
  display: flex;
  align-items: center;
  color: #4a6bff;
}
.navbar img{
    width: 50px;
    border-radius: 50%; 
    height: 50px;
}
.navbar a{
    text-decoration: none;
}

.logo {
  font-size: 1.5rem;
  font-weight: bold;
  margin-left: 20px;
}

.nav-icons a {
  color: white;
  font-size: 1.2rem;
  margin-left: 1.5rem;
  text-decoration: none;
}

.hero {
  display: flex;
  align-items: center;
  padding: 4rem 2rem;
  background: linear-gradient(to right, #1e90ff, #6a5acd);
  color: white;
  position: static;
}

.hero-text {
  flex: 1;
  min-width: 280px;
  padding: 1rem;
}

.hero-text h1 {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.hero-text p {
  font-size: 1.1rem;
  margin-bottom: 1.7rem;
}

.hero-image {
  flex: 1;
  min-width: 280px;
  padding: 1rem;
}

.hero-image img {
  width: 350px;
  border-radius: 12px;
}

.btn-primary {
  background-color: #fff;
  color: #1e90ff;
  padding: 0.75rem 1.5rem;
  border-radius: 25px;
  text-decoration: none;
  font-weight: 600;
  transition: background 0.3s;

}

.btn-primary:hover {
  background-color: #ddd;
}

.footer {
  text-align: center;
  padding: 1rem;
  background-color: #1e1e2f;
  color: white;
}

/* Sign-up Page Styles */
.signup-body {
  background-color: #f0f4f8;
}

.signup-container {
  display: flex;
  flex-wrap: wrap;
  min-height: 100vh;
}

.signup-image {
  flex: 1;
  min-width: 300px;
}

.signup-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.signup-form {
  flex: 1;
  min-width: 300px;
  background-color: white;
  padding: 4rem 2rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.signup-form h2 {
  margin-bottom: 2rem;
}

.signup-form form {
  display: flex;
  flex-direction: column;
}

.signup-form input {
  margin-bottom: 1.5rem;
  padding: 0.75rem;
  font-size: 1rem;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.signup-form a {
  background-color: #1e90ff;
  color: white;
  padding: 0.75rem;
  font-size: 1rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  text-decoration: none;
}
.lab{
    visibility: hidden;
    margin-right: -500px;
}

.signup-form button:hover {
  background-color: #187bcd;
}

.signup-form p {
  margin-top: 1rem;
}
/* Login Page Styles */
.login-body {
  background: linear-gradient(to right, #1e90ff, #6a5acd);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 100vh;

}
.loginbody{
    width: 400px;
    height: 600px;
    background-color: white;
    display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
}
.loginbody img{
    width: 150px;
    height: 150px;
}



.loginbody h2 {
  margin-bottom: 2rem;
}

.loginbody form {
  display: flex;
  flex-direction: column;
}

.loginbody input {
  margin-bottom: 1.5rem;
  padding: 0.75rem;
  font-size: 1rem;
  border-radius: 6px;
  border: 1px solid #ccc;
  width: 220px;
  height: 100%;
}
.i{
    margin-top:10px;
    margin-left:10px;
    color:red;
}

.a {
  background-color: #1e90ff;
  color: white;
  padding: 10px;
  font-size: 1rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  width: 100px;
  text-decoration: none;
  height:60px;
}

.login-form button:hover {
  background-color: #187bcd;
}

.loginbody p {
  margin-top: 1rem;
}
@media only screen and (max-width:768px){
    .hero-text h1{
        font-size: 40px;
        margin-left: 0;
        margin-top: 80px;

    }
   html,body{
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        width: 100%;
        height: 100%;
    }
    .hero {
  display: flex;
  align-items: center;
  flex-direction: column;
  background: linear-gradient(to right, #1e90ff, #6a5acd);
  color: white;
  padding-left: 30px;
  padding-right: 20px;
  max-width:  100%;

}
.hero-image img{
    width: 150px;
}
.navbar{
    padding: 0;
    position: sticky;
    top: 0%;
}
.hero-text p{
font-size: 25px;
margin-left: 10px;
}

.btn-primary{
    margin-top: 500px;
}
.menu{
    height: 850px;
    width: 180px;
    position: absolute;
    background-color: #fff;
    transform: translateX(-225px);
    visibility: hidden;
    padding: 20px;
    margin-top: -15px;
    color: transparent;
}
nav h1{

    color:#1e1e2f;
    font-weight: 900;
    text-align: right;
    padding: 10px 0;
    padding-right: 30px;
    pointer-events: none;
    font-size: 35px;
}
ul li{
    text-decoration: none;
    list-style: none;
    margin-top: 10px;
}
ul li a{
    color: black;
    text-decoration: none;
    font-weight: 500;
    padding: 5px 0;
    display: block;
    font-size: 20px;
}
ul li:hover a{
    color: black;
}
.lab input{
    display: none;
    visibility: hidden;
    -webkit-appearance: none;

}
.toggle{
    position: absolute;
    height: 30px;
    width: 30px;
    z-index: 1;
    top: 20px;
    right: 15px;
    border-radius: 2px;
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0,0,0.3);
}
.toggle .common{
position: absolute;
height: 2px;
width: 10px;
background-color:black;
border-radius: 50px;
transition: 0.3s ease;

}
.toggle .top{
    top: 30%;
    left: 50%;
    transform: translate(-50%,-50%);

}
.toggle .mid{
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
}
.toggle .bottom{
    top: 70%;
    left: 50%;
    transform: translate(-50%,-50%);
}
input:checked~ .toggle .top{
    left: 2px;
    top: 14px;
    width: 25px;
    transform: rotate(45deg);
}
input:checked~ .toggle .bottom{
    left: 2px;
    top: 14px;
    width: 25px;
    transform: rotate(-45deg);
}
input:checked~ .toggle .mid{
    opacity: 0;
    transform: translate(-30px);
}
input:checked~.menu{
    margin-top: -15px;
    transform: translateX(-225px);
    box-shadow: 0 0 15px rgba(0, 0,0,0.8);
    visibility: visible;
    
    

}
a{
text-decoration: none;
cursor: pointer;

}
.lab{
    visibility: visible;
}
.navbar{display: flex;
flex-direction: column;}
.nav-icons{
    visibility: hidden;
    padding: 0;
}
.navbar img{
    width: 40px;
    height: 40px;
}
.d{
    margin-right:180px;
}
.navbar a{
    text-decoration: none;
}

}
    </style>
</head>
<body>
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
      <h2>Verify the OTP</h2>
      <a href="index.html"><img src="image.png" alt=""></a>
      <form method="POST">
  <input type="text" name="otp" placeholder="Enter OTP" required>
  <button class="a" type="submit">Verify OTP</button>
</form>
<h4>check your mail for OTP</h4>

<!-- PLACE MESSAGE BELOW -->
<?php if (!empty($message)): ?>
    <div style="margin-top: 15px; text-align: center; color: <?= strpos($message, '✅') === 0 ? 'green' : 'red' ?>;">
        <?= $message ?>
    </div>
<?php endif; ?>

    </div>
    </div>
   

  <footer class="footer">
    <p>© 2025 WalletApp. All rights reserved.</p>
  </footer>
  <script src="app.js"></script>
</body>
</html>
