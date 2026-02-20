<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>CampusConnect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6ec6ff, #0d47a1);
            color: white;
            text-align: center;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 40px;
        }

        .btn {
            padding: 14px 30px;
            margin: 15px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s ease;
            color: white;
        }

        .student-btn {
            background-color: #2196f3;
        }

        .admin-btn {
            background-color: #1565c0;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }
    </style>
</head>

<body>

    <div>
        <h1>Welcome to CampusConnect</h1>

        <!-- Student -->
        <a href="/campusconnect/register.php" class="btn student-btn">
            I'm a Student
        </a>

        <br>

        <!-- Admin -->
        <a href="/campusconnect/admin_register.php" class="btn admin-btn">
            I'm an Admin
        </a>
    </div>

</body>
</html>
