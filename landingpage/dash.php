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

?>
<!DOCTYPE html>
<html class="full-screen-mobile no-js" lang="en">

<head>
    <title>QuickSend - Your Fastest Connection</title>
    <meta charset="utf-8" />
    <meta name="keywords"
        content="QuickSend, bulk mail, email sender, fast delivery, email service, reliable mail delivery" />
    <meta name="description"
        content="QuickSend - Your Fastest Connection for reliable and lightning-fast bulk email delivery. Send emails effortlessly and efficiently." />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="author" content="QuickSend" />
    <meta property="og:title" content="QuickSend - Your Fastest Connection" />
    <meta property="og:description"
        content="Experience QuickSend: the fastest, most reliable bulk email sender. Your emails delivered in a flash, every time." />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="shortcut icon" href="images/favicon/favicon.svg" type="image/x-icon" />

    <!-- Plugins styles -->
    <link rel="stylesheet" href="styles/plugins.css" />

    <!-- Main styles -->
    <link rel="stylesheet" href="styles/main.css" />

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</head>

<body>

    <!-- Start Register  -->
    <footer class="footer">
        <div class="container footer__container" style="display: flex; justify-content: center; align-items: center;">
            <div class="footer__quickContact" style="width: 80%; ">
                <div class="footer__quickContact--body text-center" style="padding-top: 40px;">
                    <!-- Logo -->
                    <img src="images/logo/dark-logo.png" alt="QuickSend" class="footer__quickContact--logo"
                        style="width: 400px">

                    <div class="footer__quickContact--heading"
                        style="display: flex; justify-content: center; align-items: center;"><br><br>
                        <nav class="main-nav">
                            <a id="dashboard-link" class="nav__link">
                                <i class="fas fa-tachometer-alt"></i> &nbsp;Home
                            </a>
                            <a id="send-email-link" class="nav__link">
                                <i class="fas fa-envelope"></i> &nbsp;Send
                            </a>
                            <a id="upload-link" class="nav__link">
                                <i class="fas fa-upload"></i> &nbsp;Upload List
                            </a>
                            <a id="view-recipients-link" class="nav__link">
                                <i class="fas fa-users"></i> &nbsp;View List
                            </a>
                            <a id="edit-smtp-link" class="nav__link">
                                <i class="fas fa-cogs"></i> &nbsp;SMTP Setup
                            </a>
                            <a href="logout.php" class="nav__link">
                                <i class="fas fa-sign-out-alt"></i> &nbsp;Logout
                            </a>
                        </nav>
                    </div>

                    <!-- Dashboard Section -->
                    <div id="dashboard" class="section active " style="padding-top: 0px;">
                        <div class="row">
                            <h3>Welcome, <?php echo $user['user_name']; ?> !</h3>
                            <!-- Start pricing card 01 -->
                            <div class="col-lg-4">
                                <div class="pricing__card">
                                    <div class="pricing__card--price">
                                        <h5 class="pricing__card--price-title">Total Emails Sent</h5>
                                        <div class="pricing__card--price-currency">
                                            <?php echo $total_emails; ?><span>/ 1000</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End pricing card 01 -->

                            <!-- Start pricing card 02 -->
                            <div class="col-lg-4">
                                <div class="pricing__card">
                                    <div class="pricing__card--price">
                                        <h5 class="pricing__card--price-title">
                                            Total Recipients
                                        </h5>
                                        <div class="pricing__card--price-currency">
                                            <?php echo $total_recipients; ?> <span>/ 1000</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End pricing card 02 -->

                            <!-- Start pricing card 03 -->
                            <div class="col-lg-4">
                                <div class="pricing__card pricing__recommendation">
                                    <div class="pricing__card--price">
                                        <h5 class="pricing__card--price-title">SMTP Connection</h5>
                                        <div class="pricing__card--price-currency" style="color: yellow;">
                                            <p id="smtp-status"
                                                class="<?php echo strpos($smtp_status, 'Connected') !== false ? 'status-success' : 'status-failed'; ?>">
                                                <?php echo $smtp_status; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Send Email Section -->
                    <div id="send-email" class="section " style="padding: 0px;">
                        <div class="row">
                            <h3 class="footer__quickContact--heading-title" style="padding-bottom: 10px;">
                                Send Bulk Email !
                            </h3>
                            <!-- Login Form -->
                            <form id="loginForm" class="form footer__quickContact--form">
                                <div class="row ">
                                    <!-- Error Message -->
                                    <div id="errorMessage" class="form__group col-lg-12"
                                        style="color: red; text-align: center; display: none;"></div>

                                    <!-- Subject -->
                                    <div class="form__group col-lg-6">
                                        <input type="text" class="form__base" name="subject" placeholder="Subject"
                                            required>
                                    </div>
                                    <!-- To -->
                                    <div class="form__group col-lg-6">
                                        <select class="form__base" name="to" required>
                                            <option value="">Select Recipient</option>
                                            <option value="all">All Recipients</option>
                                            <option value="failed">Failed Recipients</option>
                                            <option value="sent">Sent Recipients</option>
                                        </select>
                                    </div>
                                    <!-- Body -->
                                    <div class="form__group col-lg-12">
                                        <textarea class="form__base" name="body" rows="5" placeholder="Body"
                                            required></textarea>
                                    </div>
                                    <!-- file -->
                                    <div class="form__group col-lg-12">
                                        <input type="file" class="form__base" name="attachment"
                                            accept=".pdf, .docx, .png, .jpg">
                                    </div>
                                </div>
                                <!-- Submit Button -->
                                <div class="form__group text-center">
                                    <button type="submit" class="button button__purple">
                                        <span>Send</span>
                                    </button>
                                </div>
                            </form>

                            <!-- Modal for Sending Status -->
                            <div id="statusModal" class="modal">
                                <div class="modal-content">
                                    <span class="close" id="closeStatusModal">&times;</span>
                                    <h2>Sending Status</h2>
                                    <div id="statusContent"></div>
                                </div>
                            </div>


                            <script>
                                const emailModal = document.getElementById("sendEmailModal");
                                const emailLink = document.getElementById("send-email-link");
                                const closeEmailModal = document.getElementById("closeEmailModal");
                                const emailForm = document.getElementById("emailForm");

                                const statusModal = document.getElementById("statusModal");
                                const closeStatusModal = document.getElementById("closeStatusModal");
                                const statusContent = document.getElementById("statusContent");

                                // Open the "Send Email" modal
                                emailLink.addEventListener("click", () => {
                                    emailModal.style.display = "flex";
                                });

                                // Close the "Send Email" modal
                                closeEmailModal.addEventListener("click", () => {
                                    emailModal.style.display = "none";
                                });

                                // Close the "Sending Status" modal
                                closeStatusModal.addEventListener("click", () => {
                                    statusModal.style.display = "none";
                                });

                                // Close modals when clicking outside
                                window.addEventListener("click", (event) => {
                                    if (event.target === emailModal) emailModal.style.display = "none";
                                    if (event.target === statusModal) statusModal.style.display = "none";
                                });

                                // Handle form submission
                                emailForm.addEventListener("submit", (e) => {
                                    e.preventDefault();
                                    const formData = new FormData(emailForm);

                                    // Close the "Send Email" modal
                                    emailModal.style.display = "none";

                                    // Show the "Sending Status" modal
                                    statusContent.innerHTML = "Starting to send emails...<br>";
                                    statusModal.style.display = "flex";

                                    // Start sending emails
                                    fetch("send_bulk_email.php", {
                                        method: "POST",
                                        body: formData,
                                    })
                                        .then((response) => {
                                            const reader = response.body.getReader();
                                            const decoder = new TextDecoder();
                                            function read() {
                                                reader.read().then(({ done, value }) => {
                                                    if (done) return; // All data has been read
                                                    const chunk = decoder.decode(value);
                                                    statusContent.innerHTML += chunk + "<br>";
                                                    read(); // Continue reading
                                                });
                                            }
                                            read();
                                        })
                                        .catch(() => {
                                            statusContent.innerHTML += "An error occurred.";
                                        });
                                });

                            </script>
                        </div>
                    </div>

                    <!-- Upload List Section -->
                    <div id="upload" class="section" style="padding-top: 0px;">
                        <h1>Upload Recipient List</h1>
                        <!-- Upload form goes here -->
                    </div>

                    <!-- View Recipients Section -->
                    <div id="view-recipients" class="section" style="padding-top: 0px;">
                        <h3>View Recipients</h3>
                        <div id="recipientsContent">
                            <!-- Recipients table will be dynamically loaded here -->
                        </div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            const viewRecipientsLink = document.getElementById("view-recipients-link");
                            const recipientsContent = document.getElementById("recipientsContent");

                            // Load recipients data into the section when the link is clicked
                            viewRecipientsLink.addEventListener("click", () => {
                                // Display a loading message
                                recipientsContent.innerHTML = "Loading recipients...";

                                // Fetch recipient data dynamically
                                fetch("view_recipients.php")
                                    .then((response) => response.text())
                                    .then((html) => {
                                        recipientsContent.innerHTML = html;
                                    })
                                    .catch((error) => {
                                        console.error("Error fetching recipients:", error);
                                        recipientsContent.innerHTML = "Failed to load recipients.";
                                    });
                            });
                        });
                    </script>


                    <!-- SMTP Setup Section -->
                    <div id="edit-smtp" class="section"
                        style="padding: 0px; max-width: 600px; margin: auto; font-family: Arial, sans-serif;">
                        <h2 style="text-align: center;">SMTP Setup</h2>

                        <!-- SMTP Status and Refresh Button -->
                        <div style="max-width: 800px; margin: auto; text-align: center;">
                            <div class="row form">
                                <div class="col-lg-12">
                                    <h4 id="smtp-status" style="color: #666; margin-bottom: 20px;">
                                        SMTP Status: <span id="status-placeholder">Checking...</span>
                                    </h4>
                                </div>
                                <div class="col-lg-6">
                                    <button id="refresh-status" type="hide"class="button button__blue"style="display: none;">
                                
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Error Message Section -->
                        <div id="errorMessage" class="form__group col-lg-12"
                            style="color: red; text-align: center; display: none;">
                            <!-- Error messages will appear here -->
                        </div>

                        <!-- SMTP Settings Form -->
                        <form method="post" class="form footer__quickContact--form">
                            <div class="row">
                                <!-- SMTP Host -->
                                <div class="form__group col-lg-6">
                                    <label for="smtp_host" style="display: block; font-weight: bold;">SMTP Host:</label>
                                    <input type="text" class="form__base" name="smtp_host" id="smtp_host"
                                        placeholder="SMTP Host" value="<?php echo $smtp_settings['smtp_host'] ?? ''; ?>"
                                        required>
                                </div>

                                <!-- SMTP Port -->
                                <div class="form__group col-lg-6">
                                    <label for="smtp_port" style="display: block; font-weight: bold;">SMTP Port:</label>
                                    <input type="number" class="form__base" name="smtp_port" id="smtp_port"
                                        placeholder="SMTP Port" value="<?php echo $smtp_settings['smtp_port'] ?? ''; ?>"
                                        required>
                                </div>
                            </div>
                            <div class="row">
                                <!-- SMTP Username -->
                                <div class="form__group col-lg-6">
                                    <label for="smtp_user" style="display: block; font-weight: bold;">SMTP
                                        Username:</label>
                                    <input type="text" class="form__base" name="smtp_user" id="smtp_user"
                                        placeholder="SMTP Username"
                                        value="<?php echo $smtp_settings['smtp_user'] ?? ''; ?>" required>
                                </div>

                                <!-- SMTP Password -->
                                <div class="form__group col-lg-6">
                                    <label for="smtp_pass" style="display: block; font-weight: bold;">SMTP
                                        Password:</label>
                                    <input type="password" class="form__base" name="smtp_pass" id="smtp_pass"
                                        placeholder="SMTP Password"
                                        value="<?php echo $smtp_settings['smtp_pass'] ?? ''; ?>" required>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form__group text-center">
                                <button type="submit" class="button button__purple" style="padding: 10px 20px;">
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>

                </div>


                <script>
                    // Refresh SMTP status
                    document.getElementById('refresh-status').addEventListener('click', function () {
                        fetch('check_smtp_status.php')
                            .then(response => response.text())
                            .then(status => {
                                const statusElement = document.getElementById('status-placeholder');
                                statusElement.textContent = status;
                                if (status.includes('Connected')) {
                                    statusElement.style.color = 'green';
                                } else {
                                    statusElement.style.color = 'red';
                                }
                            })
                            .catch(error => {
                                const statusElement = document.getElementById('status-placeholder');
                                statusElement.textContent = 'Error checking status.';
                                statusElement.style.color = 'red';
                            });
                    });

                    // Initial status check
                    document.getElementById('refresh-status').click();
                </script>


            </div>
        </div>
        </div>
    </footer>


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
                console.log("Target Section:", sectionId, target);
                if (target) target.classList.add("active");
            }

            navLinks.forEach(link => {
                link.addEventListener("click", function () {
                    console.log("Clicked Link ID:", this.id);
                    const sectionId = this.id.replace("-link", "");
                    console.log("Section ID to Show:", sectionId);
                    showSection(sectionId);
                });
            });

            // Default to showing the dashboard
            showSection("dashboard");
        });

    </script>

    <!-- End Register -->

    <!-- Jquery -->
    <script src="scripts/jquery.min.js"></script>

    <!-- Plugins scripts -->
    <script src="scripts/plugins.js"></script>

    <!-- Main scripts -->
    <script src="scripts/main.js"></script>
</body>

</html>