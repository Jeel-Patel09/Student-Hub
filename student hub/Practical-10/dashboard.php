<?php

require_once "auth.php";

requireLogin();

?>

<!DOCTYPE html>
<html>

<head>

    <title>Dashboard</title>

    <style>

        body {
            font-family: Arial;
            background: #f4f4f4;
            text-align: center;
        }

        .box {
            width: 500px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #aaa;
        }

        a {
            display: block;
            margin: 15px;
            padding: 10px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .logout {
            background: #dc3545;
        }

    </style>

</head>

<body>

<div class="box">

    <h1>Welcome</h1>

    <h2>
        <?php echo htmlspecialchars($_SESSION['username']); ?>
    </h2>

    <p>
        Role:
        <strong>
            <?php echo htmlspecialchars($_SESSION['role']); ?>
        </strong>
    </p>


    <?php if ($_SESSION['role'] == "admin"): ?>

        <a href="admin.php">
            Admin Dashboard
        </a>

    <?php endif; ?>


    <?php if ($_SESSION['role'] == "student"): ?>

        <a href="student.php">
            Student Dashboard
        </a>

    <?php endif; ?>


    <a href="logout.php" class="logout">
        Logout
    </a>

</div>

</body>
</html>