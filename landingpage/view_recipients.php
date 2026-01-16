<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized access.";
    exit();
}

require 'db_connection.php';

// Query to fetch recipients in alphabetical order, limited to 10
$query = "SELECT * FROM recipients ORDER BY name ASC LIMIT 10";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo '<style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        thead {
            background-color: #4CAF50;
            color: white;
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody tr:hover {
            background-color: #f1f1f1;
        }
        th {
            text-transform: uppercase;
        }
    </style>';
    echo '<table>';
    echo '<thead>
            <tr>
                <th>Serial No</th>
                <th>Email</th>
                <th>Name</th>
                <th>Status</th>
            </tr>
          </thead>';
    echo '<tbody>';
    $serial_no = 1; // Initialize serial number
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . $serial_no++ . '</td>';
        echo '<td>' . htmlspecialchars($row['email']) . '</td>';
        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
        echo '<td>' . htmlspecialchars($row['status']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
} else {
    echo '<p>No recipients found.</p>';
}
?>
