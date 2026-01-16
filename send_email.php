<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'db_connection.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.hostinger.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ravikant@aureabliss.com'; // Replace with your email
    $mail->Password = 'AureaB@123'; // Replace with your password or app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    $mail->setFrom('ravikant@aureabliss.com', 'Aurea Bliss');
    $mail->addReplyTo('ravikant@aureabliss.com', 'Aurea Bliss');
    $mail->isHTML(true);

    $query = "SELECT * FROM recipients WHERE status = 'pending' LIMIT 10";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $mail->clearAddresses();
        $mail->addAddress($row['email'], $row['name']);
        $mail->Subject = 'Bulk Email Example';
        $mail->Body = file_get_contents('email_template.html');

        if ($mail->send()) {
            $conn->query("UPDATE recipients SET status = 'sent' WHERE id = " . $row['id']);
        } else {
            $conn->query("UPDATE recipients SET status = 'failed' WHERE id = " . $row['id']);
        }
        sleep(2); // Throttle to avoid spam detection
    }

    echo "Emails sent!";
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}";
}
?>