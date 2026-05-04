<?php
session_start();
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $link=$_POST['link'];
    $cost=$_POST['cost'];


    $stmt = $pdo->prepare("INSERT INTO tasks (title,description,link,cost) VALUES (?,?,?,?)");
    $stmt->execute([$title, $description, $link, $cost,]);

    header('Location: admin_dashboard.php'); // redirect back after adding
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Task</title>
</head>
<body>
    <style>
/* Basic Reset */
body {
  font-family: 'Segoe UI', sans-serif;
  background: #f7f9fc;
  padding: 20px;
}

/* Form Container */
.task-form {
  background: white;
  padding: 25px 30px;
  border-radius: 12px;
  max-width: 500px;
  margin: 0 auto;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
  animation: fadeIn 0.5s ease-in-out;
}

/* Form Title */
.form-title {
  text-align: center;
  color: #4f46e5;
  margin-bottom: 25px;
  animation: slideDown 0.4s ease-in-out;
}

/* Labels */
.task-form label {
  display: block;
  margin-top: 15px;
  margin-bottom: 5px;
  font-weight: 600;
  color: #333;
}

/* Inputs & Textareas */
.task-form input[type="text"],
.task-form textarea {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  transition: border 0.3s, box-shadow 0.3s;
  resize: vertical;
}

.task-form input:focus,
.task-form textarea:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
  outline: none;
}

/* Submit Button */
.submit-btn {
  margin-top: 20px;
  width: 100%;
  padding: 12px;
  background: #4f46e5;
  color: white;
  font-weight: bold;
  font-size: 15px;
  border: none;
  border-radius: 10px;
  transition: background 0.3s ease;
  cursor: pointer;
}

.submit-btn:hover {
  background: #3730a3;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 600px) {
  .task-form {
    padding: 20px;
  }
}
</style>

   <h2 class="form-title">➕ Add New Task</h2>

<form method="POST" class="task-form">
  <label>Title:</label>
  <input type="text" name="title" required>

  <label>Description:</label>
  <textarea name="description" required></textarea>

  <label>Link:</label>
  <textarea name="link" required></textarea>

  <label>Cost:</label>
  <input type="text" name="cost" required>

  <button type="submit" class="submit-btn">Add Task</button>
</form>

</body>
</html>
