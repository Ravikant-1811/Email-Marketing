<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $email = $_POST['email'];
    $activation_date = date('Y-m-d');
    $expiry_date = date('Y-m-d', strtotime('+1 year')); // 1-year validity

    $stmt = $conn->prepare("INSERT INTO users (username, password, email, activation_date, expiry_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $password, $email, $activation_date, $expiry_date);

    if ($stmt->execute()) {
        echo "User registered successfully. Activation email sent.";
        // Send activation email
        mail($email, "Account Activated", "Your account has been activated until $expiry_date.");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
