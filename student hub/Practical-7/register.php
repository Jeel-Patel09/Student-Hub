<?php

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $confirmPassword = trim($_POST["confirmPassword"] ?? "");

    if ($name == "" || $email == "" || $password == "" || $confirmPassword == "") {

        $message = "Please fill all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Invalid email address.";

    } elseif ($password != $confirmPassword) {

        $message = "Passwords do not match.";

    } else {

        $name = htmlspecialchars($name);
        $email = htmlspecialchars($email);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // CSV
        $csvFile = "registrations.csv";
        $file = fopen($csvFile, "a");

        if (filesize($csvFile) == 0) {
            fputcsv($file, ["Name", "Email", "Password"]);
        }

        fputcsv($file, [$name, $email, $hashedPassword]);
        fclose($file);

        // JSON
        $jsonFile = "registrations.json";

        if (file_exists($jsonFile)) {
            $data = json_decode(file_get_contents($jsonFile), true);

            if (!is_array($data)) {
                $data = [];
            }
        } else {
            $data = [];
        }

        $data[] = [
            "name" => $name,
            "email" => $email,
            "password" => $hashedPassword
        ];

        file_put_contents(
            $jsonFile,
            json_encode($data, JSON_PRETTY_PRINT)
        );

        $message = "Registration successful and data saved.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - StudentHub</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
<header>
    <div class="logo">
        <h1>StudentHub</h1>
    </div>
    <nav>
        <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="dashboard.html">Dashboard</a></li>
            <li><a href="profile_management.html">Profile</a></li>
            <li><a href="event.html">Events</a></li>
            <li><a href="contact.html">Contact</a></li>
            <li><a href="FAQ.html">FAQ</a></li>
            <li><a href="login.html">Login</a></li>
        </ul>
    </nav>
</header>

<div class="register">
    <h1>Student Registration</h1>
    <?php
    if ($message != "") {
        echo "<p class='success'>$message</p>";
    }
    ?>

    <form action="" method="POST">

    <label>Full Name</label>
    <input type="text" name="name" placeholder="Enter Full Name" required>

    <label>Email</label>
    <input type="email" name="email" placeholder="Enter Email" required>

    <label>Password</label>
    <input type="password" name="password" placeholder="Enter Password" required>

    <label>Confirm Password</label>
    <input type="password" name="confirmPassword" placeholder="Confirm Password" required>

    <button type="submit">Register</button>

</form>
</div>

<footer>
    <p>&copy; 2026 StudentHub Portal. All Rights Reserved.</p>
</footer>
</body>
</html>