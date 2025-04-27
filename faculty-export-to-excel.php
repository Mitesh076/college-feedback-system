<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_feedback";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Rebuild filter conditions from session or POST
$where = "1=1";
$params = [];
$types = "";

if (!empty($_POST['department'])) {
    $where .= " AND department = ?";
    $params[] = $_POST['department'];
    $types .= "s";
}

if (!empty($_POST['semester'])) {
    $where .= " AND semester = ?";
    $params[] = $_POST['semester'];
    $types .= "s";
}

if (!empty($_POST['section'])) {
    $where .= " AND section = ?";
    $params[] = $_POST['section'];
    $types .= "s";
}
if (!empty($_POST['teacher'])) {
    $where .= " AND teacher = ?";
    $params[] = $_POST['teacher'];
    $types .= "s";
}

if (!empty($_POST['subject'])) {
    $where .= " AND subject_name = ?";
    $params[] = $_POST['subject'];
    $types .= "s";
}

// Get filtered data
$sql = "SELECT * FROM faculty_feedback WHERE $where ORDER BY enrollment_number ASC";
$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="faculty_feedback_'.date('Y-m-d').'.xls"');

// Start Excel content
echo "<table border='1'>";
echo "<tr>
        <th>ID</th>
        <th>Name</th>
        <th>Enrollment</th>
        <th>Department</th>
        <th>Semester</th>
        <th>Section</th>
        <th>Teacher</th>
        <th>Subject</th>
        <th>CO1 Rating</th>
        <th>CO2 Rating</th>
        <th>CO3 Rating</th>
        <th>CO4 Rating</th>
        <th>CO5 Rating</th>
        <th>CO6 Rating</th>
         <th>CO7 Rating</th>
        <th>Date</th>
      </tr>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row['id']."</td>";
        echo "<td>".htmlspecialchars($row['name'])."</td>";
        echo "<td>".htmlspecialchars($row['enrollment_number'])."</td>";
        echo "<td>".htmlspecialchars($row['department'])."</td>";
        echo "<td>".htmlspecialchars($row['semester'])."</td>";
        echo "<td>".htmlspecialchars($row['section'])."</td>";
        echo "<td>".htmlspecialchars($row['teacher'])."</td>";
        echo "<td>".htmlspecialchars($row['subject_name'])."</td>";
        
        // Output CO ratings (1-6)
        for ($i = 1; $i <= 7; $i++) {
            echo "<td>".(!empty($row["co{$i}_text"]) ? $row["co{$i}_rating"] : '')."</td>";
        }
        
        echo "<td>".date('d/m/Y H:i', strtotime($row['submission_date']))."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='14'>No filtered feedback records found</td></tr>";
}

echo "</table>";

$stmt->close();
$conn->close();
exit();
?>