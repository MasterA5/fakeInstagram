<?php
require_once(__DIR__ . '/../db/db.php');
session_start();

$stories = [];
$result = $conn->query("SELECT stories.*, users.username, users.display_name, users.avatar
                         FROM stories
                         JOIN users ON stories.user_id = users.id
                         WHERE stories.expires_at > NOW()
                         ORDER BY stories.created_at DESC");
while ($row = $result->fetch_assoc()) {
    $stories[] = $row;
}
?>
<div class="stories-row mb-4">
    <?php if (isset($_SESSION['user_id'])): ?>
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
        <div class="flex flex-col items-center gap-1 story-item cursor-pointer" data-story-id="<?= htmlspecialchars($s['id']) ?>" data-user-id="<?= htmlspecialchars($s['user_id']) ?>" data-image="<?= htmlspecialchars($s['image'] ?? '') ?>" data-content="<?= htmlspecialchars($s['content']) ?>" data-username="<?= htmlspecialchars($s['display_name'] ?? $s['username']) ?>" data-avatar="<?= htmlspecialchars($s['avatar']) ?>" style="flex-shrink: 0;">
            <div class="story-circle">
                <div class="story-circle-inner">
                    <img src="<?= htmlspecialchars($s['avatar']) ?>" class="w-full h-full object-cover">
                </div>
            </div>
            <span class="text-[11px] text-muted truncate w-16 text-center"><?= htmlspecialchars($s['display_name'] ?? $s['username']) ?></span>
        </div>
    <?php endforeach; ?>
</div>
