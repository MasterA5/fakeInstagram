<?php
session_start();
include("../db/db.php");

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;

// Delete expired stories first
$conn->query("DELETE FROM stories WHERE expires_at <= NOW()");

$sql = "SELECT stories.*, users.username, users.display_name, users.avatar
        FROM stories
        JOIN users ON stories.user_id = users.id
        WHERE stories.expires_at > NOW()
        ORDER BY stories.created_at DESC
        LIMIT 50";

$result = $conn->query($sql);
$stories = [];

while ($row = $result->fetch_assoc()) {
    $row['is_owner'] = $userId && $row['user_id'] === $userId;
    $stories[] = $row;
}

echo json_encode($stories);
exit;
