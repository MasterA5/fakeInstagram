<?php
$profileId = $_GET['profile'] ?? '';
$profileData = getProfile($conn, $profileId);
if (!$profileData) {
    echo '<p class="text-center text-muted mt-10">Usuario no encontrado</p>';
    return;
}

$isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] === $profileId;
$followerCount = getFollowerCount($conn, $profileId);
$followingCount = getFollowingCount($conn, $profileId);
$isFollow = !$isOwner && isset($_SESSION['user_id']) && isFollowing($conn, $_SESSION['user_id'], $profileId);
?>
<div class="card border rounded-2xl p-6 card-hover shadow-sm animate-slide-up" style="border-color: var(--border);">
    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
        <div class="relative">
            <img src="<?= htmlspecialchars($profileData['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>"
                 class="w-24 h-24 sm:w-28 sm:h-28 rounded-full ring-4 ring-indigo-500/20">
            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-500 border-4 border-zinc-900 rounded-full"></div>
        </div>

        <div class="flex-1 text-center sm:text-left">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div>
                    <h1 class="text-2xl font-bold"><?= htmlspecialchars($profileData['display_name'] ?? $profileData['username']) ?></h1>
                    <p class="text-sm text-muted">@<?= htmlspecialchars($profileData['username']) ?></p>
                </div>

                <?php if ($isOwner): ?>
                    <a href="?settings" class="sm:ml-auto btn-ghost px-4 py-2 rounded-xl text-xs font-medium transition flex items-center gap-1.5 text-muted hover:text-white">
                        <i class="bi bi-pencil"></i> Editar perfil
                    </a>
                <?php elseif (isset($_SESSION['user_id'])): ?>
                    <form action="./core/follow/follow.php" method="POST" class="sm:ml-auto">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="followed_id" value="<?= htmlspecialchars($profileId) ?>">
                        <button type="submit" class="px-5 py-2 rounded-xl text-xs font-semibold transition-all flex items-center gap-1.5 <?= $isFollow ? 'btn-ghost text-muted hover:text-white' : 'btn-accent text-white shadow-lg shadow-indigo-500/20' ?>">
                            <i class="bi <?= $isFollow ? 'bi-person-check' : 'bi-person-plus' ?>"></i>
                            <?= $isFollow ? 'Siguiendo' : 'Seguir' ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            <?php if (!empty($profileData['bio'])): ?>
                <p class="text-sm mt-3 leading-relaxed" style="color: var(--text-secondary);"><?= htmlspecialchars($profileData['bio']) ?></p>
            <?php endif; ?>

            <div class="flex gap-6 mt-4 justify-center sm:justify-start">
                <div class="text-center sm:text-left">
                    <span class="text-xl font-bold"><?= $followerCount ?></span>
                    <span class="text-sm text-muted block">seguidores</span>
                </div>
                <div class="text-center sm:text-left">
                    <span class="text-xl font-bold"><?= $followingCount ?></span>
                    <span class="text-sm text-muted block">siguiendo</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 space-y-6">
    <?php
    $stmt = $conn->prepare("SELECT posts.*, users.username, users.display_name, users.avatar, COUNT(likes.id) AS likes_count FROM posts JOIN users ON posts.user_id = users.id LEFT JOIN likes ON likes.post_id = posts.id WHERE posts.user_id = ? GROUP BY posts.id ORDER BY posts.created_at DESC");
    $stmt->bind_param("s", $profileId);
    $stmt->execute();
    $posts = $stmt->get_result();
    ?>
    <?php if ($posts->num_rows === 0): ?>
        <div class="text-center py-16">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4" style="background: var(--bg-card);">
                <i class="bi bi-journal-text text-muted"></i>
            </div>
            <p class="text-muted text-sm">No hay publicaciones aún</p>
        </div>
    <?php else: ?>
        <?php while ($data = $posts->fetch_assoc()): ?>
            <?php include("./components/post_card.php"); ?>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
