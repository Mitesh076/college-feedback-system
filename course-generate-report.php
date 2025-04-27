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

// Validate required parameters
if (empty($_GET['subject'])) {
    header("Location: course-display-feedback.php?error=Please select a subject to generate the report");
    exit();
}

// Sanitize inputs
$subject = $conn->real_escape_string($_GET['subject']);
$department = isset($_GET['department']) ? $conn->real_escape_string($_GET['department']) : null;
$semester = isset($_GET['semester']) ? $conn->real_escape_string($_GET['semester']) : null;
$section = isset($_GET['section']) ? $conn->real_escape_string($_GET['section']) : null;

// Build query conditions
$where = "subject_name = '$subject'";
$filter_description = "Subject: " . htmlspecialchars($subject);

if ($department) {
    $where .= " AND department = '$department'";
    $filter_description .= ", Department: " . htmlspecialchars($department);
}

if ($semester) {
    $where .= " AND semester = '$semester'";
    $filter_description .= ", Semester: " . htmlspecialchars($semester);
}

if ($section) {
    $where .= " AND section = '$section'";
    $filter_description .= ", Section: " . htmlspecialchars($section);
}

// Get report data
$sql = "SELECT 
            AVG(co1_rating) as avg_co1,
            AVG(co2_rating) as avg_co2,
            AVG(co3_rating) as avg_co3,
            AVG(co4_rating) as avg_co4,
            AVG(co5_rating) as avg_co5,
            AVG(co6_rating) as avg_co6,
            AVG(co7_rating) as avg_co7,
            COUNT(*) as total_feedbacks
        FROM course_feedback 
        WHERE $where";

$result = $conn->query($sql);
$report_data = $result ? $result->fetch_assoc() : null;
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Outcome Report - College Feedback System</title>
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

        h3 {
            color: var(--primary-color);
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
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
            h3 {
                font-size: 1.625rem;
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
            h3 {
                font-size: 1.5rem;
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
                <h2>Course Outcome Report</h2>
                <h3><?php echo htmlspecialchars($subject); ?></h3>
                <p><?php echo $filter_description; ?></p>
            </div>
            
            <?php if ($report_data && $report_data['total_feedbacks'] > 0) : ?>
                <div class="stats">
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_co1'], 1); ?></div>
                        <div class="stat-label">Average CO1 Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_co2'], 1); ?></div>
                        <div class="stat-label">Average CO2 Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_co3'], 1); ?></div>
                        <div class="stat-label">Average CO3 Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_co4'], 1); ?></div>
                        <div class="stat-label">Average CO4 Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_co5'], 1); ?></div>
                        <div class="stat-label">Average CO5 Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_co6'], 1); ?></div>
                        <div class="stat-label">Average CO6 Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo round($report_data['avg_co7'], 1); ?></div>
                        <div class="stat-label">Average CO7 Rating</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value"><?php echo $report_data['total_feedbacks']; ?></div>
                        <div class="stat-label">Total Feedbacks</div>
                    </div>
                </div>
                
                <div class="chart-container">
                    <canvas id="coChart"></canvas>
                </div>
                
                <div class="button-group">
                    <button onclick="window.print()" class="btn-report">Print Report</button>
                    <a href="course-display-feedback.php" class="btn-report">Back to Feedbacks</a>
                </div>
                
                <script>
                    const ctx = document.getElementById('coChart').getContext('2d');
                    const coChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['CO1', 'CO2', 'CO3', 'CO4', 'CO5', 'CO6', 'CO7'],
                            datasets: [{
                                label: 'Average Rating',
                                data: [
                                    <?php echo round($report_data['avg_co1'], 1); ?>,
                                    <?php echo round($report_data['avg_co2'], 1); ?>,
                                    <?php echo round($report_data['avg_co3'], 1); ?>,
                                    <?php echo round($report_data['avg_co4'], 1); ?>,
                                    <?php echo round($report_data['avg_co5'], 1); ?>,
                                    <?php echo round($report_data['avg_co6'], 1); ?>,
                                    <?php echo round($report_data['avg_co7'], 1); ?>
                                ],
                                backgroundColor: [
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(153, 102, 255, 0.7)',
                                    'rgba(255, 159, 64, 0.7)',
                                    'rgba(201, 203, 207, 0.7)'
                                ],
                                borderColor: [
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)',
                                    'rgba(201, 203, 207, 1)'
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
                                        text: 'Course Outcomes'
                                    }
                                }
                            },
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Course Outcome Ratings Analysis',
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
                    <a href="course-display-feedback.php" class="btn-report">Back to Feedbacks</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>