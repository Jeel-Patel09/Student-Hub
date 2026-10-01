<?php

include "db.php";

$search = "";

if (isset($_GET["search"])) {
    $search = $_GET["search"];
}

$sql = "SELECT * FROM events ORDER BY event_date";

$stmt = $conn->prepare($sql);
$stmt->execute();

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($search != "") {

    $sql = "SELECT
                registrations.registration_id,
                students.name,
                students.email,
                students.course,
                events.event_name,
                events.event_date,
                events.venue,
                registrations.registration_date

            FROM registrations

            INNER JOIN students
            ON registrations.student_id = students.student_id

            INNER JOIN events
            ON registrations.event_id = events.event_id

            WHERE students.name LIKE ?
               OR students.email LIKE ?
               OR students.course LIKE ?
               OR events.event_name LIKE ?
               OR events.venue LIKE ?
               OR registrations.registration_date LIKE ?

            ORDER BY registrations.registration_id DESC";

    $stmt = $conn->prepare($sql);

    $value = "%" . $search . "%";

    $stmt->execute([
        $value,
        $value,
        $value,
        $value,
        $value,
        $value
    ]);

} else {

    $sql = "SELECT
                registrations.registration_id,
                students.name,
                students.email,
                students.course,
                events.event_name,
                events.event_date,
                events.venue,
                registrations.registration_date

            FROM registrations

            INNER JOIN students
            ON registrations.student_id = students.student_id

            INNER JOIN events
            ON registrations.event_id = events.event_id

            ORDER BY registrations.registration_id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
}

$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>

<head>

    <title>StudentHub</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <h1>StudentHub</h1>

    <h2>College Events</h2>

    <table>

        <tr>
            <th>Event ID</th>
            <th>Event Name</th>
            <th>Event Date</th>
            <th>Venue</th>
        </tr>

        <?php foreach ($events as $event) { ?>

        <tr>
            <td>
                <?php echo $event["event_id"]; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($event["event_name"]); ?>
            </td>

            <td>
                <?php echo $event["event_date"]; ?>
            </td>

            <td>
                <?php echo htmlspecialchars($event["venue"]); ?>
            </td>

        </tr>

        <?php } ?>

    </table>

    <div class="register-link">

        <a href="register.php">
            Register for an Event
        </a>

    </div>

    <h2 class="registered-title">
        Registered Students
    </h2>

    <form method="GET" class="search-box">

        <input
            type="text"
            name="search"
            placeholder="Search registration..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">
            Search
        </button>

        <a href="index.php">
            Show All
        </a>

    </form>

    <table>

        <tr>

            <th>Student</th>
            <th>Email</th>
            <th>Course</th>
            <th>Event</th>
            <th>Event Date</th>
            <th>Venue</th>
            <th>Registration Date</th>

        </tr>

        <?php if (count($registrations) > 0) { ?>

            <?php foreach ($registrations as $registration) { ?>

            <tr>
                <td>
                    <?php echo htmlspecialchars($registration["name"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($registration["email"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($registration["course"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($registration["event_name"]); ?>
                </td>

                <td>
                    <?php echo $registration["event_date"]; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($registration["venue"]); ?>
                </td>

                <td>
                    <?php echo $registration["registration_date"]; ?>
                </td>

            </tr>
            <?php } ?>

        <?php } else { ?>

            <tr>
                <td colspan="7">
                    No registration found.
                </td>
            </tr>

        <?php } ?>
    </table>

</div>

</body>

</html>