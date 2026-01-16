<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $today = date('Y-m-d');
        if ($today >= $user['activation_date'] && $today <= $user['expiry_date']) {
            $_SESSION['user'] = $user['username'];
            header("Location: index.php");
        } else {
            echo "Your account is inactive or expired.";
        }
    } else {
        echo "Invalid credentials.";
    }
}
?>
