<?php
$userId = $_SESSION['user_id'] ?? null;

// Delete expired stories first
$conn->query("DELETE FROM stories WHERE expires_at <= NOW()");

// Group stories by user, keep the most recent as display
$sql = "SELECT s1.*, users.username, users.display_name, users.avatar,
               (SELECT COUNT(*) FROM story_likes WHERE story_id = s1.id) AS likes_count,
               (SELECT COUNT(*) FROM stories s2 WHERE s2.user_id = s1.user_id AND s2.expires_at > NOW()) AS total_stories
        FROM stories s1
        JOIN users ON s1.user_id = users.id
        WHERE s1.expires_at > NOW()
        AND s1.id = (
            SELECT s3.id FROM stories s3
            WHERE s3.user_id = s1.user_id AND s3.expires_at > NOW()
            ORDER BY s3.created_at DESC LIMIT 1
        )
        ORDER BY s1.created_at DESC
        LIMIT 50";
$result = $conn->query($sql);

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
        'likes_count' => $row['likes_count'],
        'is_liked' => $isLiked,
        'is_owner' => $isOwner ? 1 : 0,
        'total' => $row['total_stories'],
    ];
}
?>
<div class="stories-wrapper mb-4"><div class="stories-fade-left"></div><div class="stories-fade-right"></div><div class="stories-row">
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
             data-user-id="<?= htmlspecialchars($s['user_id']) ?>"
             data-username="<?= htmlspecialchars($s['username']) ?>"
             data-avatar="<?= htmlspecialchars($s['avatar']) ?>"
             data-is-owner="<?= $s['is_owner'] ?>"
             style="flex-shrink: 0;">
            <div class="story-circle">
                <div class="story-circle-inner">
                    <img src="<?= htmlspecialchars($s['avatar']) ?>" class="w-full h-full object-cover">
                </div>
                <?php if ($s['total'] > 1): ?>
                    <div class="absolute -bottom-0.5 right-0 w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-bold" style="background: var(--accent); color: white;"><?= $s['total'] ?></div>
                <?php endif; ?>
            </div>
            <span class="text-[11px] text-muted truncate w-16 text-center"><?= htmlspecialchars($s['username']) ?></span>
        </div>
    <?php endforeach; ?>
</div></div>
