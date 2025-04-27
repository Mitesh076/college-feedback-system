<?php
$conn = new mysqli("localhost", "root", "", "student_feedback");
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed"]));
}

$enrollment = $_GET['enrollment'];
$year = $_GET['year'];
$department = $_GET['department'];
$semester = $_GET['semester'];
$section = $_GET['section'];

$sql = "SELECT COUNT(*) as count FROM infrastructure_feedback WHERE enrollment_number = ? AND year = ? AND department = ? AND semester = ? AND section = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisss", $enrollment, $year, $department, $semester, $section);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(["exists" => $row['count'] > 0]);

$stmt->close();
$conn->close();
?>