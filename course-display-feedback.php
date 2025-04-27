<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_feedback";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get subjects for dropdown
$subjects = [];
if (isset($_GET['department']) && isset($_GET['semester'])) {
    $dept = $conn->real_escape_string($_GET['department']);
    $sem = $conn->real_escape_string($_GET['semester']);
    $sql = "SELECT DISTINCT subject_name FROM course_feedback WHERE department = '$dept' AND semester = '$sem'";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row['subject_name'];
    }
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM course_feedback WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $delete_message = "Feedback deleted successfully";
    } else {
        $delete_message = "Error deleting feedback: " . $stmt->error;
    }
    $stmt->close();
}

// Build filter conditions
$where = "1=1";
$params = [];
$types = "";

if (isset($_GET['department']) && $_GET['department'] != '') {
    $where .= " AND department = ?";
    $params[] = $_GET['department'];
    $types .= "s";
}

if (isset($_GET['semester']) && $_GET['semester'] != '') {
    $where .= " AND semester = ?";
    $params[] = $_GET['semester'];
    $types .= "s";
}

if (isset($_GET['section']) && $_GET['section'] != '') {
    $where .= " AND section = ?";
    $params[] = $_GET['section'];
    $types .= "s";
}

if (isset($_GET['subject']) && $_GET['subject'] != '') {
    $where .= " AND subject_name = ?";
    $params[] = $_GET['subject'];
    $types .= "s";
}

if (isset($_GET['year']) && $_GET['year'] != '') {
    $where .= " AND YEAR(submission_date) = ?";
    $params[] = $_GET['year'];
    $types .= "s";
}

