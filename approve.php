<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? '';
    $reason = $_POST['reason'] ?? '';

    if (!$id || !$status) {
        header("Location: admin_dashboard.php?error=Invalid+Request");
        exit;
    }

    try {
        // Start transaction
        $pdo->beginTransaction();

        if ($status === 'approved') {
            // Update status first
            $stmt = $pdo->prepare("UPDATE submissions SET status = 'approved', seen = 0 WHERE id = ?");
            $stmt->execute([$id]);

            // Fetch user's email and reward
            $stmt = $pdo->prepare("SELECT email, reward FROM submissions WHERE id = ?");
            $stmt->execute([$id]);
            $submission = $stmt->fetch();

            if ($submission) {
                $email = $submission['email'];
                $reward = (float)$submission['reward'];

                // Update wallet
                $update = $pdo->prepare("UPDATE users SET wallet = wallet + ? WHERE email = ?");
                $update->execute([$reward, $email]);
            }

        } elseif ($status === 'rejected') {
            // Update with rejection reason
            $stmt = $pdo->prepare("UPDATE submissions SET status = 'rejected', rejection_reason = ?, seen = 0 WHERE id = ?");
            $stmt->execute([$reason, $id]);
        }

        // Commit transaction
        $pdo->commit();

        // Redirect on success
        header("Location: admin_dashboard.php?success=Submission+updated");
        exit;

    } catch (PDOException $e) {
        // Roll back if failed
        $pdo->rollBack();
        header("Location: admin_dashboard.php?error=Database+Error");
        exit;
    }
}
?>
