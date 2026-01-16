<?php
require 'db_connection.php';

if (isset($_POST['submit'])) {
    // Retrieve user inputs
    $query = "SELECT MAX(CAST(SUBSTRING(user_id, 6) AS UNSIGNED)) AS max_id FROM user_registration";
    $result = mysqli_query($conn, $query);
    if (!$result) {
        die("Database query failed: " . mysqli_error($conn)); // Check for database query errors
    }
    $row = mysqli_fetch_assoc($result);
    $max_id = $row['max_id'] ? $row['max_id'] : 0; // fallback to 0 if NULL
    $new_user_id = 'QS/U' . str_pad($max_id + 1, 4, '0', STR_PAD_LEFT);

    $name = trim($_POST['fname']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $email = trim($_POST['cemail']);
    $mobile = trim($_POST['cmobile']);
    $otp = rand(100000, 999999);

    // Check if user already exists
    $sqlcheck = "SELECT * FROM user_registration WHERE user_email = ? OR user_mobile = ?";
    $stmt = $conn->prepare($sqlcheck);
    $stmt->bind_param("ss", $email, $mobile);
    $stmt->execute();
    $resultcheck = $stmt->get_result();

    if ($resultcheck->num_rows > 0) {
        echo "<script>alert('User already exists. Please login.')</script>";
    } else {
        // Password Confirmation Check
        if ($password === $cpassword) {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $sqlInsert = "INSERT INTO user_registration (user_id, user_name, user_password, user_email, user_mobile, user_status) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("ssssss", $new_user_id, $name, $hashed_password, $email, $mobile, $otp);
            if ($stmtInsert->execute()) {

                echo "<script>window.location='login.php'</script>";
            } else {
                echo "<script>alert('Registration failed. Please try again later.')</script>";
            }
        } else {
            echo "<script>alert('Passwords do not match. Please try again.')</script>";
        }
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
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
</head>

<body>

    <!-- Start Register  -->
    <footer class="footer">
        <div class="container footer__container" style="display: flex; justify-content: center; align-items: center;">
            <div class="footer__quickContact col-lg-6">
                <div class="footer__quickContact--body text-center" style="padding-top:40px;">
                    <!-- Logo -->
                    <img src="images/logo/dark-logo.png" alt="QuickSend" class="footer__quickContact--logo"
                        style="width:400px">

                    <div class="footer__quickContact--heading text-center">
                        <h4 class="footer__quickContact--heading-title">
                            <br>Register Now
                        </h4>
                        <p>
                            Enter your details below to register.
                        </p>
                    </div>
                    <form class="form footer__quickContact--form js-form" method="POST" action="">
                        <div class="row">
                            <!-- Full Name -->

                            <input type="hidden" name="user_id" value="Test/1010" readonly>
                            <div class="form__group col-lg-6">
                                <input type="text" class="form__base" name="fname" minlength="2" placeholder="Full Name"
                                    required />
                            </div>
                            <!-- Moblie -->
                            <div class="form__group col-lg-6">
                                <input type="text" class="form__base" name="cmobile" minlength="10"
                                    placeholder="Enter Mobile No." required />
                            </div>
                            <!-- Email -->
                            <div class="form__group col-lg-12">
                                <input type="email" class="form__base" name="cemail" placeholder="Enter Email"
                                    required />
                            </div>
                            <!-- Password -->
                            <div class="form__group col-lg-6">
                                <input type="password" class="form__base" name="password" minlength="6"
                                    placeholder="Enter Password" required />
                            </div>
                            <!-- Confirm Password -->
                            <div class="form__group col-lg-6">
                                <input type="password" class="form__base" name="cpassword" minlength="6"
                                    placeholder="Confirm Password" required />
                            </div>

                        </div>
                        <!-- Submit Button -->
                        <div class="form__group text-center">
                            <button type="submit" class="button button__purple" name="submit">
                                <span>Register</span>
                            </button><br>
                            Already have an account? <a href="login.php">
                                <span>Login</span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </footer>

    <!-- End Register -->

    <!-- Jquery -->
    <script src="scripts/jquery.min.js"></script>

    <!-- Plugins scripts -->
    <script src="scripts/plugins.js"></script>

    <!-- Main scripts -->
    <script src="scripts/main.js"></script>
</body>

</html>