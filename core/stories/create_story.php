<?php
session_start();
include("../db/db.php");
include("../extras/generate_uuid.php");
include("../post/images/upload_image.php");
include("../extras/csrf.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrf_token)) {
    echo json_encode(['error' => 'Error de validación']);
    exit;
}

$content = trim($_POST['content'] ?? '');
if (empty($content) && empty($_FILES['image']['tmp_name'])) {
    echo json_encode(['error' => 'Agrega una imagen a tu historia']);
    exit;
}

$user_id = $_SESSION['user_id'];
$imageUrl = uploadImage($_FILES['image'] ?? []);
$id = generateUUID();

$env = parse_ini_file(__DIR__ . '/../../.env');
$historyHours = isset($env['HISTORY_TIME']) ? max(1, (int)$env['HISTORY_TIME']) : 24;

$expiresAt = date('Y-m-d H:i:s', strtotime("+$historyHours hours"));

$sql = "INSERT INTO stories (id, user_id, image, content, created_at, expires_at) VALUES (?, ?, ?, ?, NOW(), ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $id, $user_id, $imageUrl, $content, $expiresAt);
$stmt->execute();

echo json_encode(['success' => true, 'story_id' => $id]);
exit;
