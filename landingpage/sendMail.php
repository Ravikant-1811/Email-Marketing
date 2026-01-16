<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "bulk_mailer");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if the user already exists
    $checkQuery = "SELECT * FROM contact_form_submissions WHERE mobile = ? OR email = ?";
    $stmtCheck = $conn->prepare($checkQuery);
    $stmtCheck->bind_param("ss", $mobile, $email);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();

    if ($result->num_rows > 0) {
        // User already exists
        echo "Error: The provided email or mobile number is already registered.";
    } else {
        // Save data to database
        $stmt = $conn->prepare("INSERT INTO contact_form_submissions (name, mobile, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $mobile, $email);

        if ($stmt->execute()) {
            // Send thank-you email
            $to = $email;
            $subject = "Thank You for Contacting QuickSend";
            $message = "Dear $name,\n\nThank you for reaching out to QuickSend. We'll get in touch with you soon.\n\nBest Regards,\nThe QuickSend Team";
            $headers = "From: no-reply@quicksend.com";

            mail($to, $subject, $message, $headers);

            echo "Success";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    }

    // Close the check prepared statement
    $stmtCheck->close();
    $conn->close();
}
?>
