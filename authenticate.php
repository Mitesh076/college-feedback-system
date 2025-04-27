<?php
session_start();
$conn = new mysqli("localhost", "root", "", "student_feedback");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];
$role     = $_POST['role'];

$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password' AND role = '$role'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] === 'admin') {
        header("Location: admin-dashboard.php");
    } else {
        header("Location: student-dashboard.php");
    }
} else {
    echo "Invalid login credentials or role.";
}
?>
