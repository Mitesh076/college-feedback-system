<?php
$conn = new mysqli("localhost", "root", "", "student_feedback");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $stmt = $conn->prepare("DELETE FROM infrastructure_feedback WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Record deleted'); window.location.href='display-infrastructure-feedback.php';</script>";
    } else {
        echo "Error deleting: " . $conn->error;
    }
}
?>
