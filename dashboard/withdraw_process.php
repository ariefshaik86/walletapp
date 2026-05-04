<?php
session_start();
require '../includes/db.php';

$email = $_SESSION['email'];

// If method is NOT set, save account details
if (!isset($_POST['method'])) {
  $method = !empty($_POST['upi_id']) ? 'upi' : 'bank';

  if ($method === 'upi') {
    $upi = $_POST['upi_id'];
    $stmt = $pdo->prepare("UPDATE users SET withdrawal_method=?, upi_id=? WHERE email=?");
    $stmt->execute([$method, $upi, $email]);
  } else {
    $stmt = $pdo->prepare("UPDATE users SET withdrawal_method=?, account_holder=?, bank_name=?, account_number=?, ifsc_code=? WHERE email=?");
    $stmt->execute([
      'bank',
      $_POST['account_holder'],
      $_POST['bank_name'],
      $_POST['account_number'],
      $_POST['ifsc_code'],
      $email
    ]);
  }

  echo "<script>alert('✅ Account details saved! Now enter amount to withdraw.'); window.location.href='withdraw.php';</script>";
  exit;
}

// Else process withdrawal


if (!isset($_SESSION['email'])) {
    die("User not logged in.");
}

$email = $_SESSION['email'];

// Check if amount and method are set
if (!isset($_POST['amount'], $_POST['method'])) {
    die("Invalid form submission.");
}

$amount = (int)$_POST['amount'];
$method = $_POST['method'];

// Validate amount
if ($amount <= 0) {
    die("Invalid amount.");
}

// Fetch user to get account/UPI details
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

// Compose withdrawal details based on method
$details = ($method === 'upi')
    ? 'UPI: ' . $user['upi_id']
    : "Bank: {$user['bank_name']}, Acc: {$user['account_number']}, IFSC: {$user['ifsc_code']}";

// Insert withdrawal request
$stmt = $pdo->prepare("INSERT INTO withdrawals (email, method, details, amount, status, requested_at)
                       VALUES (?, ?, ?, ?, 'pending', NOW())");

$success = $stmt->execute([$email, $method, $details, $amount]);

if ($success) {
    echo "<script>alert('✅ Withdrawal request submitted!'); window.location.href='user_dashboard.php';</script>";
} else {
    echo "<script>alert('❌ Something went wrong. Please try again.');</script>";
}
?>
