<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
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
            <div class="footer__quickContact" style="width: 50%; max-width: 600px;">
                <div class="footer__quickContact--body text-center" style="padding-top: 40px;">
                    <!-- Logo -->
                    <img src="images/logo/dark-logo.png" alt="QuickSend" class="footer__quickContact--logo"
                        style="width: 400px">

                    <div class="footer__quickContact--heading text-center">
                        <h4 class="footer__quickContact--heading-title">
                            <br>Login Now
                        </h4>
                        <p>
                            Enter your details below to login.
                        </p>
                    </div>
                    <!-- Login Form -->
                    <form id="loginForm" class="form footer__quickContact--form">
                        <div class="row">
                            <!-- Error Message -->
                            <div id="errorMessage" class="form__group col-lg-12" style="color: red; text-align: center; display: none;"></div>

                            <!-- Email -->
                            <div class="form__group col-lg-12">
                                <input type="email" class="form__base" name="cemail" placeholder="Enter Email" required />
                            </div>
                            <!-- Password -->
                            <div class="form__group col-lg-12">
                                <input type="password" class="form__base" name="password" minlength="6" placeholder="Enter Password" required />
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="form__group text-center">
                            <button type="submit" class="button button__purple">
                                <span>Login</span>
                            </button><br>
                            Don't have an account? <a href="register.php">
                                <span>Register Now</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </footer>

    <script>
        $(document).ready(function () {
            $('#loginForm').on('submit', function (e) {
                e.preventDefault(); // Prevent default form submission

                // Clear previous error message
                $('#errorMessage').hide().text('');

                // Collect form data
                var formData = $(this).serialize();

                // AJAX request
                $.ajax({
                    url: 'login_process.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            // Redirect to the dashboard
                            window.location.href = 'dashboard.php';
                        } else {
                            // Display error message
                            $('#errorMessage').text(response.message).fadeIn();
                        }
                    },
                    error: function () {
                        // Handle server error
                        $('#errorMessage').text('An error occurred. Please try again later.').fadeIn();
                    }
                });
            });
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