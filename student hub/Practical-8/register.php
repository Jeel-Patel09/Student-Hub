<?php

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $course = $_POST["course"];
    $event_id = $_POST["event_id"];

    try {
        $check = $conn->prepare(
            "SELECT student_id FROM students WHERE email = ?"
        );

        $check->execute([$email]);

        $student = $check->fetch(PDO::FETCH_ASSOC);

        if ($student) {

            $student_id = $student["student_id"];

        } else {
            $sql = "INSERT INTO students (name, email, course)
                    VALUES (?, ?, ?)";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                $name,
                $email,
                $course
            ]);

            $student_id = $conn->lastInsertId();
        }
        $checkRegistration = $conn->prepare(
            "SELECT registration_id
             FROM registrations
             WHERE student_id = ? AND event_id = ?"
        );

        $checkRegistration->execute([
            $student_id,
            $event_id
        ]);

        if ($checkRegistration->fetch()) {

            $message = "You are already registered for this event.";

        } else {
            $sql = "INSERT INTO registrations
                    (student_id, event_id, registration_date)
                    VALUES (?, ?, CURDATE())";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                $student_id,
                $event_id
            ]);

            header("Location: index.php?success=1");
            exit;
        }

    } catch (PDOException $e) {

        $message = "Error: " . $e->getMessage();

    }
}

$eventQuery = $conn->prepare(
    "SELECT * FROM events ORDER BY event_date"
);

$eventQuery->execute();

$events = $eventQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>

    <title>Student Registration</title>
    <link rel="stylesheet" href="register.css">

</head>

<body>
<div class="register-container">

    <h1>StudentHub</h1>

    <h2>Student Registration</h2>

    <?php if ($message != "") { ?>

        <p class="message">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php } ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Course</label>
        <input type="text" name="course" required>


        <label>Select Event</label>
        <select name="event_id" required>
            <option value="">-- Select Event --</option>
            <?php foreach ($events as $event) { ?>
                <option value="<?php echo $event["event_id"]; ?>">
                    <?php echo htmlspecialchars($event["event_name"]); ?>
                </option>
            <?php } ?>
        </select>

        <button type="submit">
            Register
        </button>
    </form>
    <a class="back" href="index.php">
        View Events
    </a>

</div>
</body>
</html>