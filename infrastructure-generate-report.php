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

// Sanitize inputs
$department = isset($_GET['department']) && $_GET['department'] != '' ? $_GET['department'] : null;
$semester = isset($_GET['semester']) && $_GET['semester'] != '' ? $_GET['semester'] : null;
$section = isset($_GET['section']) && $_GET['section'] != '' ? $_GET['section'] : null;
$year = isset($_GET['year']) && $_GET['year'] != '' ? $_GET['year'] : null;

// Build query conditions
$where = "1=1";
$params = [];
$types = "";
$filter_description = "";

if ($department) {
    $where .= " AND department = ?";
    $params[] = $department;
    $types .= "s";
    $filter_description .= "Department: " . htmlspecialchars($department);
}

if ($semester) {
    $where .= " AND semester = ?";
    $params[] = $semester;
    $types .= "i";
    $filter_description .= ($filter_description ? ", " : "") . "Semester: " . htmlspecialchars($semester);
}

if ($section) {
    $where .= " AND section = ?";
    $params[] = $section;
    $types .= "s";
    $filter_description .= ($filter_description ? ", " : "") . "Section: " . htmlspecialchars($section);
}

if ($year) {
    $where .= " AND year = ?";
    $params[] = $year;
    $types .= "i";
    $filter_description .= ($filter_description ? ", " : "") . "Year: " . htmlspecialchars($year);
}

if (!$filter_description) {
    $filter_description = "All Feedback";
}

// Store filters in session for potential Excel export
session_start();
$_SESSION['current_filters'] = [
    'where' => $where,
    'params' => $params,
    'types' => $types
];