// Get filtered data
$sql = "SELECT * FROM course_feedback WHERE $where ORDER BY enrollment_number ASC";
$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Course Feedback - College Feedback System</title>
    <style>
        :root {
            --primary-color: #1e3a8a;
            --accent-color: #7c3aed;
            --hover-color: #6d28d9;
            --text-color: #1f2937;
            --secondary-text: #6b7280;
            --card-bg: rgba(255, 255, 255, 0.95);
            --shadow: 0 12px 48px rgba(0, 0, 0, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #d1d5db, #93c5fd, #c4b5fd);
            background-size: 200% 200%;
            animation: gradientFlow 12s ease infinite;
            min-height: 100vh;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-x: hidden;
        }

        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            max-width: 1400px;
            width: 100%;
            background: var(--card-bg);
            padding: 2.5rem;
            border-radius: 24px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .container:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        h1 {
            color: #ffffff;
            font-size: 3rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 3rem;
            letter-spacing: -0.02em;
            line-height: 1.2;
            background: linear-gradient(to right, #2563eb, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .filters {
            background: rgba(255, 255, 255, 0.8);
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
        }

        .filters:hover {
            transform: translateY(-4px);
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .filter-group {
            flex: 1;
            min-width: 180px;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-color);
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 1rem;
            background: #f9fafb;
            transition: all 0.3s ease;
            appearance: none;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path fill="%231f2937" d="M7 10l5 5 5-5z"/></svg>')
                no-repeat right 0.75rem center;
            background-size: 14px;
        }

        select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 8px rgba(124, 58, 237, 0.3);
            background: #ffffff;
            outline: none;
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(90deg, #2563eb, #7c3aed);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        button:hover {
            background: linear-gradient(90deg, #1e40af, #6d28d9);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #d1d5db;
        }

        th {
            background: linear-gradient(90deg, #2563eb, #7c3aed);
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            position: sticky;
            top: 0;
        }

        tr:hover {
            background: rgba(124, 58, 237, 0.05);
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .delete-btn {
            background: linear-gradient(90deg, #dc2626, #b91c1c);
            color: #ffffff;
            border: none;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: linear-gradient(90deg, #b91c1c, #991b1b);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .message {
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 10px;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .co-rating {
            display: inline-block;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            padding: 0.3rem 0.5rem;
            background: rgba(124, 58, 237, 0.1);
            border-radius: 8px;
            font-size: 0.85rem;
            color: var(--primary-color);
        }

        .button-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-report, .btn-excel {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            color: #ffffff;
            font-weight: 600;
            font-size: 1.1rem;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .btn-report {
            background: linear-gradient(90deg, #2563eb, #7c3aed);
        }

        .btn-report:hover {
            background: linear-gradient(90deg, #1e40af, #6d28d9);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .btn-excel {
            background: linear-gradient(90deg, #27ae60, #219653);
        }

        .btn-excel:hover {
            background: linear-gradient(90deg, #219653, #1e8449);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .btn-excel:disabled {
            background: linear-gradient(90deg, #95a5a6, #7f8c8d);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-report-container {
            text-align: center;
            margin-top: 2rem;
        }

        .btn-report-back {
            background: linear-gradient(90deg, #2563eb, #7c3aed);
            color: #ffffff;
            border: none;
            padding: 1rem 2.5rem;
            font-size: 1.25rem;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-report-back:hover {
            background: linear-gradient(90deg, #1e40af, #6d28d9);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }

        .btn-report-back:active {
            transform: translateY(0);
        }

        .btn-report-back::before {
            content: '←';
            font-size: 1.3rem;
        }

        p.no-records {
            text-align: center;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            color: var(--secondary-text);
            font-size: 1.1rem;
            margin-top: 2rem;
        }

        @media (max-width: 1024px) {
            .filter-row {
                gap: 0.75rem;
            }
            .filter-group {
                min-width: 150px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 1.5rem;
            }
            h1 {
                font-size: 2.5rem;
            }
            .container {
                padding: 2rem;
            }
            .filter-row {
                flex-direction: column;
            }
            .filter-group {
                min-width: 100%;
            }
            th, td {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
            .btn-report, .btn-excel {
                font-size: 1rem;
                padding: 0.5rem 1rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }
            h1 {
                font-size: 2rem;
            }
            .container {
                padding: 1.5rem;
            }
            th, td {
                padding: 0.5rem;
                font-size: 0.8rem;
            }
            .action-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
            .btn-report-back {
                padding: 0.75rem 2rem;
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Course Feedback Entries</h1>
        
        <?php if (isset($delete_message)) { ?>
            <div class="message <?php echo strpos($delete_message, 'Error') === false ? 'success' : 'error'; ?>">
                <?php echo $delete_message; ?>
            </div>
        <?php } ?>
        
        <div class="filters">
            <form method="get">
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Department</label>
                        <select name="department" id="department" onchange="this.form.submit()">
                            <option value="">All Departments</option>
                            <option value="IT" <?php if (isset($_GET['department']) && $_GET['department'] == 'IT') echo 'selected'; ?>>IT</option>
                            <option value="CE" <?php if (isset($_GET['department']) && $_GET['department'] == 'CE') echo 'selected'; ?>>CE</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Semester</label>
                        <select name="semester" id="semester" onchange="this.form.submit()">
                            <option value="">All Semesters</option>
                            <?php for ($i = 1; $i <= 8; $i++) { ?>
                                <option value="<?php echo $i; ?>" <?php if (isset($_GET['semester']) && $_GET['semester'] == $i) echo 'selected'; ?>>
                                    Sem <?php echo $i; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Section</label>
                        <select name="section">
                            <option value="">All Sections</option>
                            <option value="1" <?php if (isset($_GET['section']) && $_GET['section'] == '1') echo 'selected'; ?>>Sec 1</option>
                            <option value="2" <?php if (isset($_GET['section']) && $_GET['section'] == '2') echo 'selected'; ?>>Sec 2</option>
                            <option value="3" <?php if (isset($_GET['section']) && $_GET['section'] == '3') echo 'selected'; ?>>Sec 3</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Subject</label>
                        <select name="subject" id="subject">
                            <option value="">All Subjects</option>
                            <?php foreach ($subjects as $subject) { ?>
                                <option value="<?php echo htmlspecialchars($subject); ?>" 
                                    <?php if (isset($_GET['subject']) && $_GET['subject'] == $subject) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($subject); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Year</label>
                        <select name="year">
                            <option value="">All Years</option>
                            <?php 
                            $currentYear = date('Y');
                            for ($year = $currentYear; $year >= 2020; $year--) { ?>
                                <option value="<?php echo $year; ?>" <?php if (isset($_GET['year']) && $_GET['year'] == $year) echo 'selected'; ?>>
                                    <?php echo $year; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 15px;">
                    <button type="submit">Apply Filters</button>
                    <a href="course-display-feedback.php" style="margin-left: 10px; color: var(--accent-color); text-decoration: none;">Reset Filters</a>
                </div>
            </form>
        </div>
        
        <?php if ($result->num_rows > 0) { ?>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Enrollment</th>
                            <th>Department</th>
                            <th>Semester</th>
                            <th>Section</th>
                            <th>Subject</th>
                            <th>CO Ratings (1-10)</th>
                            <th>Comments</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['enrollment_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td><?php echo htmlspecialchars($row['semester']); ?></td>
                                <td><?php echo htmlspecialchars($row['section']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                <td>
                                    <?php 
                                    for ($i = 1; $i <= 7; $i++) {
                                        if (!empty($row["co{$i}_text"])) {
                                            echo '<div class="co-rating" title="'.htmlspecialchars($row["co{$i}_text"]).'">';
                                            echo "CO{$i}: {$row["co{$i}_rating"]}/10";
                                            echo '</div>';
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo !empty($row['comments']) ? htmlspecialchars(substr($row['comments'], 0, 50)).(strlen($row['comments']) > 50 ? '...' : '') : '-'; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($row['submission_date'])); ?></td>
                                <td>
                                    <button class="action-btn delete-btn" 
                                            onclick="if(confirm('Are you sure you want to delete this feedback?')) { 
                                                window.location.href='course-display-feedback.php?delete_id=<?php echo $row['id']; ?>' 
                                            }">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <div class="button-group">
                <form method="get" action="course-generate-report.php">
                    <?php if (isset($_GET['department'])) : ?>
                        <input type="hidden" name="department" value="<?php echo htmlspecialchars($_GET['department']); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['semester'])) : ?>
                        <input type="hidden" name="semester" value="<?php echo htmlspecialchars($_GET['semester']); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['section'])) : ?>
                        <input type="hidden" name="section" value="<?php echo htmlspecialchars($_GET['section']); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['subject'])) : ?>
                        <input type="hidden" name="subject" value="<?php echo htmlspecialchars($_GET['subject']); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['year'])) : ?>
                        <input type="hidden" name="year" value="<?php echo htmlspecialchars($_GET['year']); ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn-report" <?php echo !isset($_GET['subject']) ? 'disabled title="Please select a subject first"' : ''; ?>>
                        Generate Report
                    </button>
                </form>
                
                <form method="post" action="course-export-to-excel.php">
                    <?php if (isset($_GET['department'])) : ?>
                        <input type="hidden" name="department" value="<?php echo htmlspecialchars($_GET['department']); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['semester'])) : ?>
                        <input type="hidden" name="semester" value="<?php echo htmlspecialchars($_GET['semester']); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['section'])) : ?>
                        <input type="hidden" name="section" value="<?php echo htmlspecialchars($_GET['section']); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['subject'])) : ?>
                        <input type="hidden" name="subject" value="<?php echo htmlspecialchars($_GET['subject']); ?>">
                    <?php endif; ?>
                    <?php if (isset($_GET['year'])) : ?>
                        <input type="hidden" name="year" value="<?php echo htmlspecialchars($_GET['year']); ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn-excel">
                        Export to Excel
                    </button>
                </form>
            </div>
            
        <?php } else { ?>
            <p class="no-records">No feedback records found matching your criteria.</p>
        <?php } ?>
    </div>

    <script>
        // Auto-submit form when department or semester changes
        document.getElementById('department').addEventListener('change', function() {
            document.getElementById('subject').innerHTML = '<option value="">Loading...</option>';
            this.form.submit();
        });
        
        document.getElementById('semester').addEventListener('change', function() {
            document.getElementById('subject').innerHTML = '<option value="">Loading...</option>';
            this.form.submit();
        });
    </script>

    <div class="btn-report-container">
        <button onclick="window.location.href='view-feedback-options.php'" class="btn-report-back">Back to Dashboard</button>
    </div>
</body>
</html>

<?php 
$stmt->close();
$conn->close();
?>