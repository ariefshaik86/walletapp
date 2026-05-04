<?php
require '../includes/auth.php';
require '../includes/db.php';
$user = $_SESSION['user'];
$name = $user['name']; 
$wallet=$user['wallet'];
$email=$user['email'];
$pendingStmt = $pdo->prepare("SELECT * FROM submissions WHERE email=:email AND status='pending'");
$pendingStmt->execute(['email' => $email]);
$pendingApprovals = $pendingStmt->fetchAll();


// Fetch completed approvals
$completedStmt =  $pdo->prepare("SELECT * FROM submissions WHERE email=:email AND status='approved'");
$completedStmt->execute(['email' => $email]);
$completedApprovals = $completedStmt->fetchAll();

$rejectedStmt =  $pdo->prepare("SELECT * FROM submissions WHERE email=:email AND status='rejected'");
$rejectedStmt->execute(['email' => $email]);
$rejectedApprovals = $rejectedStmt->fetchAll();
// e.g. "john"
$firstName = explode(' ', $user['name'])[1];
$firstLetter = strtoupper($firstName[0]); // "J"
$stmt = $pdo->prepare("SELECT COUNT(status) AS total FROM submissions WHERE email=:email AND status='pending'");
$stmt->execute(['email' => $email]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$mt = $pdo->prepare("SELECT COUNT(status) AS total FROM submissions WHERE email=:email AND status='approved'");
$mt->execute(['email' => $email]);
$complete = $mt->fetch(PDO::FETCH_ASSOC);

$tmt = $pdo->prepare("
    SELECT COUNT(*) AS total 
    FROM tasks 
    WHERE id NOT IN (
        SELECT task_id FROM submissions 
        WHERE email = :email AND status = 'approved'
    )
");
$tmt->execute(['email' => $email]);
$count = $tmt->fetch();

$availableCount = $count['total'];
$availableCount = $count['total'];
$stmt = $pdo->prepare("
  SELECT COUNT(*) AS unread_count 
  FROM submissions 
  WHERE email = ? AND seen = 0 AND status != 'pending'
");
$stmt->execute([$email]);
$unreadCount = $stmt->fetch()['unread_count'];




// Fetch from submissions (task notifications)
$stmt1 = $pdo->prepare("
  SELECT id, task_title AS title, status, rejection_reason AS reason, seen, submitted_at AS created_at, 'task' AS type
  FROM submissions
  WHERE email = ? AND status != 'pending'
");
$stmt1->execute([$email]);
$taskNotes = $stmt1->fetchAll();

// Fetch from withdrawals
$stmt2 = $pdo->prepare("
  SELECT id, CONCAT('Withdrawal of ₹', amount, ' via ', method) AS title, status, reason, seen, requested_at AS created_at, 'withdrawal' AS type
  FROM withdrawals
  WHERE email = ? AND status IN ('approved', 'rejected')
");
$stmt2->execute([$email]);
$withdrawNotes = $stmt2->fetchAll();

// Merge and sort by latest first
$allNotes = array_merge($taskNotes, $withdrawNotes);
usort($allNotes, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));







?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#4a6bff">
    <meta name="description" content="EarnMore - Complete tasks and earn money">
    <title>EarnMore - Dashboard</title>
    <style>
      /* Base Styles */
:root {
  --primary: #4a6bff;
  --primary-light: rgba(74, 107, 255, 0.1);
  --primary-dark: #3a5bff;
  --secondary: #6c5ce7;
  --accent: #00cec9;
  --success: #00b894;
  --warning: #fdcb6e;
  --danger: #ff7675;
  --dark: #2d3436;
  --gray: #636e72;
  --light-gray: #dfe6e9;
  --lighter-gray: #f1f3f5;
  --white: #ffffff;
  --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
  --gradient-hover: linear-gradient(135deg, var(--primary-dark), #5d4ae0);
  --shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  --shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.1);
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  --border-radius: 12px;
  --border-radius-sm: 8px;
  --sidebar-width: 260px;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  background-color: #f8fafc;
  color: var(--dark);
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
  overflow-x: hidden;
}

/* App Container */
.app-container {
  display: flex;
  min-height: 100vh;
  position: relative;
}

/* Sidebar */
.sidebar {
  width: var(--sidebar-width);
  background: var(--white);
  box-shadow: 2px 0 15px rgba(0, 0, 0, 0.03);
  display: flex;
  flex-direction: column;
  position: fixed;
  height: 100vh;
  z-index: 100;
  transition: var(--transition);
}

.logo-container {
  display: flex;
  align-items: center;
  padding: 25px 20px;
  text-decoration: none;
}

.logo-img {
  height: 36px;
  margin-right: 12px;
}

.logo {
  font-size: 20px;
  font-weight: 700;
  color: var(--dark);
  background: var(--gradient);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
}

.nav-menu {
  flex: 1;
  padding: 20px 0;
  overflow-y: auto;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  color: var(--gray);
  text-decoration: none;
  font-weight: 500;
  transition: var(--transition);
  margin: 4px 10px;
  border-radius: var(--border-radius-sm);
}

.nav-item i {
  font-size: 20px;
  width: 24px;
  margin-right: 12px;
  text-align: center;
}

.nav-item:hover {
  color: var(--primary);
  background-color: var(--primary-light);
}

.nav-item.active {
  
  font-weight: 600;
}

.user-profile {
  padding: 20px;
  border-top: 1px solid var(--light-gray);
  display: flex;
  align-items: center;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--gradient);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 20px;
  margin-right: 12px;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-weight: 600;
  color: var(--dark);
  font-size: 14px;
}

.user-email {
  font-size: 12px;
  color: var(--gray);
  margin-top: 2px;
}

/* Main Content */
.main-content {
  flex: 1;
  margin-left: var(--sidebar-width);
  padding: 20px 30px;
  transition: var(--transition);
}

/* Top Bar */
.top-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 0;
  margin-bottom: 20px;
}

.search-bar {
  position: relative;
  max-width: 400px;
  width: 100%;
}

.search-bar i {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--gray);
}

.search-bar input {
  width: 100%;
  padding: 12px 20px 12px 45px;
  border: 1px solid var(--light-gray);
  border-radius: 30px;
  font-size: 14px;
  transition: var(--transition);
  background-color: var(--white);
}

.search-bar input:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.1);
}

