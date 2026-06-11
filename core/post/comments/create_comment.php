<?php
session_start();
include("../../db/db.php");
include("../../extras/generate_uuid.php");
include("../../extras/csrf.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrf_token)) {
    echo json_encode(['error' => 'Error de validación']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;
$content = trim($_POST['content'] ?? '');

if (!$post_id || empty($content)) {
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

$id = generateUUID();

$sql = "INSERT INTO comments (id, user_id, post_id, content) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $id, $user_id, $post_id, $content);
$stmt->execute();

// Fetch comment with user data for response
$q = $conn->prepare("SELECT comments.*, users.username, users.avatar FROM comments JOIN users ON comments.user_id = users.id WHERE comments.id = ?");
$q->bind_param("s", $id);
$q->execute();
$comment = $q->get_result()->fetch_assoc();

if (!$comment) {
    echo json_encode(['error' => 'Error al obtener el comentario']);
    exit;
}

echo json_encode([
    'success' => true,
    'id' => $comment['id'],
    'user_id' => $comment['user_id'],
    'username' => $comment['username'],
    'avatar' => $comment['avatar'],
    'content' => $comment['content'],
    'created_at' => $comment['created_at'],
]);
exit;
