<?php
require 'includes/db.php';

// Fetch all submissions ordered by newest first
$stmt = $pdo->query("SELECT * FROM submissions ORDER BY submitted_at DESC");
$submissions = $stmt->fetchAll();
$stmt = $pdo->query("SELECT * FROM withdrawals ORDER BY requested_at DESC");
$withdrawals = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Panel - Task Submissions</title>
  <style>
    
.button {
  display: inline-block;
  padding: 10px 18px;
  font-size: 15px;
  font-weight: 600;
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s ease;
  margin: 5px;
  box-shadow: 0 3px 8px rgba(0,0,0,0.08);
}

.button.approve {
  background-color: #4f46e5; /* Indigo */
  color: white;
}

.button.approve:hover {
  background-color: #3730a3;
}

.button.logout {
  background-color: #ef4444; /* Red */
  color: white;
}

.button.logout:hover {
  background-color: #b91c1c;
}


    table { border-collapse: collapse; width: 100%; }
    th, td { padding: 10px; border: 1px solid #ccc; }
    th { background: #eee; }
    button { padding: 5px 10px; border: none; color: white; border-radius: 3px; cursor: pointer; }
    .approve { background-color: green; }
    .reject { background-color: red; }
    .withdraw-heading {
  font-size: 1.8rem;
  margin-bottom: 20px;
  text-align: center;
  color: #333;
}

.table-wrapper {
  overflow-x: auto;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  border-radius: 12px;
  background: #fff;
  padding: 10px;
}

.withdrawal-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95rem;
  min-width: 800px;
}

.withdrawal-table th, .withdrawal-table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.withdrawal-table th {
  background-color: #f5f5f5;
  font-weight: 600;
  color: #444;
}

.withdrawal-table tbody tr:hover {
  background-color: #f0f8ff;
}

.highlight {
  background: #fffde7;
}

.btn {
  display: inline-block;
  padding: 6px 10px;
  font-size: 14px;
  border-radius: 6px;
  color: #fff;
  text-decoration: none;
  margin-right: 4px;
}

.btn.approve {
  background: #4CAF50;
}

.btn.reject {
  background: #E53935;
}

.badge {
  padding: 5px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: bold;
  text-transform: capitalize;
}

.badge.pending {
  background: #fff3cd;
  color: #856404;
}

.badge.approved {
  background: #d4edda;
  color: #155724;
}

.badge.rejected {
  background: #f8d7da;
  color: #721c24;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
  .withdraw-heading {
    font-size: 1.5rem;
  }

  .withdrawal-table {
    font-size: 0.85rem;
  }

  .btn {
    font-size: 12px;
    padding: 5px 8px;
  }

  .badge {
    font-size: 11px;
  }
}

  </style>
</head>
<body>
  <h1>Admin - Task Completion Requests</h1>

  <table>
    <tr>
      <th>Email</th>
      <th>Task Title</th>
      <th>Screenshot</th>
      <th>Status</th>
      <th>Reward</th>
      <th>Actions</th>
    </tr>

    <?php foreach ($submissions as $sub): ?>
      <tr>
        <td><?= htmlspecialchars($sub['email']) ?></td>
        <td><?= htmlspecialchars($sub['task_title']) ?></td>
        <td><a href="<?= htmlspecialchars($sub['file_path']) ?>" target="_blank">View</a></td>
        <td><?= htmlspecialchars(ucfirst($sub['status'])) ?></td>
        <td><?= htmlspecialchars($sub['reward']) ?></td>
        <td>
          <?php if ($sub['status'] === 'pending'): ?>
            <form action="approve.php" method="POST" style="display:inline;">
              <input type="hidden" name="id" value="<?= $sub['id'] ?>">
              <input type="hidden" name="status" value="approved">
              <button type="submit" class="approve">Approve</button>
            </form>

            <form action="approve.php" method="POST" style="display:inline;">
              <input type="hidden" name="id" value="<?= $sub['id'] ?>">
              <input type="hidden" name="status" value="rejected">
              <input name="reason" placeholder="enter the reason.." required>
              <button type="submit" class="reject">Reject</button>
            </form>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
  <h2 class="withdraw-heading">🧾 Withdrawal Requests</h2>

<div class="table-wrapper">
  <table class="withdrawal-table">
    <thead>
      <tr>
        <th>Email</th>
        <th>Method</th>
        <th>Details</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Requested At</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($withdrawals as $row): ?>
        <tr class="<?= $row['status'] === 'pending' ? 'highlight' : '' ?>">
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= strtoupper($row['method']) ?></td>
          <td><?= htmlspecialchars($row['details']) ?></td>
          <td>₹<?= (int)$row['amount'] ?>.00</td>
          <td><span class="badge <?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
          <td><?= date('d M Y, h:i A', strtotime($row['requested_at'])) ?></td>
          <td>
            <?php if ($row['status'] === 'pending'): ?>
               <form action="dashboard/approve_withdrawal.php" method="POST" style="display:inline;">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <input type="hidden" name="status" value="approved">
              <button type="submit" class="approve">Approve</button>
            </form>

            <form action="dashboard/approve_withdrawal.php" method="POST" style="display:inline;">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <input type="hidden" name="status" value="rejected">
              <input name="reason" placeholder="enter the reason.." required>
              <button type="submit" class="reject">Reject</button>
            </form>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>


  <br><br>
  <a class="button approve" href="add_task.php">➕ Add Task</a>
<a class="button logout" href="admin_logout.php">🚪 Log out</a>

</body>
</html>
