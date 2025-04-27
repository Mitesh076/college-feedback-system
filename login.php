<!DOCTYPE html>
<html>
<head>
    <title>College Feedback System - Login</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(120deg, #d1d5db, #93c5fd, #c4b5fd);
            background-size: 200% 200%;
            animation: gradientFlow 10s ease infinite;
            padding: 0;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            max-width: 450px;
            width: 90%;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .login-container:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
        }
        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2), transparent);
            transform: rotate(45deg);
            z-index: -1;
        }
        h2 {
            color: #1e3a8a;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(to right, #2563eb, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        label {
            display: block;
            text-align: left;
            color: #1f2937;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        input, select {
            width: 100%;
            padding: 14px;
            margin: 8px 0 20px 0;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            font-size: 16px;
            box-sizing: border-box;
            background: #f9fafb;
            transition: all 0.3s ease;
        }
        input:focus, select:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 10px rgba(124, 58, 237, 0.3);
            background: #ffffff;
            outline: none;
        }
        input[type="submit"] {
            background: linear-gradient(90deg, #2563eb, #7c3aed);
            color: white;
            border: none;
            padding: 16px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        input[type="submit"]:hover {
            background: linear-gradient(90deg, #1e40af, #6d28d9);
            transform: scale(1.05);
        }
        select {
            appearance: none;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path fill="%231f2937" d="M7 10l5 5 5-5z"/></svg>') no-repeat right 14px center;
            background-size: 14px;
        }
        @media (max-width: 480px) {
            .login-container {
                padding: 25px;
                width: 95%;
            }
            h2 {
                font-size: 26px;
            }
            input[type="submit"] {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>College Feedback System</h2>
    <form action="authenticate.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin">Admin</option>
            <option value="student">Student</option>
        </select>

        <input type="submit" value="Login">
    </form>
</div>

</body>
</html>