<header class="sticky top-0 z-50" style="background: var(--bg-secondary); border-bottom: 1px solid var(--border);">
    <div class="max-w-[935px] mx-auto px-4 h-14 flex items-center justify-between">

        <a href="index.php" class="text-xl font-extrabold tracking-tight flex items-center gap-1.5 flex-shrink-0">
            <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold" style="background: var(--accent); color: white;">F</span>
            <span class="hidden sm:inline">Fake<span style="color: var(--accent);">Social</span></span>
        </a>

        <div class="hidden sm:flex items-center gap-1">
            <a href="index.php" class="px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2 <?= $page === 'feed' ? 'text-white' : 'text-muted hover:text-[var(--text-primary)]' ?>" style="<?= $page === 'feed' ? 'background: var(--accent);' : '' ?>">
                <i class="bi bi-house-door-fill"></i>
            </a>
            <a href="?explore=1" class="px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2 <?= $page === 'explore' ? 'text-white' : 'text-muted hover:text-[var(--text-primary)]' ?>" style="<?= $page === 'explore' ? 'background: var(--accent);' : '' ?>">
                <i class="bi bi-compass"></i>
            </a>
            <?php if ($logged): ?>
                <button id="upload-btn" class="px-4 py-2 rounded-lg text-sm font-medium transition-all text-muted hover:text-[var(--accent)] hover:bg-[var(--bg-card-hover)] flex items-center gap-2">
                    <i class="bi bi-plus-lg text-lg"></i>
                </button>
                <a href="?settings" class="px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2 <?= $page === 'settings' ? 'text-white' : 'text-muted hover:text-[var(--text-primary)]' ?>" style="<?= $page === 'settings' ? 'background: var(--accent);' : '' ?>">
                    <i class="bi bi-gear"></i>
                </a>
            <?php endif; ?>
        </div>

        <div class="flex items-center gap-3">
            <?php if ($logged): ?>
                <button id="upload-btn-mobile" class="sm:hidden px-2 py-2 rounded-lg text-muted hover:text-[var(--accent)] transition">
                    <i class="bi bi-plus-lg text-lg"></i>
                </button>
                <a href="?profile=<?= urlencode($_SESSION['user_id']) ?>" class="transition-transform hover:scale-105">
                    <img src="<?= htmlspecialchars($_SESSION['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-7 h-7 rounded-full ring-2 ring-transparent hover:ring-[var(--accent)] transition-all object-cover">
                </a>
                <a href="./core/auth/logout.php" class="text-muted hover:text-red-400 transition text-sm flex items-center gap-1.5 px-2 py-1.5 rounded-lg hover:bg-[var(--bg-card-hover)]">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
