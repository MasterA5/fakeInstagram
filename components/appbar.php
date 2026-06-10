<header class="sticky top-0 z-50 glass" style="background: rgba(from var(--bg-secondary) r g b / 0.85); border-bottom: 1px solid var(--border);">
    <div class="max-w-6xl mx-auto px-4 py-3 flex justify-between items-center">

        <a href="index.php" class="text-xl font-extrabold tracking-tight hover:opacity-80 transition flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold" style="background: var(--accent); color: white;">F</span>
            <span>Fake<span style="color: var(--accent);">Social</span></span>
        </a>

        <div class="hidden sm:flex items-center gap-1 p-1 rounded-xl" style="background: var(--bg-card);">
            <a href="index.php" class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?= !isset($_GET['profile']) && !isset($_GET['settings']) && !isset($_GET['explore']) ? 'text-white shadow-sm' : 'text-muted hover:text-white' ?>" style="<?= !isset($_GET['profile']) && !isset($_GET['settings']) && !isset($_GET['explore']) ? 'background: var(--accent);' : '' ?>">
                <i class="bi bi-house-door mr-1.5"></i>Inicio
            </a>
            <a href="?explore=1" class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?= isset($_GET['explore']) ? 'text-white shadow-sm' : 'text-muted hover:text-white' ?>" style="<?= isset($_GET['explore']) ? 'background: var(--accent);' : '' ?>">
                <i class="bi bi-compass mr-1.5"></i>Explorar
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="?settings" class="px-4 py-2 rounded-lg text-sm font-medium transition-all <?= isset($_GET['settings']) ? 'text-white shadow-sm' : 'text-muted hover:text-white' ?>" style="<?= isset($_GET['settings']) ? 'background: var(--accent);' : '' ?>">
                    <i class="bi bi-gear mr-1.5"></i>Ajustes
                </a>
            <?php endif; ?>
        </div>

        <div class="flex items-center gap-3">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="?profile=<?= urlencode($_SESSION['user_id']) ?>" class="transition-transform hover:scale-105">
                    <img src="<?= htmlspecialchars($_SESSION['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-8 h-8 rounded-full ring-2 ring-transparent hover:ring-indigo-500 transition-all">
                </a>
                <a href="./core/auth/logout.php" class="btn-ghost px-3 py-1.5 rounded-lg text-sm transition text-muted hover:text-red-400 hover:border-red-400/30 flex items-center gap-1.5">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="hidden sm:inline">Salir</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
