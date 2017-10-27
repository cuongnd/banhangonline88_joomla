<?php
$servername = "localhost";
$username = "banhangonl_bho";
$password = "w7a8GNPjXb";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>