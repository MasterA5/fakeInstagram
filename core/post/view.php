<?php
require_once(__DIR__ . '/../db/db.php');
require_once(__DIR__ . '/../extras/format_datetime.php');

$postId = $_GET['post'] ?? '';
if (!$postId) {
    echo '<p class="text-center text-muted mt-10">Post no encontrado</p>';
    return;
}

$userId = $_SESSION['user_id'] ?? null;

if ($userId) {
    $stmt = $conn->prepare("SELECT posts.*, users.username, users.display_name, users.avatar,
                                   COUNT(DISTINCT likes.id) AS likes_count,
                                   EXISTS(SELECT 1 FROM likes WHERE likes.user_id = ? AND likes.post_id = posts.id) AS is_liked
                            FROM posts
                            JOIN users ON posts.user_id = users.id
                            LEFT JOIN likes ON likes.post_id = posts.id
                            WHERE posts.id = ?
                            GROUP BY posts.id");
    $stmt->bind_param("ss", $userId, $postId);
} else {
    $stmt = $conn->prepare("SELECT posts.*, users.username, users.display_name, users.avatar,
                                   COUNT(DISTINCT likes.id) AS likes_count,
                                   0 AS is_liked
                            FROM posts
                            JOIN users ON posts.user_id = users.id
                            LEFT JOIN likes ON likes.post_id = posts.id
                            WHERE posts.id = ?
                            GROUP BY posts.id");
    $stmt->bind_param("s", $postId);
}

$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    echo '<p class="text-center text-muted mt-10">Post no encontrado</p>';
    return;
}
?>

<div class="max-w-[470px] mx-auto">
    <a href="?profile=<?= urlencode($data['user_id']) ?>" class="text-sm text-muted hover:text-white inline-flex items-center gap-1 mb-4 transition">
        <i class="bi bi-arrow-left"></i> Volver al perfil
    </a>
    <?php include(__DIR__ . '/../../components/post_card.php'); ?>
</div>
