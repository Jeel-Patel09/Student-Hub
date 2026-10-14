<?php

require_once "auth.php";

requireRole("student");

?>

<!DOCTYPE html>
<html>

<head>

    <title>Student Dashboard</title>

    <style>

        body {
            font-family: Arial;
            background: #eee;
        }

        .box {
            width: 500px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 0 10px #aaa;
        }

        a {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

    </style>

</head>

<body>

<div class="box">

    <h1>Student Dashboard</h1>

    <p>
        Welcome Student,
        <?php echo htmlspecialchars($_SESSION['username']); ?>
    </p>

    <p>
        You have student access.
    </p>

    <a href="dashboard.php">
        Back
    </a>

    <a href="logout.php">
        Logout
    </a>

</div>

</body>

</html>