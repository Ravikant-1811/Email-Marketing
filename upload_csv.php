<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $email = $data[0];
            $name = isset($data[1]) ? $data[1] : null;

            $stmt = $conn->prepare("INSERT IGNORE INTO recipients (email, name) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $name);
            $stmt->execute();
        }
        fclose($handle);
    }
    echo "<script>alert('Recipients uploaded successfully.');</script>";
    echo "<script>window.location = 'index.php';</script>";
}
?>
