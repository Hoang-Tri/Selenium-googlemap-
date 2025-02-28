<?php
$servername = "localhost";
$username = "thuc_tap";
$password = "f78dc8d3d0dc863c";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>