// Get report data
$sql = "SELECT 
            AVG(classroom_rating) as avg_classroom,
            AVG(lab_rating) as avg_lab,
            AVG(library_rating) as avg_library,
            AVG(wifi_rating) as avg_wifi,
            AVG(washroom_rating) as avg_washroom,
            AVG(parking_rating) as avg_parking,
            AVG(canteen_rating) as avg_canteen,
            AVG(security_rating) as avg_security,
            AVG(overall_rating) as avg_overall,
            COUNT(*) as total_feedbacks
        FROM infrastructure_feedback 
        WHERE $where";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$report_data = $result ? $result->fetch_assoc() : null;

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infrastructure Feedback Report - College Feedback System</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .report-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        h2 {
            color: #ffffff;
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
            line-height: 1.2;
            background: linear-gradient(to right, #2563eb, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .report-header p {
            color: var(--secondary-text);
            font-size: 1.1rem;
            line-height: 1.7;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-box {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 0;
            background: linear-gradient(45deg, #2563eb, #7c3aed);
            transition: height 0.4s ease;
            z-index: 0;
        }

        .stat-box:hover::before {
            height: 100%;
        }

        .stat-box:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
        }

        .stat-value {
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
            transition: color 0.4s ease;
        }

        .stat-box:hover .stat-value {
            color: #ffffff;
        }

        .stat-label {
            color: var(--secondary-text);
            font-size: 1rem;
            position: relative;
            z-index: 1;
            transition: color 0.4s ease;
        }

        .stat-box:hover .stat-label {
            color: #e5e7eb;
        }

        .chart-container {
            max-width: 100%;
            height: 400px;
            margin: 2rem auto;
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .message.error {
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 10px;
            text-align: center;
            background: #f8d7da;
            color: #721c24;
        }

        .button-group {
            text-align: center;
            margin-top: 2rem;
        }

        .btn-report {
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
            margin: 0.5rem;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-report:hover {
            background: linear-gradient(90deg, #1e40af, #6d28d9);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        }

        .btn-report:active {
            transform: translateY(0);
        }

        .btn-report::before {
            content: '←';
            font-size: 1.3rem;
        }

        @media (max-width: 1024px) {
            .stats {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 1.5rem;
            }
            h2 {
                font-size: 2.5rem;
            }
            .container {
                padding: 2rem;
            }
            .chart-container {
                height: 300px;
            }
            .btn-report {
                padding: 0.75rem 2rem;
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }
            h2 {
                font-size: 2rem;
            }
            .container {
                padding: 1.5rem;
            }
            .stats {
                grid-template-columns: 1fr;
            }
            .stat-box {
                padding: 1rem;
            }
            .stat-value {
                font-size: 1.5rem;
            }
            .chart-container {
                height: 250px;
            }
            .btn-report {
                padding: 0.75rem 1.5rem;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="report-container">
            <div class="report-header">
                <h2>Infrastructure Feedback Report</h2>
                <p><?php echo $filter_description; ?></p>
            </div>
            
            <?php if ($report_data && $report_data['total_feedbacks'] > 0) : ?>
                <div class="stats">
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_classroom'], 1); ?></div>
                        <div class="stat-label">Average Classroom Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_lab'], 1); ?></div>
                        <div class="stat-label">Average Computer Labs Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_library'], 1); ?></div>
                        <div class="stat-label">Average Library Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_wifi'], 1); ?></div>
                        <div class="stat-label">Average Wi-Fi Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_washroom'], 1); ?></div>
                        <div class="stat-label">Average Washrooms Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_parking'], 1); ?></div>
                        <div class="stat-label">Average Parking Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_canteen'], 1); ?></div>
                        <div class="stat-label">Average Canteen Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_security'], 1); ?></div>
                        <div class="stat-label">Average Security Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_overall'], 1); ?></div>
                        <div class="stat-label">Average Overall Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo $report_data['total_feedbacks']; ?></div>
                        <div class="stat-label">Total Feedbacks</div>
                    </div>
                </div>
                
                <div class="chart-container">
                    <canvas id="infraChart"></canvas>
                </div>
                
                <div class="button-group">
                    <button onclick="window.print()" class="btn-report">Print Report</button>
                    <a href="infrastructure-display-feedback.php" class="btn-report">Back to Feedbacks</a>
                </div>
                
                <script>
                    const ctx = document.getElementById('infraChart').getContext('2d');
                    const infraChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Classrooms', 'Computer Labs', 'Library', 'Wi-Fi', 'Washrooms', 'Parking', 'Canteen', 'Security', 'Overall'],
                            datasets: [{
                                label: 'Average Rating',
                                data: [
                                    <?php echo round($report_data['avg_classroom'], 1); ?>,
                                    <?php echo round($report_data['avg_lab'], 1); ?>,
                                    <?php echo round($report_data['avg_library'], 1); ?>,
                                    <?php echo round($report_data['avg_wifi'], 1); ?>,
                                    <?php echo round($report_data['avg_washroom'], 1); ?>,
                                    <?php echo round($report_data['avg_parking'], 1); ?>,
                                    <?php echo round($report_data['avg_canteen'], 1); ?>,
                                    <?php echo round($report_data['avg_security'], 1); ?>,
                                    <?php echo round($report_data['avg_overall'], 1); ?>
                                ],
                                backgroundColor: [
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(153, 102, 255, 0.7)',
                                    'rgba(255, 159, 64, 0.7)',
                                    'rgba(201, 203, 207, 0.7)',
                                    'rgba(46, 204, 113, 0.7)',
                                    'rgba(231, 76, 60, 0.7)'
                                ],
                                borderColor: [
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)',
                                    'rgba(201, 203, 207, 1)',
                                    'rgba(46, 204, 113, 1)',
                                    'rgba(231, 76, 60, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 10,
                                    title: {
                                        display: true,
                                        text: 'Average Rating (1-10)'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Infrastructure Categories'
                                    }
                                }
                            },
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Infrastructure Feedback Ratings Analysis',
                                    font: {
                                        size: 18
                                    }
                                },
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Avg: ' + context.raw.toFixed(1);
                                        }
                                    }
                                }
                            }
                        }
                    });
                </script>
            <?php else : ?>
                <div class="message error">
                    No feedback data available for the selected criteria.
                </div>
                <div class="button-group">
                    <a href="infrastructure-display-feedback.php" class="btn-report">Back to Feedbacks</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>