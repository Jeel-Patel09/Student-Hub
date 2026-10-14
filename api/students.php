<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=UTF-8');

$user = current_user();
if (!$user || $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success'=>false,'message'=>'Administrator access required.']);
    exit;
}
$method = $_SERVER['REQUEST_METHOD'];
$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) $payload = [];

if ($method !== 'GET') {
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($payload['csrf_token'] ?? null);
    if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        echo json_encode(['success'=>false,'message'=>'Security token is invalid. Refresh the page and try again.']);
        exit;
    }
}
try {
    if ($method === 'GET') {
        $rows = $pdo->query('SELECT id,student_code,name,email,course,created_at FROM students ORDER BY id DESC')->fetchAll();
        echo json_encode(['success'=>true,'students'=>$rows]); exit;
    }
    if ($method === 'POST') {
        $name = trim((string)($payload['name'] ?? ''));
        $email = strtolower(trim((string)($payload['email'] ?? '')));
        $course = trim((string)($payload['course'] ?? ''));
        if ($name === '' || mb_strlen($name)>100 || !filter_var($email,FILTER_VALIDATE_EMAIL) || mb_strlen($email)>150 || $course==='' || mb_strlen($course)>100) {
            http_response_code(422); echo json_encode(['success'=>false,'message'=>'Enter a valid name, email and course.']); exit;
        }
        $code = 'MAN' . strtoupper(bin2hex(random_bytes(4)));
        $stmt = $pdo->prepare('INSERT INTO students (student_code,name,email,course) VALUES (?,?,?,?)');
        $stmt->execute([$code,$name,$email,$course]);
        $student = ['id'=>(int)$pdo->lastInsertId(),'student_code'=>$code,'name'=>$name,'email'=>$email,'course'=>$course];
        audit($pdo,$user['id'],'STUDENT_CREATE','Created student record ID '.$student['id']);
        echo json_encode(['success'=>true,'message'=>'Student added successfully.','student'=>$student]); exit;
    }
    if ($method === 'DELETE') {
        $id = filter_var($payload['id'] ?? null,FILTER_VALIDATE_INT);
        if (!$id || $id<1) { http_response_code(422); echo json_encode(['success'=>false,'message'=>'Invalid student ID.']); exit; }
        $stmt = $pdo->prepare('DELETE FROM students WHERE id=? AND user_id IS NULL AND NOT EXISTS (SELECT 1 FROM registrations r WHERE r.student_id = students.id)');
        $stmt->execute([$id]);
        if (!$stmt->rowCount()) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Student record not found, linked to an account, or already has event registrations.']); exit; }
        audit($pdo,$user['id'],'STUDENT_DELETE','Deleted student record ID '.$id);
        echo json_encode(['success'=>true,'message'=>'Student record deleted.']); exit;
    }
    http_response_code(405);
    header('Allow: GET, POST, DELETE');
    echo json_encode(['success'=>false,'message'=>'Method not allowed.']);
} catch (PDOException $e) {
    error_log('Student API error: '.$e->getMessage());
    if ($e->getCode()==='23000') {
        http_response_code(409); echo json_encode(['success'=>false,'message'=>'That email or student code already exists.']);
    } else {
        http_response_code(500); echo json_encode(['success'=>false,'message'=>'A database error occurred.']);
    }
}
