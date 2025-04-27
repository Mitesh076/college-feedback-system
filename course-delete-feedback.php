<?php
$conn = new mysqli("localhost", "root", "", "student_feedback");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $conn->query("DELETE FROM course_feedback WHERE id = $id");
}

$conn->close();
header("Location: course-display-feedback.php");
exit;
?>
