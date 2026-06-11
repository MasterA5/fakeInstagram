<?php
session_start();
include("../../db/db.php");

header('Content-Type: application/json');

$post_id = $_GET['post_id'] ?? null;
if (!$post_id) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("SELECT comments.*, users.username, users.avatar FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at ASC");
$stmt->bind_param("s", $post_id);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($comments);
exit;
