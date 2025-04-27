<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "student_feedback");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();
    $stmt->close();

    header("Location: view-users.php");
    exit();
}

// Delete user
if (isset($_GET['delete'])) {
    $userId = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $userId");
    header("Location: view-users.php");
    exit();
}

// Fetch users by role
$adminUsers = $conn->query("SELECT * FROM users WHERE role = 'admin'");
$studentUsers = $conn->query("SELECT * FROM users WHERE role = 'student'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Users - College Feedback System</title>
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

        h2 {
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

        h3 {
            color: var(--primary-color);
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .add-user-form {
            max-width: 500px;
            width: 100%;
            background: var(--card-bg);
            padding: 2.5rem;
            border-radius: 24px;
            box-shadow: var(--shadow);
            margin: 0 auto 3rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .add-user-form::before {
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

        .add-user-form:hover::before {
            height: 100%;
        }

        .add-user-form:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .add-user-form input,
        .add-user-form select {
            width: 100%;
            padding: 0.75rem;
            margin-top: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 1rem;
            background: #f9fafb;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .add-user-form input:focus,
        .add-user-form select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 8px rgba(124, 58, 237, 0.3);
            background: #ffffff;
            outline: none;
        }

        .add-user-form select {
            appearance: none;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path fill="%231f2937" d="M7 10l5 5 5-5z"/></svg>')
                no-repeat right 0.75rem center;
            background-size: 14px;
        }

        .add-user-form button {
            margin-top: 1rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(90deg, #2563eb, #7c3aed);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: uppercase;
            transition: all 0.3s ease;
            width: 100%;
            position: relative;
            z-index: 1;
        }

        .add-user-form button:hover {
            background: linear-gradient(90deg, #1e40af, #6d28d9);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .user-table {
            width: 100%;
            max-width: 1000px;
            border-collapse: collapse;
            background: var(--card-bg);
            margin-bottom: 3rem;
            box-shadow: var(--shadow);
            border-radius: 24px;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .user-table th,
        .user-table td {
            padding: 1rem;
            border: 1px solid #d1d5db;
            text-align: center;
            color: var(--text-color);
            position: relative;
            z-index: 1;
        }

        .user-table th {
            background: linear-gradient(90deg, #2563eb, #7c3aed);
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
        }

        .user-table tr {
            transition: background 0.3s ease;
        }

        .user-table tr:hover {
            background: rgba(124, 58, 237, 0.05);
        }

        .delete-btn {
            padding: 0.5rem 1rem;
            background: linear-gradient(90deg, #dc2626, #b91c1c);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            background: linear-gradient(90deg, #b91c1c, #991b1b);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
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
            margin-top: 1rem;
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
            .user-table {
                max-width: 100%;
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
            .add-user-form {
                padding: 2rem;
            }
            .user-table th,
            .user-table td {
                padding: 0.75rem;
                font-size: 0.9rem;
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
            .add-user-form {
                padding: 1.5rem;
            }
            .add-user-form button {
                font-size: 1rem;
            }
            .user-table th,
            .user-table td {
                padding: 0.5rem;
                font-size: 0.8rem;
            }
            .delete-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
            .btn-report {
                padding: 0.75rem 2rem;
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <h2>Manage Users</h2>

    <div class="add-user-form">
        <form method="POST">
            <h3>Add New User</h3>
            <input type="text" name="username" placeholder="Username" required />
            <input type="password" name="password" placeholder="Password" required />
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="student">Student</option>
            </select>
            <button type="submit" name="add_user">Add User</button>
        </form>
    </div>

    <h3>Admin Users</h3>
    <table class="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $adminUsers->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= ucfirst($row['role']) ?></td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">
                        <button class="delete-btn">Delete</button>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h3>Student Users</h3>
    <table class="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $studentUsers->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= ucfirst($row['role']) ?></td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">
                        <button class="delete-btn">Delete</button>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div style="text-align: center;">
        <button onclick="window.location.href='admin-dashboard.php'" class="btn-report">Back to Admin Dashboard</button>
    </div>
</body>
</html>