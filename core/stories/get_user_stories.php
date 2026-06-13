<?php
session_start();
include("../db/db.php");

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
$targetUserId = $_GET['user_id'] ?? '';

if (!$targetUserId) {
    echo json_encode(['error' => 'No user_id']);
    exit;
}

$sql = "SELECT stories.*, users.username, users.display_name, users.avatar,
               (SELECT COUNT(*) FROM story_likes WHERE story_id = stories.id) AS likes_count
        FROM stories
        JOIN users ON stories.user_id = users.id
        WHERE stories.user_id = ? AND stories.expires_at > NOW()
        ORDER BY stories.created_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $targetUserId);
$stmt->execute();
$result = $stmt->get_result();

$stories = [];
while ($row = $result->fetch_assoc()) {
    $isOwner = $userId && $row['user_id'] === $userId;
    $isLiked = 0;
    if ($userId && !$isOwner) {
        $lk = $conn->prepare("SELECT 1 FROM story_likes WHERE story_id = ? AND user_id = ?");
        $lk->bind_param("ss", $row['id'], $userId);
        $lk->execute();
        $isLiked = $lk->get_result()->num_rows > 0 ? 1 : 0;
    }
    $stories[] = [
        'id' => $row['id'],
        'user_id' => $row['user_id'],
        'image' => $row['image'],
        'username' => $row['display_name'] ?? $row['username'],
        'avatar' => $row['avatar'],
        'likes_count' => (int)$row['likes_count'],
        'is_liked' => $isLiked,
        'is_owner' => $isOwner,
    ];
}

echo json_encode($stories);
exit;
