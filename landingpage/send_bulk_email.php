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
        // Load SMTP settings
        $smtpQuery = "SELECT * FROM smtp_settings LIMIT 1";
        $smtpResult = $conn->query($smtpQuery);
        $smtp = $smtpResult->fetch_assoc();

        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = $smtp['smtp_host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp['smtp_user'];
        $mail->Password   = $smtp['smtp_pass'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = $smtp['smtp_port'];

        // Sender details
        $mail->setFrom($smtp['smtp_user'], 'Radhakrishna Web Solution');
        $mail->isHTML(true);

        // Prepare the HTML body
        $htmlBody = nl2br(htmlentities($body, ENT_QUOTES, 'UTF-8'));

        // Handle attachment (max 5MB, optional)
        if ($attachment && $attachment['error'] === UPLOAD_ERR_OK) {
            if ($attachment['size'] <= 5 * 1024 * 1024) {
                $mail->addAttachment($attachment['tmp_name'], $attachment['name']);
            } else {
                echo "⚠️ Attachment too large (max 5MB). Skipping file.\n";
            }
        }

        // Get all recipients
        $recipientsQuery = "SELECT * FROM recipients";
        $recipients = $conn->query($recipientsQuery);

        while ($row = $recipients->fetch_assoc()) {
            $mail->clearAddresses();

            $mail->addAddress($row['email'], $row['name']);
            $mail->Subject = $subject;

            // Optional: personalize greeting
            $personalizedBody = str_replace("Dear Intern", "Dear " . htmlspecialchars($row['name']), $htmlBody);
            $mail->Body = $personalizedBody;

            // Send email
            if ($mail->send()) {
                $conn->query("UPDATE recipients SET status = 'sent' WHERE id = " . $row['id']);
                echo "✅ Email sent to " . $row['email'] . "\n";
            } else {
                $conn->query("UPDATE recipients SET status = 'failed' WHERE id = " . $row['id']);
                echo "❌ Failed to send email to " . $row['email'] . "\n";
            }

            flush();
            ob_flush();
        }
    } catch (Exception $e) {
        echo "⚠️ Error: " . $e->getMessage() . "\n";
        flush();
        ob_flush();
    }
}
