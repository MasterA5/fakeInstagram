<?php
session_start();
include("../db/db.php");
include("../extras/generate_uuid.php");
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

$userId = $_SESSION['user_id'];
$storyId = $_POST['story_id'] ?? null;

if (!$storyId) {
    echo json_encode(['error' => 'ID no válido']);
    exit;
}

// Check if story exists and is not owned by user
$check = $conn->prepare("SELECT user_id FROM stories WHERE id = ?");
$check->bind_param("s", $storyId);
$check->execute();
$story = $check->get_result()->fetch_assoc();

if (!$story) {
    echo json_encode(['error' => 'Historia no encontrada']);
    exit;
}

$isOwner = $story['user_id'] === $userId;

$likeCheck = $conn->prepare("SELECT 1 FROM story_likes WHERE story_id = ? AND user_id = ?");
$likeCheck->bind_param("ss", $storyId, $userId);
$likeCheck->execute();
$alreadyLiked = $likeCheck->get_result()->num_rows > 0;

if ($alreadyLiked) {
    $stmt = $conn->prepare("DELETE FROM story_likes WHERE story_id = ? AND user_id = ?");
    $stmt->bind_param("ss", $storyId, $userId);
    $stmt->execute();
    $liked = false;
} else {
    $id = generateUUID();
    $stmt = $conn->prepare("INSERT INTO story_likes (id, story_id, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $id, $storyId, $userId);
    $stmt->execute();
    $liked = true;
}

$count = $conn->prepare("SELECT COUNT(*) AS cnt FROM story_likes WHERE story_id = ?");
$count->bind_param("s", $storyId);
$count->execute();
$likesCount = $count->get_result()->fetch_assoc()['cnt'];

echo json_encode([
    'liked' => $liked,
    'count' => (int)$likesCount,
    'is_owner' => $isOwner
]);
exit;
