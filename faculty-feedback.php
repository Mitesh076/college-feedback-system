<?php
// Enable full error reporting
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'student_feedback');

// Initialize response
$response = ['success' => false, 'message' => ''];

try {
    // 1. Verify database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // 2. Log received POST data for debugging
    file_put_contents('debug_post.log', print_r($_POST, true), FILE_APPEND);

    // 3. Validate required fields
    $required = ['name', 'enrollment', 'year', 'department', 'semester', 'section','teacher', 'subject'];
    $missing = [];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $missing[] = $field;
        }
    }
    if (!empty($missing)) {
        throw new Exception("Missing required fields: " . implode(', ', $missing));
    }

    // 4. Prepare data with proper escaping
    $data = [
        'name' => $conn->real_escape_string($_POST['name']),
        'enrollment' => $conn->real_escape_string($_POST['enrollment']),
        'year' => $conn->real_escape_string($_POST['year']),
        'department' => $conn->real_escape_string($_POST['department']),
        'semester' => $conn->real_escape_string($_POST['semester']),
        'section' => $conn->real_escape_string($_POST['section']),
        'teacher' => $conn->real_escape_string($_POST['teacher']),
        'subject' => $conn->real_escape_string($_POST['subject']),
        'subject_name' => $conn->real_escape_string($_POST['subject_name'] ?? ''),
        'comments' => $conn->real_escape_string($_POST['comments'] ?? '')
    ];

    // 5. Process CO ratings with validation
    $co_ratings = [];
    for ($i = 1; $i <= 7; $i++) {
        $rating = $_POST["co{$i}_rating"] ?? null;
        $co_ratings[$i] = [
            'text' => $conn->real_escape_string($_POST["co{$i}_text"] ?? ''),
            'rating' => (is_numeric($rating) && $rating >= 1 && $rating <= 10) ? (int)$rating : null
        ];
    }

    // 6. Check for existing feedback
    $check_sql = "SELECT COUNT(*) as count FROM faculty_feedback WHERE enrollment_number = ? AND subject = ?";
    $check = $conn->prepare($check_sql);
    if (!$check) {
        throw new Exception("Prepare check failed: " . $conn->error);
    }
    
    $check->bind_param("ss", $data['enrollment'], $data['subject']);
    if (!$check->execute()) {
        throw new Exception("Check execute failed: " . $check->error);
    }
    
    $result = $check->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        throw new Exception("Feedback already submitted for this subject");
    }

    // 7. Prepare the insert statement
    $insert_sql = "INSERT INTO faculty_feedback (
        name, enrollment_number, academic_year, department, semester, section,teacher,
        subject, subject_name, 
        co1_text, co1_rating, co2_text, co2_rating,
        co3_text, co3_rating, co4_text, co4_rating,
        co5_text, co5_rating, co6_text, co6_rating,
        co7_text, co7_rating, comments, submission_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($insert_sql);
    if (!$stmt) {
        throw new Exception("Prepare insert failed: " . $conn->error);
    }

    // 8. Bind parameters with correct types
    $bind_types = "sssssssss"; // First 8 string params
    $bind_params = [
        $data['name'], $data['enrollment'], $data['year'], $data['department'],
        $data['semester'], $data['section'],$data['teacher'], $data['subject'], $data['subject_name']
    ];

    // Add CO ratings (each is string + integer)
    for ($i = 1; $i <= 7; $i++) {
        $bind_types .= "si"; // string + integer for each CO
        $bind_params[] = $co_ratings[$i]['text'];
        $bind_params[] = $co_ratings[$i]['rating'];
    }

    // Add comments (string)
    $bind_types .= "s";
    $bind_params[] = $data['comments'];

    // 9. Dynamic parameter binding
    $stmt->bind_param($bind_types, ...$bind_params);

    // 10. Execute and verify
    if ($stmt->execute()) {
        $response = [
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'insert_id' => $stmt->insert_id
        ];
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log(date('[Y-m-d H:i:s] ') . $e->getMessage() . "\n", 3, 'feedback_errors.log');
} finally {
    if (isset($conn)) $conn->close();
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>