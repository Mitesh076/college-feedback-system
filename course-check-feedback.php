<?php
// Ensure no output before headers
if (ob_get_level()) ob_end_clean();

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'student_feedback');

$response = ['success' => false, 'message' => ''];

try {
    // Validate input
    if (!isset($_GET['enrollment']) || !isset($_GET['subject'])) {
        throw new Exception("Missing required parameters");
    }

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed");
    }

    $enrollment = $conn->real_escape_string($_GET['enrollment']);
    $subject = $conn->real_escape_string($_GET['subject']);

    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM course_feedback WHERE enrollment_number = ? AND subject = ?");
    if (!$stmt) {
        throw new Exception("Prepare statement failed");
    }

    $stmt->bind_param("ss", $enrollment, $subject);
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed");
    }

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    $response = [
        'success' => true,
        'exists' => ($data['count'] > 0)
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    if (isset($conn)) $conn->close();
    echo json_encode($response);
    exit;
}
?>