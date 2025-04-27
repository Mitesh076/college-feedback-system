<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - College Feedback System</title>
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

    .boxes-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 2rem;
      max-width: 1400px;
      width: 100%;
      margin: 0 auto;
    }

    .box {
      background: var(--card-bg);
      padding: 2.5rem;
      border-radius: 24px;
      box-shadow: var(--shadow);
      text-align: center;
      cursor: pointer;
      position: relative;
      overflow: hidden;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      backdrop-filter: blur(10px);
    }

    .box::before {
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

    .box:hover::before {
      height: 100%;
    }

    .box:hover {
      transform: translateY(-12px);
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .box h2 {
      color: var(--primary-color);
      font-size: 1.875rem;
      font-weight: 700;
      margin-bottom: 1rem;
      position: relative;
      z-index: 1;
      transition: color 0.4s ease;
    }

    .box:hover h2 {
      color: #ffffff;
    }

    .box p {
      color: var(--secondary-text);
      font-size: 1.1rem;
      line-height: 1.7;
      position: relative;
      z-index: 1;
      transition: color 0.4s ease;
    }

    .box:hover p {
      color: #e5e7eb;
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
            margin-top: 3rem;
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
      .boxes-container {
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
      }
    }

    @media (max-width: 768px) {
      .boxes-container {
        grid-template-columns: 1fr;
      }
      h1 {
        font-size: 2.5rem;
      }
      .box {
        padding: 2rem;
      }
    }

    @media (max-width: 480px) {
      body {
        padding: 1rem;
      }
      h1 {
        font-size: 2rem;
      }
      .box {
        padding: 1.5rem;
      }
      .box h2 {
        font-size: 1.625rem;
      }
      .btn-report {
        padding: 0.75rem 2rem;
        font-size: 1.1rem;
      }
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
    <div class="boxes-container">
      <!-- Box 1: View Users -->
      <div class="box" onclick="window.location.href='view-users.php'">
        <h2>Manage Users</h2>
        <p>Manage all registered users.</p>
      </div>

      <!-- Box 2: View Feedback -->
      <div class="box" onclick="window.location.href='view-feedback-options.php'">
        <h2>View Feedback</h2>
        <p>View all feedback forms filled by users.</p>
      </div>
    </div>
    <div>
      <button onclick="window.location.href='login.php'" class="btn-report">Back to login</button>
    </div>
  </div>
</body>
</html>