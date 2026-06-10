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
<div class="max-w-2xl mx-auto space-y-6 animate-slide-up">
    <div class="text-center mb-2">
        <h1 class="text-2xl font-bold">Explorar</h1>
        <p class="text-sm text-muted mt-1">Descubre nuevos usuarios</p>
    </div>

    <form method="GET" class="relative">
        <input type="hidden" name="explore" value="1">
        <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-muted"></i>
        <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar usuarios..."
            class="w-full px-4 py-3 pl-11 rounded-2xl text-sm transition shadow-sm" style="background: var(--bg-card); border: 1px solid var(--border); color: var(--text-primary);">
        <?php if (!empty($search)): ?>
            <a href="?explore=1" class="absolute right-4 top-1/2 -translate-y-1/2 text-muted hover:text-white transition">
                <i class="bi bi-x-lg text-xs"></i>
            </a>
        <?php endif; ?>
    </form>

    <?php if (!empty($search)): ?>
        <p class="text-sm text-muted">Resultados para "<strong style="color: var(--text-primary);"><?= htmlspecialchars($search) ?></strong>"</p>
    <?php endif; ?>

    <?php if (empty($users)): ?>
        <div class="text-center py-16">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4" style="background: var(--bg-card);">
                <i class="bi bi-search text-muted"></i>
            </div>
            <p class="text-muted text-sm">No se encontraron usuarios</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <?php foreach ($users as $u): ?>
                <a href="?profile=<?= urlencode($u['id']) ?>" class="card border rounded-2xl p-4 card-hover shadow-sm flex items-center gap-4 group" style="border-color: var(--border);">
                    <img src="<?= htmlspecialchars($u['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-14 h-14 rounded-full ring-2 ring-transparent group-hover:ring-indigo-500/30 transition-all">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm"><?= htmlspecialchars($u['display_name'] ?? $u['username']) ?></p>
                        <p class="text-xs text-muted truncate">@<?= htmlspecialchars($u['username']) ?></p>
                        <?php if (!empty($u['bio'])): ?>
                            <p class="text-xs text-muted truncate mt-0.5"><?= htmlspecialchars($u['bio']) ?></p>
                        <?php endif; ?>
                    </div>
                    <i class="bi bi-chevron-right text-muted group-hover:text-white transition group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
