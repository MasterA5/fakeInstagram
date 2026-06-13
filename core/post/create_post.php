<?php
session_start();
include("../db/db.php");
include("../extras/generate_uuid.php");
include("./images/upload_image.php");
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

if (empty($content)) {
    echo json_encode(['error' => 'Contenido vacío']);
    exit;
}

if (strlen($content) > 2000) {
    echo json_encode(['error' => 'El contenido es demasiado largo']);
    exit;
}

$user_id = $_SESSION['user_id'];
$imageUrl = uploadImage($_FILES['image'] ?? []);
$id = generateUUID();
$isStory = isset($_POST['is_story']) && $_POST['is_story'] === '1';

if ($isStory) {
    $env = parse_ini_file(__DIR__ . '/../../.env');
    $historyHours = isset($env['HISTORY_TIME']) ? max(1, (int)$env['HISTORY_TIME']) : 24;
    $expiresAt = date('Y-m-d H:i:s', strtotime("+$historyHours hours"));

    $sql = "INSERT INTO stories (id, user_id, image, content, created_at, expires_at) VALUES (?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $id, $user_id, $imageUrl, $content, $expiresAt);
    $stmt->execute();

    echo json_encode(['success' => true, 'story_id' => $id, 'is_story' => true]);
    exit;
}

// Regular post
$sql = "INSERT INTO posts (id, user_id, image, content) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $id, $user_id, $imageUrl, $content);
$stmt->execute();

$query = $conn->prepare("SELECT posts.*, users.username, users.display_name, users.avatar,
                          0 AS likes_count, 0 AS is_liked
                          FROM posts
                          JOIN users ON posts.user_id = users.id
                          WHERE posts.id = ?");
$query->bind_param("s", $id);
$query->execute();
$data = $query->get_result()->fetch_assoc();

ob_start();
include("../../components/post_card.php");
$html = ob_get_clean();

echo json_encode(['success' => true, 'post_id' => $id, 'html' => $html]);
exit;
