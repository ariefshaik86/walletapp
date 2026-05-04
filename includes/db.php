<?php
$host = "sql303.infinityfree.com";
$db = "if0_39343256_rapid";
$user = "if0_39343256";
$pass = "B8LgZlyEFCbrN";

$conn = new mysqli($host, $user, $pass, $db);
$pdo = new PDO("mysql:host=sql303.infinityfree.com;dbname=if0_39343256_rapid;charset=utf8", $user, $pass, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
