<?php
session_start();
include("../db/db.php");
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

$follower_id = $_SESSION['user_id'];
$followed_id = $_POST['followed_id'] ?? null;

if (!$followed_id) {
    echo json_encode(['error' => 'ID no válido']);
    exit;
}
if ($followed_id === $follower_id) {
    echo json_encode(['error' => 'No puedes seguirte a ti mismo']);
    exit;
}

$check = $conn->prepare("SELECT 1 FROM follows WHERE follower_id = ? AND followed_id = ?");
$check->bind_param("ss", $follower_id, $followed_id);
$check->execute();

$isFollowing = $check->get_result()->num_rows > 0;

if ($isFollowing) {
    $stmt = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND followed_id = ?");
    $stmt->bind_param("ss", $follower_id, $followed_id);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)");
    $stmt->bind_param("ss", $follower_id, $followed_id);
    $stmt->execute();
}

$count = $conn->prepare("SELECT COUNT(*) AS cnt FROM follows WHERE followed_id = ?");
$count->bind_param("s", $followed_id);
$count->execute();
$followerCount = $count->get_result()->fetch_assoc()['cnt'];

echo json_encode([
    'following' => !$isFollowing,
    'follower_count' => (int)$followerCount
]);
exit;
