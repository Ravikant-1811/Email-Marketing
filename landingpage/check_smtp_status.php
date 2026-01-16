<?php
require 'db_connection.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to check SMTP connection
function checkSMTPConnectionWithAuth($host, $port, $username, $password) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $port;

        $mail->smtpConnect(); // Test connection
        return "Connected";
    } catch (Exception $e) {
        return "Failed: " . $e->getMessage();
    }
}

// Fetch SMTP settings from the database
$smtp_query = "SELECT `smtp_host`, `smtp_port`, `smtp_user`, `smtp_pass` FROM `smtp_settings` LIMIT 1";
$smtp_result = $conn->query($smtp_query);

if ($smtp_result->num_rows > 0) {
    $smtp_settings = $smtp_result->fetch_assoc();
    $smtp_host = $smtp_settings['smtp_host'];
    $smtp_port = $smtp_settings['smtp_port'];
    $smtp_user = $smtp_settings['smtp_user'];
    $smtp_pass = $smtp_settings['smtp_pass'];

    echo checkSMTPConnectionWithAuth($smtp_host, $smtp_port, $smtp_user, $smtp_pass);
} else {
    echo "SMTP settings not found in the database.";
}
?>
