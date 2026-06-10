<?php
$search = trim($_GET['q'] ?? '');

if (!empty($search)) {
    $like = "%$search%";
    $stmt = $conn->prepare("SELECT id, username, display_name, avatar, bio FROM users WHERE username LIKE ? OR display_name LIKE ? LIMIT 20");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $users = [];
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("SELECT id, username, display_name, avatar FROM users WHERE id != ? ORDER BY RAND() LIMIT 12");
        $stmt->bind_param("s", $_SESSION['user_id']);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $result = $conn->query("SELECT id, username, display_name, avatar FROM users ORDER BY RAND() LIMIT 12");
        if ($result) $users = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
<div class="px-4 sm:px-0 pt-4 sm:pt-0 space-y-4 animate-slide-up">
    <form method="GET" class="relative max-w-[600px] mx-auto">
        <input type="hidden" name="explore" value="1">
        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-muted text-sm"></i>
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar"
            class="w-full px-4 py-2.5 pl-10 rounded-lg text-sm transition" style="background: var(--bg-card); border: 1px solid var(--border); color: var(--text-primary);">
        <?php if (!empty($search)): ?>
            <a href="?explore=1" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-white transition">
                <i class="bi bi-x-lg text-xs"></i>
            </a>
        <?php endif; ?>
    </form>

    <?php if (!empty($search)): ?>
        <p class="text-sm text-muted px-1">Resultados para "<strong style="color: var(--text-primary);"><?= htmlspecialchars($search) ?></strong>"</p>
    <?php endif; ?>

    <?php if (empty($users)): ?>
        <div class="text-center py-16">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl mx-auto mb-4 border-2" style="border-color: var(--border);">
                <i class="bi bi-search text-muted"></i>
            </div>
            <p class="text-muted text-sm">No se encontraron usuarios</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <?php foreach ($users as $u): ?>
                <a href="?profile=<?= urlencode($u['id']) ?>" class="flex flex-col items-center text-center p-4 rounded-xl card-hover transition" style="background: var(--bg-card); border: 1px solid var(--border);">
                    <img src="<?= htmlspecialchars($u['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-16 h-16 rounded-full mb-2 ring-2 ring-transparent hover:ring-[var(--accent)]/30 transition-all">
                    <p class="font-semibold text-sm truncate w-full"><?= htmlspecialchars($u['display_name'] ?? $u['username']) ?></p>
                    <p class="text-xs text-muted truncate w-full">@<?= htmlspecialchars($u['username']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
