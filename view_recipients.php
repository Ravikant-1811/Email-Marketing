<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo "Unauthorized access.";
    exit();
}

require 'db_connection.php';

$query = "SELECT * FROM recipients";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo '<table>';
    echo '<thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Name</th>
                <th>Status</th>
            </tr>
          </thead>';
    echo '<tbody>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . $row['email'] . '</td>';
        echo '<td>' . $row['name'] . '</td>';
        echo '<td>' . $row['status'] . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p>No recipients found.</p>';
}
?>
