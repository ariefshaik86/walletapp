<?php
session_start();
require 'includes/db.php';        // connect to your database
require 'send_otp_mail.php';      // sendOTP() function

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];

    // ✅ Step 1: Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo "❌ Email already registered. <a href='login.php'>Login instead</a>";
        exit;
    }

    // ✅ Step 2: Save user info temporarily in session
    $_SESSION['temp_reg'] = [
        'name' => $_POST['name'],
        'email' => $email,
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'referral' => $_POST['referral'] ?? null
    ];

    // ✅ Step 3: Generate and send OTP
    $otp = rand(100000, 999999);
    $_SESSION['reg_otp'] = $otp;
    $_SESSION['reg_otp_expiry'] = time() + 300; // expires in 5 mins

    if (sendOTP($email, $otp)) {
        header("Location: verify_register_otp.php");
        exit;
    } else {
        echo "❌ Failed to send OTP. Try again.";
    }
}
