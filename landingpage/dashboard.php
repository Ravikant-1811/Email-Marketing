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
$smtp_query = "SELECT * FROM smtp_settings LIMIT 1";
$smtp_result = $conn->query($smtp_query);
$smtp_settings = $smtp_result->fetch_assoc();

// Handle Form Submission for SMTP Settings
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $smtp_host = $_POST['smtp_host'];
    $smtp_port = $_POST['smtp_port'];
    $smtp_user = $_POST['smtp_user'];
    $smtp_pass = $_POST['smtp_pass'];

    if ($smtp_settings) {
        // Update existing settings
        $update_query = "UPDATE smtp_settings SET smtp_host=?, smtp_port=?, smtp_user=?, smtp_pass=? WHERE id=?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sisss", $smtp_host, $smtp_port, $smtp_user, $smtp_pass, $smtp_settings['id']);
    } else {
        // Insert new settings
        $insert_query = "INSERT INTO smtp_settings (smtp_host, smtp_port, smtp_user, smtp_pass) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("siss", $smtp_host, $smtp_port, $smtp_user, $smtp_pass);
    }
    $stmt->execute();
    header("Location: index.php"); // Refresh to display updated settings
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

</head>

<body>
    <nav>
        <nav>
            <a href="index.php"><i class="fas fa-tachometer-alt"></i> &nbsp;Home</a>
            <a id="send-email-link"><i class="fas fa-envelope"></i> &nbsp;Send</a>
            <a id="upload-link"><i class="fas fa-upload"></i> &nbsp;Upload List</a>
            <a id="view_recipients"><i class="fas fa-users"></i> &nbsp;View List</a>
            <a id="edit-smtp-btn"><i class="fas fa-cogs"></i> &nbsp;SMTP Setup</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> &nbsp;Logout</a>
        </nav>

    </nav>
    
    <div class="container">
        <h1>Welcome, <?php echo $user['user_name']; ?> !</h1>
        <div class="card">
            <h2>Total Emails Sent</h2>
            <p><?php echo $total_emails; ?></p>
        </div>
        <div class="card">
            <h2>Total Recipients</h2>
            <p><?php echo $total_recipients; ?></p>
        </div>
        <?php
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\Exception;

        require 'vendor/autoload.php';
        require 'db_connection.php';

        // Function to check SMTP connection
        function checkSMTPConnectionWithAuth($host, $port, $username, $password)
        {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = $host;
                $mail->SMTPAuth = true;
                $mail->Username = $username;
                $mail->Password = $password;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Use TLS or SSL as needed
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

            // Check SMTP connection
            $smtp_status = checkSMTPConnectionWithAuth($smtp_host, $smtp_port, $smtp_user, $smtp_pass);
        } else {
            $smtp_status = "SMTP settings not found in the database.";
        }
        ?>
        <div class="card">
            <h2>SMTP Connection Status</h2>
            <p id="smtp-status"
                class="<?php echo strpos($smtp_status, 'Connected') !== false ? 'status-success' : 'status-failed'; ?>">
                <?php echo $smtp_status; ?>
            </p>
            <button id="refresh-status"><i class="fas fa-sync-alt"></i> Refresh Status</button>
        </div>

    </div>


</body>
<script>
   document.getElementById('refresh-status').addEventListener('click', function () {
    fetch('check_smtp_status.php')
        .then(response => response.text())
        .then(status => {
            const statusElement = document.getElementById('smtp-status');
            statusElement.textContent = status;
            if (status.includes('Connected')) {
                statusElement.className = 'status-success';
            } else {
                statusElement.className = 'status-failed';
            }
        })
        .catch(error => {
            alert('Failed to refresh status: ' + error.message);
        });
});

</script>
<?php
include 'module.php';
?>

</html>