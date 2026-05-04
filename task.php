<?php
require 'includes/auth.php';
require 'includes/db.php';

$taskId = $_GET['id'] ?? null;

if (!$taskId) die("Task ID missing.");

$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$taskId]);
$task = $stmt->fetch();

if (!$task) die("Task not found.");
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
         .task-container {
      max-width: 600px;
      margin: 0 auto;
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .hid{
      outline:none;
      border:none;

    }
    h2 {
      margin-top: 0;
      color: #333;
    }

    .task-details p {
      line-height: 1.6;
      margin: 10px 0;
    }

    .task-details a.install-link {
      display: inline-block;
      margin-top: 10px;
      background: #4a6bff;
      color: #fff;
      text-decoration: none;
      padding: 10px 15px;
      border-radius: 5px;
    }
    .modern-input {
      width: 100%;
      max-width: 300px;
      padding: 12px 16px;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      background-color: #f2f2f2;
      box-shadow: inset 0 0 0 1px #ccc;
      transition: box-shadow 0.3s, background-color 0.3s;
      outline: none;
    }

    .modern-input:focus {
      box-shadow: 0 0 0 2px #4f46e5; /* modern blue glow */
      background-color: #fff;
    }

    .modern-input::placeholder {
      color: #888;
    }

    .upload-form {
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #ccc;
      display:flex;
      flex-direction:column;

    }
    .upload-form form{
      display:flex;
      flex-direction:column;
      gap:10px

    }

    .upload-form input[type="file"] {
      display: block;
      margin: 10px 0 20px;
    }

    .upload-form button {
      background: #4a6bff;
      color: white;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      border-radius: 4px;
      cursor: pointer;
    }

    .upload-form button:hover {
      background: #218838;
    }

    .message {
      margin-top: 15px;
      color: green;
      font-weight: bold;
    }
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
  color: var(--primary);
  background-color: var(--primary-light);
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
        :root {
            --primary: #4a6bff;
            --primary-light: #7b91ff;
            --primary-dark: #3a56d4;
            --success: #00b894;
            --danger: #ff7675;
            --dark: #2d3436;
            --light: #f8f9fa;
            --white: #ffffff;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-400: #ced4da;
            --gray-500: #adb5bd;
            --gray-600: #6c757d;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        header {
            background: var(--white);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 500px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-light);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        /* Balance Card */
        .balance-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: var(--border-radius);
            padding: 25px;
            margin: 20px 0;
            box-shadow: var(--shadow);
            text-align: center;
        }

        .balance-card h2 {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .balance-amount {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }

        .balance-card p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Action Buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 25px 0;
        }

        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: var(--border-radius);
            border: none;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-outline {
            background: white;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Transactions */
        .transactions {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .section-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .view-all {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .transactions-list {
            margin-top: 10px;
        }

        .transaction-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--gray-200);
        }

        .transaction-item:last-child {
            border-bottom: none;
        }

        .transaction-info h4 {
            font-size: 0.95rem;
            margin-bottom: 4px;
        }

        .transaction-info p {
            font-size: 0.8rem;
            color: var(--gray-600);
        }

        .transaction-amount {
            font-weight: 600;
            text-align: right;
        }

        .income {
            color: var(--success);
        }

        .expense {
            color: var(--danger);
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: var(--gray-500);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        .modal-content {
            background: white;
            width: 100%;
            max-width: 400px;
            border-radius: var(--border-radius);
            padding: 25px;
            position: relative;
            animation: modalFadeIn 0.3s ease;
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray-500);
            background: none;
            border: none;
            padding: 5px;
        }

        .modal h2 {
            margin-bottom: 20px;
            color: var(--dark);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--gray-300);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: border-color 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.2);
        }

        .text-muted {
            color: var(--gray-500);
            font-size: 0.8rem;
            display: block;
            margin-top: 5px;
        }

        .btn-block {
            width: 100%;
            margin-top: 10px;
        }

        /* Payment Options */
        .payment-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .payment-option {
            border: 2px solid var(--gray-300);
            border-radius: var(--border-radius);
            padding: 15px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .payment-option.active {
            border-color: var(--primary);
            background-color: rgba(74, 107, 255, 0.1);
        }

        .payment-option .amount {
            font-weight: 600;
            color: var(--dark);
        }

        .mt-2 {
            margin-top: 8px;
        }

        .mt-3 {
            margin-top: 16px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }

            .balance-amount {
                font-size: 2rem;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }

            .payment-options {
                grid-template-columns: repeat(2, 1fr);
            }
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
        <img src="image.png" alt="EarnMore Logo" class="logo-img">
        <span class="logo">WalletApp</span>
      </div>
      <nav class="nav-menu">
        <a href="dashboard/user_dashboard.php" class="nav-item active">
          <i class="fas fa-home"></i>
          <span>Dashboard</span>
        </a>
        <a href="dashboard/task.php" class="nav-item">
          <i class="fas fa-tasks"></i>
          <span>Tasks</span>
        </a>
        <a href="dashboard/wallet.php" class="nav-item">
          <i class="fas fa-wallet"></i>
          <span>Wallet</span>
        </a>
        <a href="dashboard/withdraw.php" class="nav-item">
          <i class="fas fa-money-bill-wave"></i>
          <span>Withdraw</span>
        </a>
        <a href="dashboard/transactions.php" class="nav-item">
          <i class="fas fa-exchange-alt"></i>
          <span>Transactions</span>
        </a>
        <a href="dashboard/profile.html" class="nav-item">
          <i class="fas fa-user"></i>
          <span>Profile</span>
        </a>
      </nav>
      <div class="user-profile">
        <div class="user-avatar">
          <i class="fas fa-user-circle"></i>
        </div>
        <div class="user-info">
          <span class="user-name" id="userName">Arief</span>
          <span class="user-email" id="userEmail">user@example.com</span>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="task-container">
  <h2><?= htmlspecialchars($task['title']) ?></h2>

  <div class="task-details">
    <p><strong>Description:</strong> <?= htmlspecialchars($task['description']) ?></p>
    <p><strong>Reward:</strong> <?= htmlspecialchars($task['cost']) ?>/-</p>
    <p><a class="install-link" href="<?= htmlspecialchars($task['link']) ?>" target="_blank">Complete Now</a></p>
  </div>

  <div class="upload-form"  id="myDiv">
    <h3>Submit Screenshot for Approval</h3>
    <form action="upload.php" method="POST" enctype="multipart/form-data" onsubmit="runMyFunction()">
      <label>Task Id:</label>
      <input  name="task_id" value="<?= $task['id'] ?>" class="hid"readonly>
      <label>Task Title:</label>
      <input  name="task_title" value="<?= htmlspecialchars($task['title']) ?>" class="hid"readonly>
      Reward you get:
      <input type="text" name="reward" value="<?= htmlspecialchars($task['cost']) ?>/-"class="hid" readonly>
      
      <input class="modern-input"placeholder="enter ur email"type="email" name="email" required>
      <label>Select Screenshot:</label>
      <input type="file" name="screenshot" accept="image/*" required>
      <button type="submit" >Submit for Approval</button>
    </form>
  </div>


    <?php if (isset($_GET['success'])): ?>
      <p class="message">✅ Screenshot submitted successfully. Awaiting admin approval.</p>
      <button type="submit" onclick="runMyFunction()">Show Status</button>


    <?php endif; ?>
</div>

      <!-- Top Bar -->
        <!-- Bottom Navigation for Mobile -->
  <nav class="mobile-nav">
    <a href="dashboard/user_dashboard.php" class="nav-item active">
      <i class="fas fa-home"></i>
      <span>Home</span>
    </a>
    <a href="dashboard/tasks.php" class="nav-item">
      <i class="fas fa-tasks"></i>
      <span>Tasks</span>
    </a>
    <a href="dashboard/wallet.php" class="nav-item wallet-btn">
      <i class="fas fa-wallet"></i>
      <span>Wallet</span>
    </a>
    <a href="dashboard/withdraw.php" class="nav-item">
      <i class="fas fa-money-bill-wave"></i>
      <span>Withdraw</span>
    </a>
    <a href="dashboard/profile.php" class="nav-item">
      <i class="fas fa-user"></i>
      <span>Profile</span>
    </a>
  </nav>

  <!-- Scripts -->
  <script src="auth.js"></script>
  <script src="home.js"></script>
  
  <!-- PWA Service Worker -->
  <script>
    
  // Your custom function
  function runMyFunction() {
    console.log("Function is running...");

    // ✅ Now hide the div
    document.getElementById("myDiv").style.display = "none";
  }
</script>
</body>
</html>
