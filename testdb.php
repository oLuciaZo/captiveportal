<?php
$servername = '192.168.48.2';
$username = 'root';
$password = 'radiusrootdbpw';
$dbname = 'backend';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Data to be inserted
$c_ip = '192.168.1.1';
$c_mac = '00:1A:2B:3C:4D:5E';
$c_os = 'Windows 10';
$c_idcard = '1111';
$c_phone = '1234567890';
$c_time = '2024-06-03 10:00:00';
$c_tennant = 'mk-01';

// SQL query to insert data
$sql = "INSERT INTO be_client (c_ip, c_mac, c_os, c_idcard, c_phone, c_time, c_tennant) VALUES (?, ?, ?, ?, ?, ?, ?)";

// Prepare statement
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Preparation failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param("sssssss", $c_ip, $c_mac, $c_os, $c_idcard, $c_phone, $c_time, $c_tennant);

// Execute statement
if ($stmt->execute()) {
    echo "Record inserted successfully!";
} else {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
