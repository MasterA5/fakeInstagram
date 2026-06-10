<?php
require_once(__DIR__ . '/../db/db.php');
require_once(__DIR__ . '/../extras/format_datetime.php');

date_default_timezone_set('America/Argentina/Buenos_Aires');

if (isset($_SESSION['user_id'])) {
    $sql = "SELECT posts.*, users.username, users.display_name, users.avatar,
                   COUNT(likes.id) AS likes_count,
                   (SELECT 1 FROM likes WHERE likes.user_id = ? AND likes.post_id = posts.id) AS is_liked,
                   (SELECT 1 FROM follows WHERE follower_id = ? AND followed_id = posts.user_id) AS is_followed
            FROM posts
            JOIN users ON posts.user_id = users.id
            LEFT JOIN likes ON likes.post_id = posts.id
            GROUP BY posts.id
            ORDER BY is_followed DESC, posts.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $_SESSION['user_id'], $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT posts.*, users.username, users.display_name, users.avatar,
                   COUNT(likes.id) AS likes_count,
                   0 AS is_liked
            FROM posts
            JOIN users ON posts.user_id = users.id
            LEFT JOIN likes ON likes.post_id = posts.id
            GROUP BY posts.id
            ORDER BY posts.created_at DESC";

    $result = $conn->query($sql);
    if ($result === false) {
        $result = new mysqli_result();
    }
}
?>

<?php if (!$result || $result->num_rows === 0): ?>
    <div class="text-center py-16">
        <i class="bi bi-journal-text text-5xl text-muted"></i>
        <p class="text-muted mt-4 text-sm">No hay publicaciones aún</p>
        <?php if (isset($_SESSION['user_id'])): ?>
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
