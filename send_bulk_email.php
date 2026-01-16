<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: text/plain');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');

    $subject = $_POST['subject'] ?? '';
    $body = $_POST['body'] ?? '';
    $attachment = $_FILES['attachment'] ?? null;

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mailsql = "SELECT * FROM smtp_settings";
        $smtpresult = $conn->query($mailsql);
        $smtp = $smtpresult->fetch_assoc();
        $mail->Host = $smtp['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtp['smtp_user'];
        $mail->Password = $smtp['smtp_pass'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $smtp['smtp_port'];

        $mail->setFrom($smtp['smtp_user'], 'Aurea Bliss');
        $mail->isHTML(true);

        if ($attachment && $attachment['error'] === UPLOAD_ERR_OK) {
            $mail->addAttachment($attachment['tmp_name'], $attachment['name']);
        }

        $query = "SELECT * FROM recipients";
        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            $mail->clearAddresses();
            $mail->addAddress($row['email'], $row['name']);
            $mail->Subject = $subject;
            $mail->Body = $body;

            if ($mail->send()) {
                $conn->query("UPDATE recipients SET status = 'sent' WHERE id = " . $row['id']);
                echo "Email sent to " . $row['email'] . "\n";
            } else {
                $conn->query("UPDATE recipients SET status = 'failed' WHERE id = " . $row['id']);
                echo "Failed to send email to " . $row['email'] . "\n";
            }
            flush();
            ob_flush();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        flush();
        ob_flush();
    }
}
