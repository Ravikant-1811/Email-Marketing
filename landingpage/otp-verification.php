<?php
require 'db_connection.php';
$email = base64_decode($_GET['email']);

if(isset($_POST['verify'])){
    $otp = $_POST['cotp'];
    $sql = "SELECT * FROM user_registration WHERE user_email = ? AND user_status = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0){
        $sqlUpdate = "UPDATE user_registration SET user_status = 'verified' WHERE user_email = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("s", $email);
        if($stmtUpdate->execute()){
            echo "<script>alert('OTP verified successfully. Please login.')</script>";
            echo "<script>window.location='../login.php'</script>";
        } else {
            echo "<script>alert('OTP verification failed. Please try again.')</script>";
        }
    } else {
        echo "<script>alert('Invalid OTP. Please try again.')</script>";
    }
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
                            <br>OTP Verification
                        </h4>
                        <p>
                            Enter your OTP below to verify.
                        </p>
                    </div>
                    <form class="form footer__quickContact--form js-form" method="POST" action="">
                        <div class="row">
                            <!-- OTP -->
                            <div class="form__group col-lg-12">
                                <input type="text" class="form__base" name="cotp" minlength="10"
                                    placeholder="Enter OTP" required />
                            </div>
                        </div>
                        <!-- Submit Button -->
                        <div class="form__group text-center">
                            <button type="submit" class="button button__purple" name="verify">
                                <span>Verify</span>
                            </button>
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