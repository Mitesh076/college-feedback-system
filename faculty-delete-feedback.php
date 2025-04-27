<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli("localhost", "root", "", "student_feedback");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $id = intval($_POST['id']);

    // Perform delete query
    $sql = "DELETE FROM faculty_feedback WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        // Redirect back to display page after successful delete
        header("Location: faculty-display-feedback.php");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $conn->close();
}
?>
