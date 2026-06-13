<?php
$userId = $_SESSION['user_id'] ?? null;

// Delete expired stories first
$conn->query("DELETE FROM stories WHERE expires_at <= NOW()");

$stories = [];
$sql = "SELECT stories.*, users.username, users.display_name, users.avatar,
               (SELECT COUNT(*) FROM story_likes WHERE story_id = stories.id) AS likes_count
        FROM stories
        JOIN users ON stories.user_id = users.id
        WHERE stories.expires_at > NOW()
        ORDER BY stories.created_at DESC
        LIMIT 50";
$result = $conn->query($sql);

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
        'likes_count' => $row['likes_count'],
        'is_liked' => $isLiked,
        'is_owner' => $isOwner ? 1 : 0,
    ];
}
?>
<div class="stories-row mb-4">
    <?php if ($userId): ?>
        <div class="flex flex-col items-center gap-1 story-create-btn cursor-pointer" style="flex-shrink: 0;">
            <div class="story-circle" style="background: var(--border);">
                <div class="story-circle-inner flex items-center justify-center" style="background: var(--bg-card);">
                    <i class="bi bi-plus text-lg" style="color: var(--accent);"></i>
                </div>
            </div>
            <span class="text-[11px] text-muted truncate w-16 text-center">Tu historia</span>
        </div>
    <?php endif; ?>

    <?php foreach ($stories as $s): ?>
        <div class="flex flex-col items-center gap-1 story-item cursor-pointer"
             data-story-id="<?= htmlspecialchars($s['id']) ?>"
             data-user-id="<?= htmlspecialchars($s['user_id']) ?>"
             data-image="<?= htmlspecialchars($s['image'] ?? '') ?>"
             data-username="<?= htmlspecialchars($s['username']) ?>"
             data-avatar="<?= htmlspecialchars($s['avatar']) ?>"
             data-likes-count="<?= $s['likes_count'] ?>"
             data-is-liked="<?= $s['is_liked'] ?>"
             data-is-owner="<?= $s['is_owner'] ?>"
             style="flex-shrink: 0;">
            <div class="story-circle">
                <div class="story-circle-inner">
                    <img src="<?= htmlspecialchars($s['avatar']) ?>" class="w-full h-full object-cover">
                </div>
            </div>
            <span class="text-[11px] text-muted truncate w-16 text-center"><?= htmlspecialchars($s['username']) ?></span>
        </div>
    <?php endforeach; ?>
</div>