.top-bar-actions {
  display: flex;
  align-items: center;
  gap: 15px;
}

.notification-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background: var(--white);
  color: var(--gray);
  font-size: 18px;
  cursor: pointer;
  position: relative;
  transition: var(--transition);
  display: flex;
  align-items: center;
  justify-content: center;
}

.notification-btn:hover {
  background: var(--lighter-gray);
  color: var(--primary);
}

.notification-badge {
  position: absolute;
  top: 5px;
  right: 5px;
  background: var(--danger);
  color: white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  font-size: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
}

.profile-btn {
  display: flex;
  align-items: center;
  background: none;
  border: none;
  cursor: pointer;
  padding: 5px;
  border-radius: 30px;
  transition: var(--transition);
}

.profile-btn:hover {
  background: var(--lighter-gray);
}

.user-greeting {
  margin-right: 10px;
  font-weight: 500;
  font-size: 14px;
  color: var(--dark);
}

.avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--gradient);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  font-size: 14px;
}

/* Welcome Section */
.welcome-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.welcome-content h1 {
  font-size: 28px;
  color: var(--dark);
  margin-bottom: 8px;
}

.welcome-content p {
  color: var(--gray);
  font-size: 15px;
}

.quick-actions {
  display: flex;
  gap: 12px;
}

.quick-action-btn {
  display: flex;
  align-items: center;
  padding: 10px 20px;
  border: 1px solid var(--light-gray);
  border-radius: 30px;
  background: var(--white);
  color: var(--dark);
  font-weight: 500;
  font-size: 14px;
  cursor: pointer;
  transition: var(--transition);
}

.quick-action-btn i {
  margin-right: 8px;
  font-size: 14px;
}

.quick-action-btn.primary {
  background: var(--gradient);
  color: white;
  border: none;
  box-shadow: 0 4px 15px rgba(74, 107, 255, 0.3);
}

