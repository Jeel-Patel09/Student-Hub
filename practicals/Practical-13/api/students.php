<?php
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store");

require_once __DIR__ . "/db.php";

function respond(int $status, array $data): void {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function readInput(): array {
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        respond(400, [
            "success" => false,
            "message" => "Invalid JSON request body."
        ]);
    }

    return $data;
}

function validateStudent(array $data): array {
    $name = trim((string)($data["name"] ?? ""));
    $email = trim((string)($data["email"] ?? ""));
    $course = trim((string)($data["course"] ?? ""));

    if ($name === "" || $email === "" || $course === "") {
        respond(422, [
            "success" => false,
            "message" => "All fields are required."
        ]);
    }

    if (mb_strlen($name) > 100 || mb_strlen($course) > 100) {
        respond(422, [
            "success" => false,
            "message" => "Name and course must be at most 100 characters."
        ]);
    }

    if (strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond(422, [
            "success" => false,
            "message" => "Enter a valid email address."
        ]);
    }

    return [$name, $email, $course];
}

function validId($value): bool {
    return filter_var($value, FILTER_VALIDATE_INT) !== false
        && (int)$value > 0;
}

$method = $_SERVER["REQUEST_METHOD"];

try {
    switch ($method) {
        case "GET":
            $stmt = $pdo->query(
                "SELECT id, name, email, course, created_at
                 FROM students ORDER BY id DESC"
            );

            respond(200, [
                "success" => true,
                "data" => $stmt->fetchAll()
            ]);
            break;

        case "POST":
            $data = readInput();
            [$name, $email, $course] = validateStudent($data);

            $stmt = $pdo->prepare(
                "INSERT INTO students (name, email, course) VALUES (?, ?, ?)"
            );
            $stmt->execute([$name, $email, $course]);

            respond(201, [
                "success" => true,
                "message" => "Student added successfully.",
                "id" => (int)$pdo->lastInsertId()
            ]);
            break;

        case "PUT":
            $data = readInput();
            $id = $data["id"] ?? null;

            if (!validId($id)) {
                respond(422, [
                    "success" => false,
                    "message" => "A valid student ID is required."
                ]);
            }

            [$name, $email, $course] = validateStudent($data);

            $stmt = $pdo->prepare(
                "UPDATE students
                 SET name = ?, email = ?, course = ?
                 WHERE id = ?"
            );
            $stmt->execute([$name, $email, $course, (int)$id]);

            if ($stmt->rowCount() === 0) {
                $check = $pdo->prepare("SELECT id FROM students WHERE id = ?");
                $check->execute([(int)$id]);

                if (!$check->fetch()) {
                    respond(404, [
                        "success" => false,
                        "message" => "Student not found."
                    ]);
                }
            }

            respond(200, [
                "success" => true,
                "message" => "Student updated successfully."
            ]);
            break;

        case "DELETE":
            $data = readInput();
            $id = $data["id"] ?? null;

            if (!validId($id)) {
                respond(422, [
                    "success" => false,
                    "message" => "A valid student ID is required."
                ]);
            }

            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([(int)$id]);

            if ($stmt->rowCount() === 0) {
                respond(404, [
                    "success" => false,
                    "message" => "Student not found."
                ]);
            }

            respond(200, [
                "success" => true,
                "message" => "Student deleted successfully."
            ]);
            break;

        default:
            header("Allow: GET, POST, PUT, DELETE");
            respond(405, [
                "success" => false,
                "message" => "HTTP method not allowed."
            ]);
    }
} catch (PDOException $e) {
    error_log($e->getMessage());

    if ($e->getCode() === "23000") {
        respond(409, [
            "success" => false,
            "message" => "This email address already exists."
        ]);
    }

    respond(500, [
        "success" => false,
        "message" => "A database error occurred."
    ]);
}
