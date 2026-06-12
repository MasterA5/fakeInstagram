<?php
require_once(__DIR__ . '/../db/db.php');
require_once(__DIR__ . '/../extras/format_datetime.php');

date_default_timezone_set('America/Argentina/Buenos_Aires');

$limit = 5;
$userId = $_SESSION['user_id'] ?? null;

if ($userId) {
    $countSql = "SELECT COUNT(*) AS total FROM posts";
    $totalResult = $conn->query($countSql);
    $totalPosts = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;

    $sql = "SELECT posts.*, users.username, users.display_name, users.avatar,
                   COUNT(DISTINCT likes.id) AS likes_count,
                   EXISTS(SELECT 1 FROM likes WHERE likes.user_id = ? AND likes.post_id = posts.id) AS is_liked,
                   EXISTS(SELECT 1 FROM follows WHERE follower_id = ? AND followed_id = posts.user_id) AS is_followed
            FROM posts
            JOIN users ON posts.user_id = users.id
            LEFT JOIN likes ON likes.post_id = posts.id
            GROUP BY posts.id
            ORDER BY is_followed DESC, posts.created_at DESC
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $userId, $userId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $countSql = "SELECT COUNT(*) AS total FROM posts";
    $totalResult = $conn->query($countSql);
    $totalPosts = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;

    $sql = "SELECT posts.*, users.username, users.display_name, users.avatar,
                   COUNT(DISTINCT likes.id) AS likes_count,
                   0 AS is_liked
            FROM posts
            JOIN users ON posts.user_id = users.id
            LEFT JOIN likes ON likes.post_id = posts.id
            GROUP BY posts.id
            ORDER BY posts.created_at DESC
            LIMIT ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<div id="feed-container">
<?php if (!$result || $result->num_rows === 0): ?>
    <div class="text-center py-16">
        <i class="bi bi-journal-text text-5xl text-muted"></i>
        <p class="text-muted mt-4 text-sm">No hay publicaciones aún</p>
        <?php if ($userId): ?>
            <p class="text-muted text-xs mt-1">¡Sé el primero en publicar!</p>
        <?php else: ?>
            <a href="?mode=register" class="btn-accent inline-block mt-3 px-4 py-2 rounded-lg text-sm text-white font-semibold transition">Regístrate</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <?php while ($data = $result->fetch_assoc()): ?>
        <?php include("./components/post_card.php"); ?>
    <?php endwhile; ?>
<?php endif; ?>
</div>

<?php if ($totalPosts > $limit): ?>
<div id="feed-loader" class="text-center py-6 hidden">
    <div class="w-8 h-8 border-2 border-[var(--accent)] border-t-transparent rounded-full animate-spin mx-auto"></div>
</div>
<?php endif; ?>
