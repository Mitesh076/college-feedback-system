<?php
$conn = new mysqli("localhost", "root", "", "student_feedback");
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed"]));
}

$data = [
    'name' => $_POST['name'],
    'enrollment' => $_POST['enrollment'],
    'year' => $_POST['year'],
    'department' => $_POST['department'],
    'semester' => $_POST['semester'],
    'section' => $_POST['section'],
    'classroom_rating' => $_POST['classroom_rating'],
    'classroom_text' => $_POST['classroom_text'],
    'lab_rating' => $_POST['lab_rating'],
    'lab_text' => $_POST['lab_text'],
    'library_rating' => $_POST['library_rating'],
    'library_text' => $_POST['library_text'],
    'wifi_rating' => $_POST['wifi_rating'],
    'wifi_text' => $_POST['wifi_text'],
    'washroom_rating' => $_POST['washroom_rating'],
    'washroom_text' => $_POST['washroom_text'],
    'parking_rating' => $_POST['parking_rating'],
    'parking_text' => $_POST['parking_text'],
    'canteen_rating' => $_POST['canteen_rating'],
    'canteen_text' => $_POST['canteen_text'],
    'security_rating' => $_POST['security_rating'],
    'security_text' => $_POST['security_text'],
    'overall_rating' => $_POST['overall_rating'],
    'general_comments' => $_POST['general_comments']
];

$sql = "INSERT INTO infrastructure_feedback (name, enrollment_number, year, department, semester, section, classroom_rating, classroom_text, lab_rating, lab_text, library_rating, library_text, wifi_rating, wifi_text, washroom_rating, washroom_text, parking_rating, parking_text, canteen_rating, canteen_text, security_rating, security_text, overall_rating, general_comments, submission_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssisssisssssssssssssssis",
    $data['name'], $data['enrollment'], $data['year'], $data['department'], $data['semester'], $data['section'],
    $data['classroom_rating'], $data['classroom_text'],
    $data['lab_rating'], $data['lab_text'],
    $data['library_rating'], $data['library_text'],
    $data['wifi_rating'], $data['wifi_text'],
    $data['washroom_rating'], $data['washroom_text'],
    $data['parking_rating'], $data['parking_text'],
    $data['canteen_rating'], $data['canteen_text'],
    $data['security_rating'], $data['security_text'],
    $data['overall_rating'], $data['general_comments']
);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>