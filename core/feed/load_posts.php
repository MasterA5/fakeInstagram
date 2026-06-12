<?php
session_start();
require_once(__DIR__ . '/../db/db.php');
require_once(__DIR__ . '/../extras/csrf.php');
require_once(__DIR__ . '/../extras/format_datetime.php');

header('Content-Type: application/json');

$offset = isset($_GET['offset']) ? max(0, (int)$_GET['offset']) : 0;
$limit = 5;

if (isset($_SESSION['user_id'])) {
    $sql = "SELECT posts.*, users.username, users.display_name, users.avatar,
                   COUNT(DISTINCT likes.id) AS likes_count,
                   EXISTS(SELECT 1 FROM likes WHERE likes.user_id = ? AND likes.post_id = posts.id) AS is_liked,
                   EXISTS(SELECT 1 FROM follows WHERE follower_id = ? AND followed_id = posts.user_id) AS is_followed
            FROM posts
            JOIN users ON posts.user_id = users.id
            LEFT JOIN likes ON likes.post_id = posts.id
            GROUP BY posts.id
            ORDER BY is_followed DESC, posts.created_at DESC
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $_SESSION['user_id'], $_SESSION['user_id'], $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT posts.*, users.username, users.display_name, users.avatar,
                   COUNT(DISTINCT likes.id) AS likes_count,
                   0 AS is_liked
            FROM posts
            JOIN users ON posts.user_id = users.id
            LEFT JOIN likes ON likes.post_id = posts.id
            GROUP BY posts.id
            ORDER BY posts.created_at DESC
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
}

if (!$result || $result->num_rows === 0) {
    echo json_encode(['html' => '', 'hasMore' => false]);
    exit;
}

ob_start();
while ($data = $result->fetch_assoc()) {
    include(__DIR__ . '/../../components/post_card.php');
}
$html = ob_get_clean();

echo json_encode(['html' => $html, 'hasMore' => $result->num_rows === $limit]);
exit;