.quick-action-btn.primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(74, 107, 255, 0.4);
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: var(--white);
  border-radius: var(--border-radius);
  padding: 25px;
  box-shadow: var(--shadow);
  transition: var(--transition);
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.03);
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: var(--shadow-hover);
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: var(--gradient);
}

.stat-card.balance::before { background: linear-gradient(90deg, #4a6bff, #6c5ce7); }
.stat-card.earnings::before { background: linear-gradient(90deg, #00b894, #00cec9); }
.stat-card.tasks::before { background: linear-gradient(90deg, #fdcb6e, #ff9f43); }
.stat-card.referrals::before { background: linear-gradient(90deg, #a55eea, #d1b3ff); }

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  color: white;
  margin-bottom: 15px;
  background: var(--gradient);
}

.stat-card.balance .stat-icon { background: linear-gradient(135deg, #4a6bff, #6c5ce7); }
.stat-card.earnings .stat-icon { background: linear-gradient(135deg, #00b894, #00cec9); }
.stat-card.tasks .stat-icon { background: linear-gradient(135deg, #fdcb6e, #ff9f43); }
.stat-card.referrals .stat-icon { background: linear-gradient(135deg, #a55eea, #d1b3ff); }

.stat-label {
  display: block;
  color: var(--gray);
  font-size: 14px;
  margin-bottom: 5px;
}

.stat-value {
  display: block;
  font-size: 24px;
  font-weight: 700;
  color: var(--dark);
  margin-bottom: 15px;
}

.stat-link {
  color: var(--primary);
  text-decoration: none;
  font-size: 13px;
  font-weight: 500;
  display: flex;
  align-items: center;
  transition: var(--transition);
}

.stat-link i {
  margin-left: 5px;
  font-size: 10px;
  transition: var(--transition);
}

.stat-link:hover {
  color: var(--primary-dark);
}

.stat-link:hover i {
  transform: translateX(3px);
}

/* Tasks Overview */
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h2 {
  font-size: 20px;
  color: var(--dark);
  font-weight: 600;
}

.view-all {
  color: var(--primary);
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  display: flex;
  align-items: center;
  transition: var(--transition);
}

.view-all i {
  margin-left: 5px;
  font-size: 10px;
  transition: var(--transition);
}

.view-all:hover {
  color: var(--primary-dark);
}

.view-all:hover i {
  transform: translateX(3px);
}

.task-cards {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.task-card {
  background: var(--white);
  border-radius: var(--border-radius);
  padding: 25px;
  box-shadow: var(--shadow);
  transition: var(--transition);
  border-left: 4px solid var(--primary);
}

.task-card.available { border-left-color: #4a6bff; }
.task-card.pending { border-left-color: #fdcb6e; }
.task-card.completed { border-left-color: #00b894; }

.task-card-header {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}

.task-card-header i {
  font-size: 20px;
  margin-right: 12px;
  color: inherit;
}

.task-card.available .task-card-header { color: #4a6bff; }
.task-card.pending .task-card-header { color: #f39c12; }
.task-card.completed .task-card-header { color: #00b894; }

.task-card h3 {
  font-size: 16px;
  font-weight: 600;
  margin: 0;
  flex: 1;
}

.task-count {
  background: rgba(0, 0, 0, 0.05);
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.task-card p {
  color: var(--gray);
  font-size: 14px;
  margin-bottom: 20px;
}

.task-card-btn {
  display: inline-block;
  padding: 8px 16px;
  background: rgba(74, 107, 255, 0.1);
  color: var(--primary);
  border-radius: 20px;
  font-size: 13px;
  font-weight: 500;
  text-decoration: none;
  transition: var(--transition);
}

.task-card-btn:hover {
  background: rgba(74, 107, 255, 0.2);
}

.task-list {
  list-style: none;
  margin: 0;
  padding: 0;
}

.task-list li {
  padding: 8px 0;
  border-bottom: 1px solid var(--light-gray);
  display: flex;
  align-items: center;
  font-size: 14px;
  color: var(--dark);
}

.task-list li:last-child {
  border-bottom: none;
}

.task-list li::before {
  content: '•';
  margin-right: 8px;
  color: var(--gray);
}

/* Recent Activity */
.activity-list {
  background: var(--white);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  overflow: hidden;
}

.activity-item {
  display: flex;
  align-items: center;
  padding: 18px 25px;
  border-bottom: 1px solid var(--light-gray);
  transition: var(--transition);
}

.activity-item:last-child {
  border-bottom: none;
}

.activity-item:hover {
  background: var(--lighter-gray);
}

.activity-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 15px;
  font-size: 16px;
  color: white;
}

.activity-icon.success { background-color: #00b894; }
.activity-icon.warning { background-color: #fdcb6e; }
.activity-icon.danger { background-color: #ff7675; }

.activity-details {
  flex: 1;
}

.activity-details h4 {
  font-size: 15px;
  color: var(--dark);
  margin-bottom: 3px;
}

.activity-details p {
  font-size: 13px;
  color: var(--gray);
  margin-bottom: 3px;
}

.activity-time {
  font-size: 12px;
  color: var(--gray);
}

.activity-amount {
  font-weight: 600;
  color: #00b894;
}

.activity-amount.pending {
  color: #f39c12;
}

/* Mobile Navigation */
.mobile-nav {
  display: none;
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: var(--white);
  box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.05);
  z-index: 90;
  padding: 10px 0;
}

.mobile-nav .nav-item {
  flex-direction: column;
  text-align: center;
  padding: 8px 5px;
  font-size: 10px;
  color: var(--gray);
  text-decoration: none;
  border-radius: 0;
  margin: 0;
  flex: 1;
}

.mobile-nav .nav-item i {
  font-size: 18px;
  margin: 0 0 4px 0;
  display: block;
}

.mobile-nav .nav-item.wallet-btn {
  transform: translateY(-10px);
}

.mobile-nav .wallet-btn i {
  background: var(--gradient);
  color: white;
  width: 40px;
  height: 40px;
  line-height: 40px;
  border-radius: 50%;
  margin: -25px auto 5px;
  box-shadow: 0 4px 15px rgba(74, 107, 255, 0.3);
}

/* Responsive Styles */
@media (max-width: 1200px) {
  .sidebar {
    transform: translateX(-100%);
  }
  
  .sidebar.active {
    transform: translateX(0);
  }
  
  .main-content {
    margin-left: 0;
    padding: 20px 15px 80px;
  }
  
  .mobile-nav {
    display: flex;
  }
  
  .welcome-section {
    flex-direction: column;
    align-items: flex-start;
  }
  
  .quick-actions {
    margin-top: 15px;
    width: 100%;
    justify-content: flex-start;
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr 1fr;
  }
  
  .task-cards {
    grid-template-columns: 1fr;
  }
  
  .welcome-content h1 {
    font-size: 24px;
  }
}

@media (max-width: 480px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .quick-actions {
    flex-direction: column;
    gap: 10px;
  }
  
  .quick-action-btn {
    width: 100%;
    justify-content: center;
  }
}
.blurred {
    filter: blur(4px);
    pointer-events: none;
    user-select: none;
}
#searchResults a.task-card {
  padding: 12px 16px;
  background: #f8f8f8;
  margin-bottom: 10px;
  border-radius: 8px;
  transition: background 0.2s ease, transform 0.2s ease;
  cursor: pointer;
}

#searchResults a.task-card:hover {
  background: #e6e6e6;
  transform: scale(1.01);
}

#searchResults .task-title {
  font-weight: bold;
  color: #222;
  font-size: 16px;
  margin-bottom: 4px;
}

#searchResults .task-desc {
  font-size: 14px;
  color: #555;
}
/* Dropdown container */
.dropdown-card {
  display: none;
  position: absolute;
  top: 100%;
  right: 0;
  background: #ffffff;
  border: 1px solid #ddd;
  border-radius: 10px;
  padding: 10px 0;
  min-width: 160px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  z-index: 999;
}

/* Dropdown links */
.dropdown-item {
  display: flex;
  align-items: center;
  padding: 10px 15px;
  text-decoration: none;
  color: #333;
  transition: background 0.2s ease;
  font-size: 15px;
}

.dropdown-item .icon {
  margin-right: 10px;
  font-size: 16px;
}

/* Hover effect */
.dropdown-item:hover {
  background-color: #f0f0f0;
  border-radius: 5px;
}
.notification {
  padding: 12px;
  border-radius: 12px;
  margin-bottom: 10px;
  background: #f2f2f2;
  cursor: pointer;
  transition: all 0.3s ease;
}
.notification.unread {
  background-color: #e0f7fa;
  font-weight: bold;
}
.notification.read {
  background-color: #f8f9fa;
}
.modal {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background-color: rgba(0, 0, 0, 0.6);
  justify-content: center; align-items: center;
}
.modal-content {
  background: white;
  padding: 20px;
  border-radius: 10px;
}
.blurred {
  filter: blur(5px);
}



      </style>
    
    <!-- Favicon -->
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="img/icon-192x192.png">
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    <link rel="stylesheet" href="home.css">
    
    <!-- PWA Support -->
    <link rel="manifest" href="manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="EarnMore">
</head>
<body>
  <div class="app-container">
    <!-- Sidebar Navigation -->
    <aside class="sidebar">
      <div class="logo-container">
        <img src="../image.png" alt="EarnMore Logo" class="logo-img">
        <span class="logo">WalletApp</span>
      </div>
      <nav class="nav-menu">
        <a href="#" class="nav-item ">
          <i class="fas fa-home"></i>
          <span>Dashboard</span>
        </a>
        <a href="tasks.php" class="nav-item">
          <i class="fas fa-tasks"></i>
          <span>Tasks</span>
        </a>
        <a href="wallet.php" class="nav-item">
          <i class="fas fa-wallet"></i>
          <span>Wallet</span>
        </a>
        <a href="withdraw.php" class="nav-item">
          <i class="fas fa-money-bill-wave"></i>
          <span>Withdraw</span>
        </a>
        <a href="transcations.php" class="nav-item">
          <i class="fas fa-exchange-alt"></i>
          <span>Transactions</span>
        </a>
        <a href="profile.php" class="nav-item">
          <i class="fas fa-user"></i>
          <span>Profile</span>
        </a>
      </nav>
      <div class="user-profile">
        <div class="user-avatar">
          <i class="fas fa-user-circle"></i>
        </div>
        <div class="user-info">
          <span class="user-name" id="userName"><?= htmlspecialchars($name)?></span>
          <span class="user-email" id="userEmail"><?= htmlspecialchars($email)?></span>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Top Bar -->
      <header class="top-bar">
        <div class="search-bar">
          <i class="fas fa-search"></i>
          <input id="searchInput" type="text" placeholder="Search for tasks...">  </div>
        
        <div class="top-bar-actions">
          <a href="notifications.php"class="notification-btn">
            <i class="far fa-bell"></i>
            <span class="notification-badge"></span>
</a>
         <!-- Profile Button with Dropdown -->
<!-- Profile Dropdown Wrapper -->
<div class="profile-menu-wrapper" style="position: relative;">
  <!-- Profile Button -->
  <button class="profile-btn" id="profileDropdown" style="display: flex; align-items: center; gap: 10px; background: none; border: none; cursor: pointer;">
    <p class="user-greeting" style="margin: 0; font-weight: bold;">Hi, <?= htmlspecialchars($firstName) ?></p>
    <div class="avatar" style="background: #007bff; color: white; border-radius: 50%; padding: 8px 12px; font-weight: bold;">
      <?= htmlspecialchars($firstLetter) ?>
    </div>
  </button>

  <!-- Dropdown Menu -->
  <div id="dropdownMenu" class="dropdown-card">
    <a href="profile.php" class="dropdown-item">
      <span class="icon">👤</span> Profile
    </a>
    <a href="../logout.php" class="dropdown-item">
      <span class="icon">❌</span> Logout
    </a>
  </div>
</div>

      

        </div>
      </header>
      <div id="searchResults"
     style="position: absolute; top: 80px; left: 20px; width: 400px; max-height: 300px;
     overflow-y: auto; background: white; border: 1px solid #ccc; padding: 10px;
     box-shadow: 0 4px 8px rgba(0,0,0,0.2); display: none; z-index: 1001;">
</div>
<div id="notification-modal" class="modal">
  <div class="modal-content">
    <p id="notification-detail-text"></p>
    <button onclick="closeModal()">Close</button>
  </div>
</div>

<div id="dashboardContent">
    <div id="notifications">
  <?php if (empty($allNotes)): ?>
    <p>No notifications found.</p>
  <?php else: ?>
    <?php foreach ($allNotes as $note): ?>
      <?php
        $id = $note['id'];
        $type = $note['type']; // 'task' or 'withdrawal'
        $status = $note['status'];
        $isUnread = $note['seen'];
        $title = htmlspecialchars($note['title']);
        $reason = htmlspecialchars($note['reason']);
        $datetime = date('d M Y, h:i A', strtotime($note['created_at']));

        $message = $status === 'approved'
          ? "✅ $title has been approved."
          : "❌ $title was rejected. Reason: $reason";
      ?>
      <div class="notification <?= $isUnread ? 'unread' : 'read' ?>"
     onclick="showNotificationDetail(`<?= addslashes($message) ?>`, <?= $id ?>, this, '<?= $type ?>')">
  <div><?= $message ?></div>
  <small><?= $datetime ?></small>
</div>

    <?php endforeach; ?>
  <?php endif; ?>
</div>


      <!-- Welcome Section -->
</div>
    </main>
  </div>

  <!-- Bottom Navigation for Mobile -->
  <nav class="mobile-nav">
    <a href="user_dashboard.php" class="nav-item active">
      <i class="fas fa-home"></i>
      <span>Home</span>
    </a>
    <a href="tasks.php" class="nav-item">
      <i class="fas fa-tasks"></i>
      <span>Tasks</span>
    </a>
    <a href="wallet.php" class="nav-item wallet-btn">
      <i class="fas fa-wallet"></i>
      <span>Wallet</span>
    </a>
    <a href="withdraw.php" class="nav-item">
      <i class="fas fa-money-bill-wave"></i>
      <span>Withdraw</span>
    </a>
    <a href="profile.php" class="nav-item">
      <i class="fas fa-user"></i>
      <span>Profile</span>
    </a>
  </nav>
   </div>
   <script>
    

function showNotificationDetail(message, id, element, type) {
  // Show notification detail modal (if you have one)
  document.getElementById('notification-detail-text').innerText = message;
  document.getElementById('notification-modal').style.display = 'flex';
  document.getElementById('page-content').classList.add('blurred');

  // Send fetch request to mark the notification as read
  fetch('mark_notification_read.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `id=${id}&type=${type}`
  })
  .then(response => response.text())
  .then(data => {
    if (data.trim() === 'success') {
      // Update visual state
      element.classList.remove('unread');
      element.classList.add('read');
    } else {
      console.error('Failed to mark notification as read:', data);
    }
  })
  .catch(error => {
    console.error('Error:', error);
  });
}

function closeModal() {
  document.getElementById('notification-modal').style.display = 'none';
  document.getElementById('page-content').classList.remove('blurred');
}






const profileBtn = document.getElementById('profileDropdown');
const dropdown = document.getElementById('dropdownMenu');

// Toggle dropdown
profileBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
});

// Hide on outside click
document.addEventListener('click', function () {
    dropdown.style.display = 'none';
});


const input = document.getElementById('searchInput');
const resultsBox = document.getElementById('searchResults');
const dashboard = document.getElementById('dashboardContent');

input.addEventListener('keyup', function () {
    const query = input.value.trim();

    if (query === "") {
        resultsBox.style.display = "none";
        resultsBox.innerHTML = "";
        dashboard.classList.remove("blurred");
        return;
    }

    fetch('search.php?q=' + encodeURIComponent(query))
        .then(res => res.text())
        .then(data => {
            resultsBox.innerHTML = data;
            resultsBox.style.display = "block";
            dashboard.classList.add("blurred");
        });
});

// Hide and unblur on outside click
document.addEventListener('click', function (e) {
    if (!resultsBox.contains(e.target) && e.target !== input) {
        resultsBox.style.display = "none";
        dashboard.classList.remove("blurred");
    }
});
</script>


  <!-- Scripts -->
  <script src="auth.js"></script>
  <script src="home.js"></script>
  
  <!-- PWA Service Worker -->
  
</body>
</html>
