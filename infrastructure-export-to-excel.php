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
$filters = $_SESSION['current_filters'] ?? ['where' => '1=1', 'params' => [], 'types' => ''];

$where = $filters['where'];
$params = $filters['params'];
$types = $filters['types'];

// Override with POST data if available
if (!empty($_POST['department'])) {
    $where .= " AND department = ?";
    $params[] = $_POST['department'];
    $types .= "s";
}

if (!empty($_POST['semester'])) {
    $where .= " AND semester = ?";
    $params[] = $_POST['semester'];
    $types .= "i";
}

if (!empty($_POST['section'])) {
    $where .= " AND section = ?";
    $params[] = $_POST['section'];
    $types .= "s";
}

if (!empty($_POST['year'])) {
    $where .= " AND year = ?";
    $params[] = $_POST['year'];
    $types .= "i";
}

// Get filtered data
$sql = "SELECT * FROM infrastructure_feedback WHERE $where ORDER BY enrollment_number ASC";
$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="infrastructure_feedback_'.date('Y-m-d').'.xls"');

// Start Excel content
echo "<table border='1'>";
echo "<tr>
        <th>ID</th>
        <th>Name</th>
        <th>Enrollment</th>
        <th>Department</th>
        <th>Semester</th>
        <th>Section</th>
        <th>Year</th>
        <th>Classroom Rating</th>
        <th>Computer Labs Rating</th>
        <th>Library Rating</th>
        <th>Wi-Fi Rating</th>
        <th>Washrooms Rating</th>
        <th>Parking Rating</th>
        <th>Canteen Rating</th>
        <th>Security Rating</th>
        <th>Overall Rating</th>
        <th>General Comments</th>
        <th>Submission Date</th>
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
        echo "<td>".htmlspecialchars($row['year'])."</td>";
        echo "<td>".$row['classroom_rating']."</td>";
        echo "<td>".$row['lab_rating']."</td>";
        echo "<td>".$row['library_rating']."</td>";
        echo "<td>".$row['wifi_rating']."</td>";
        echo "<td>".$row['washroom_rating']."</td>";
        echo "<td>".$row['parking_rating']."</td>";
        echo "<td>".$row['canteen_rating']."</td>";
        echo "<td>".$row['security_rating']."</td>";
        echo "<td>".$row['overall_rating']."</td>";
        echo "<td>".htmlspecialchars($row['general_comments'])."</td>";
        echo "<td>".date('d/m/Y H:i', strtotime($row['submission_date']))."</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='18'>No filtered feedback records found</td></tr>";
}

echo "</table>";

$stmt->close();
$conn->close();
exit();
?>