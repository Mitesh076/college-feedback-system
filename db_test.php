<?php
$conn = new mysqli('localhost', 'root', '', 'student_feedback');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
$conn->close();
?>