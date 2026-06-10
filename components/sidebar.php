<div class="space-y-4">
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php $suggested = getSuggestedUsers($conn, $_SESSION['user_id'], 4); ?>
        <?php if (!empty($suggested)): ?>
            <div class="card border rounded-2xl p-4 shadow-sm" style="border-color: var(--border);">
                <h3 class="text-xs font-bold text-muted uppercase tracking-wider mb-4 flex items-center gap-2">
                    <i class="bi bi-people" style="color: var(--accent);"></i>
                    Sugerencias
                </h3>
                <div class="space-y-3">
                    <?php foreach ($suggested as $u): ?>
                        <a href="?profile=<?= urlencode($u['id']) ?>" class="flex items-center gap-3 group p-2 -mx-2 rounded-xl hover:bg-zinc-800/50 transition">
                            <img src="<?= htmlspecialchars($u['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-9 h-9 rounded-full ring-2 ring-transparent group-hover:ring-indigo-500/30 transition-all">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate"><?= htmlspecialchars($u['display_name'] ?? $u['username']) ?></p>
                                <p class="text-xs text-muted truncate">@<?= htmlspecialchars($u['username']) ?></p>
                            </div>
                            <i class="bi bi-arrow-right text-muted text-xs opacity-0 group-hover:opacity-100 transition"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card border rounded-2xl p-5 text-center shadow-sm" style="border-color: var(--border);">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center mx-auto mb-3" style="background: var(--accent); opacity: 0.15;">
            <i class="bi bi-camera text-lg" style="color: var(--accent);"></i>
        </div>
        <p class="font-bold text-sm">FakeSocial</p>
        <p class="text-xs text-muted mt-1">&copy; 2026</p>
    </div>
</div>
