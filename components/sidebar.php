<div class="space-y-4">
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php $suggested = getSuggestedUsers($conn, $_SESSION['user_id'], 5); ?>
        <?php if (!empty($suggested)): ?>
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-muted">Sugerencias para ti</h3>
                </div>
                <div class="space-y-2">
                    <?php foreach ($suggested as $u): ?>
                        <a href="?profile=<?= urlencode($u['id']) ?>" class="flex items-center gap-3 group p-1.5 -mx-1.5 rounded-lg hover:bg-[var(--bg-card-hover)] transition">
                            <img src="<?= htmlspecialchars($u['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-8 h-8 rounded-full ring-2 ring-transparent group-hover:ring-[var(--accent)]/30 transition-all object-cover">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold truncate"><?= htmlspecialchars($u['display_name'] ?? $u['username']) ?></p>
                                <p class="text-[11px] text-muted truncate">@<?= htmlspecialchars($u['username']) ?></p>
                            </div>
                            <span class="text-xs font-semibold" style="color: var(--accent);">Seguir</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="text-[11px] text-muted leading-relaxed space-y-1">
        <p class="font-semibold">&copy; 2026 FakeSocial</p>
        <p>Hecho con ❤️</p>
    </div>
</div>
