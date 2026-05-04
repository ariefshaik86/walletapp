<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $reason = $_POST['reason'] ?? '';

    // Get withdrawal record
    $stmt = $pdo->prepare("SELECT * FROM withdrawals WHERE id = ?");
    $stmt->execute([$id]);
    $withdraw = $stmt->fetch();

    if (!$withdraw) {
        die("Invalid withdrawal request.");
    }

    $email = $withdraw['email'];
    $amount = (float)$withdraw['amount'];
    $method = $withdraw['method'];

    // ✅ Minimum withdrawal check
    if ($status === 'approved' && $amount < 100) {
        die("Withdrawal amount must be at least ₹100.");
    }

    $pdo->beginTransaction();
    try {
        if ($status === 'approved') {
            // Deduct amount from user's wallet
            $pdo->prepare("UPDATE users SET wallet = wallet - ? WHERE email = ?")
                ->execute([$amount, $email]);

            $pdo->prepare("UPDATE withdrawals SET status = 'approved', seen = 0 WHERE id = ?")
                ->execute([$id]);

        } elseif ($status === 'rejected') {
            $pdo->prepare("UPDATE withdrawals SET status = 'rejected', reason = ?, seen = 0 WHERE id = ?")
                ->execute([$reason, $id]);
        }

        $pdo->commit();
        header("Location: ../admin_dashboard.php?success");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed: " . $e->getMessage();
    }
}
?>
