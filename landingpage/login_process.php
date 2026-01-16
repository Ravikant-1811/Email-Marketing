<?php
require 'db_connection.php';
session_start();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['cemail'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM user_registration WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['user_password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $response['success'] = true;
    } else {
        $response['message'] = "Invalid email or password.";
    }
} else {
    $response['message'] = "Invalid request method.";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
