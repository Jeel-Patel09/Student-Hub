<?php

include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($name) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address.");
    }

    if (strlen($password) < 6) {
        die("Password must be at least 6 characters.");
    }

    // Check duplicate email
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        die("Email already registered.");
    }

    $check->close();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
    );

    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<h2>Registration Successful!</h2>";
        echo "<p>User data inserted successfully.</p>";
        echo "<a href='index.php'>Register another user</a>";
    } else {
        echo "Registration failed.";
    }

    $stmt->close();
    $conn->close();
}

?>