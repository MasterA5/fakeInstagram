<?php
include_once './core/db/db.php';

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
<div class="px-4 sm:px-0 pt-4 sm:pt-0 animate-slide-up">
    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 sm:gap-12 mb-6 sm:mb-10">
        <div class="flex-shrink-0">
            <img src="<?= htmlspecialchars($profileData['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>"
                 class="w-20 h-20 sm:w-[150px] sm:h-[150px] rounded-full ring-4 ring-[var(--accent)]/20" style="object-fit: cover;">
        </div>

        <div class="flex-1 text-center sm:text-left min-w-0">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
                <h1 class="text-xl sm:text-2xl font-light"><?= htmlspecialchars($profileData['username']) ?></h1>
                <div class="flex items-center justify-center sm:justify-start gap-2">
                    <?php if ($isOwner): ?>
                        <a href="?settings&tab=profile" class="px-4 py-1.5 rounded-lg text-xs font-semibold transition" style="background: var(--bg-card); border: 1px solid var(--border); color: var(--text-primary);">
                            Editar perfil
                        </a>
                    <?php elseif (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== $profileId): ?>
                        <button class="follow-btn px-6 py-1.5 rounded-lg text-xs font-semibold transition-all <?= $isFollow ? 'bg-transparent border text-muted' : 'text-white shadow-sm' ?>" data-followed-id="<?= htmlspecialchars($profileId) ?>" data-csrf="<?= generateCsrfToken() ?>" data-following="<?= $isFollow ? '1' : '0' ?>" style="<?= $isFollow ? 'border-color: var(--border); color: var(--text-primary);' : 'background: var(--accent);' ?>">
                            <?= $isFollow ? 'Siguiendo' : 'Seguir' ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex gap-8 justify-center sm:justify-start mb-4">
                <div class="text-center sm:text-left">
                    <span class="font-semibold text-base" data-follower-count><?= $followerCount ?></span>
                    <span class="text-sm text-muted ml-1">seguidores</span>
                </div>
                <div class="text-center sm:text-left">
                    <span class="font-semibold text-base"><?= $followingCount ?></span>
                    <span class="text-sm text-muted ml-1">seguidos</span>
                </div>
            </div>

            <?php if (!empty($profileData['display_name'])): ?>
                <p class="font-semibold text-sm mb-0.5"><?= htmlspecialchars($profileData['display_name']) ?></p>
            <?php endif; ?>
            <?php if (!empty($profileData['bio'])): ?>
                <p class="text-sm" style="color: var(--text-primary);"><?= htmlspecialchars($profileData['bio']) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div style="border-top: 1px solid var(--border);">
        <div class="flex items-center justify-center gap-16 text-xs font-semibold uppercase tracking-wider py-3" style="color: var(--text-secondary);">
            <span class="flex items-center gap-1.5 py-2" style="border-top: 1px solid var(--text-primary); color: var(--text-primary); margin-top: -1px;">
                <i class="bi bi-grid-fill text-sm"></i> Publicaciones
            </span>
        </div>
    </div>

    <?php
    $stmt = $conn->prepare("SELECT id, image FROM posts WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $profileId);
    $stmt->execute();
    $posts = $stmt->get_result();
    $hasPosts = $posts->num_rows > 0;
    ?>

    <?php if ($hasPosts): ?>
        <div class="profile-grid">
            <?php while ($p = $posts->fetch_assoc()): ?>
                <a href="?post=<?= urlencode($p['id']) ?>" class="profile-grid-item card-hover">
                    <?php if (!empty($p['image'])): ?>
                        <img src="<?= htmlspecialchars($p['image']) ?>" loading="lazy">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-muted text-2xl" style="background: var(--bg-card);">
                            <i class="bi bi-journal-text"></i>
                        </div>
                    <?php endif; ?>
                </a>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-16">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 border-2" style="border-color: var(--border);">
                <i class="bi bi-camera text-muted"></i>
            </div>
            <p class="font-semibold text-lg">No hay publicaciones</p>
            <p class="text-muted text-sm mt-1">Cuando <?= $isOwner ? 'publiques' : 'el usuario publique' ?> algo, aparecerá aquí.</p>
        </div>
    <?php endif; ?>
</div>
