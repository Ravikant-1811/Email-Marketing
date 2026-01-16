<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'db_connection.php';

$sql_user = "SELECT * FROM user_registration WHERE user_id = '" . $_SESSION['user_id'] . "'";
$result_user = $conn->query($sql_user);
$user = $result_user->fetch_assoc();

// Fetch Total Emails Sent
$total_emails_query = "SELECT COUNT(*) as total_sent FROM recipients WHERE status = 'sent'";
$total_emails_result = $conn->query($total_emails_query);
$total_emails = $total_emails_result->fetch_assoc()['total_sent'];

// Fetch Total Recipients
$total_recipients_query = "SELECT COUNT(*) as total_recipients FROM recipients";
$total_recipients_result = $conn->query($total_recipients_query);
$total_recipients = $total_recipients_result->fetch_assoc()['total_recipients'];

// Fetch Current SMTP Settings
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function checkSMTPConnectionWithAuth($host, $port, $username, $password)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $port;

        $mail->smtpConnect();
        return "Connected";
    } catch (Exception $e) {
        return "Failed: " . $e->getMessage();
    }
}

$smtp_query = "SELECT `smtp_host`, `smtp_port`, `smtp_user`, `smtp_pass` FROM `smtp_settings` LIMIT 1";
$smtp_result = $conn->query($smtp_query);

if ($smtp_result->num_rows > 0) {
    $smtp_settings = $smtp_result->fetch_assoc();
    $smtp_host = $smtp_settings['smtp_host'];
    $smtp_port = $smtp_settings['smtp_port'];
    $smtp_user = $smtp_settings['smtp_user'];
    $smtp_pass = $smtp_settings['smtp_pass'];

    $smtp_status = checkSMTPConnectionWithAuth($smtp_host, $smtp_port, $smtp_user, $smtp_pass);
} else {
    $smtp_status = "SMTP settings not found in the database.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickSend Dashboard</title>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .section {
            display: none;
        }
        .section.active {
            display: block;
        }
        .nav__link {
            cursor: pointer;
            padding: 10px;
            display: inline-block;
        }
        .nav__link:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a id="dashboard-link" class="nav__link"><i class="fas fa-tachometer-alt"></i> Home</a>
            <a id="send-email-link" class="nav__link"><i class="fas fa-envelope"></i> Send Email</a>
            <a id="upload-link" class="nav__link"><i class="fas fa-upload"></i> Upload List</a>
            <a id="view-recipients-link" class="nav__link"><i class="fas fa-users"></i> View Recipients</a>
            <a id="edit-smtp-link" class="nav__link"><i class="fas fa-cogs"></i> SMTP Setup</a>
            <a href="logout.php" class="nav__link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </header>

    <main>
        <!-- Dashboard Section -->
        <div id="dashboard" class="section active">
            <h1>Welcome, <?php echo htmlspecialchars($user['user_name']); ?>!</h1>
            <p>Total Emails Sent: <?php echo $total_emails; ?></p>
            <p>Total Recipients: <?php echo $total_recipients; ?></p>
            <p>SMTP Status: <?php echo $smtp_status; ?></p>
        </div>

        <!-- Send Email Section -->
        <div id="send-email" class="section">
            <h1>Send Email</h1>
            <!-- Email sending form goes here -->
        </div>

        <!-- Upload List Section -->
        <div id="upload" class="section">
            <h1>Upload Recipient List</h1>
            <!-- Upload form goes here -->
        </div>

        <!-- View Recipients Section -->
        <div id="view-recipients" class="section">
            <h1>View Recipients</h1>
            <!-- Recipient table goes here -->
        </div>

        <!-- SMTP Setup Section -->
        <div id="edit-smtp" class="section">
            <h1>SMTP Setup</h1>
            <!-- SMTP configuration form goes here -->
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const sections = document.querySelectorAll(".section");
            const navLinks = document.querySelectorAll(".nav__link");

            function hideAllSections() {
                sections.forEach(section => section.classList.remove("active"));
            }

            function showSection(sectionId) {
                hideAllSections();
                const target = document.getElementById(sectionId);
                if (target) target.classList.add("active");
            }

            navLinks.forEach(link => {
                link.addEventListener("click", e => {
                    const sectionId = e.target.id.replace("-link", "");
                    showSection(sectionId);
                });
            });

            // Default to showing the dashboard
            showSection("dashboard");
        });
    </script>
</body>
</html>
