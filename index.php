<?php
session_start();
include("./core/db/db.php");
include("./core/extras/csrf.php");
include("./core/extras/theme.php");
include("./core/profile/view.php");

$logged = isset($_SESSION['user_id']);

$activeTheme = 'dark';
if ($logged) {
    $activeTheme = getUserTheme($conn, $_SESSION['user_id']);
}
$themeCSS = applyTheme($activeTheme);

$page = 'feed';
if (isset($_GET['profile'])) {
    $page = 'profile';
} elseif (isset($_GET['settings'])) {
    $page = 'settings';
} elseif (isset($_GET['explore'])) {
    $page = 'explore';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FakeSocial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        <?= $themeCSS ?>
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); -webkit-font-smoothing: antialiased; }
        .card { background: var(--bg-card); border-color: var(--border); }
        .text-muted { color: var(--text-secondary); }
        .btn-accent { background: var(--accent); }
        .btn-accent:hover { background: var(--accent-hover); }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.3); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .img-zoom { overflow: hidden; }
        .img-zoom img { transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .img-zoom:hover img { transform: scale(1.05); }
        .glass { backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
        .gradient-border { position: relative; }
        .gradient-border::before { content: ''; position: absolute; inset: 0; border-radius: inherit; padding: 1px; background: linear-gradient(135deg, var(--accent), transparent 50%, var(--accent)); -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0); -webkit-mask-composite: xor; mask-composite: exclude; pointer-events: none; }
        .btn-ghost { background: transparent; border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--bg-card-hover); }
        .skeleton { background: linear-gradient(90deg, var(--bg-card) 25%, var(--bg-card-hover) 50%, var(--bg-card) 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes heartBeat { 0% { transform: scale(1); } 50% { transform: scale(1.3); } 100% { transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.4s ease-out; }
        .animate-slide-up { animation: slideUp 0.5s ease-out; }
        .heart-animate:active i { animation: heartBeat 0.3s ease-in-out; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-secondary); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-secondary); }
        input:focus-visible, textarea:focus-visible, button:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        * { transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease; }
    </style>
</head>
<body class="min-h-screen">

    <?php include("./components/appbar.php"); ?>

    <div class="max-w-6xl mx-auto p-4 pt-6 animate-fade-in">
        <?php if ($page === 'profile'): ?>
            <div class="max-w-2xl mx-auto">
                <?php include("./components/profile_header.php"); ?>
            </div>
        <?php elseif ($page === 'settings' && $logged): ?>
            <?php include("./core/profile/settings.php"); ?>
        <?php elseif ($page === 'explore'): ?>
            <?php include("./core/feed/explore.php"); ?>
        <?php else: ?>
            <div class="flex gap-6 max-w-5xl mx-auto">
                <div class="flex-1 space-y-6">
                    <?php if ($logged): ?>
                        <?php include("./components/upload_card.php"); ?>
                        <?php include("./core/feed/test.php"); ?>
                    <?php else: ?>
                        <?php include("./core/forms/forms.php"); ?>
                    <?php endif; ?>
                </div>
                <div class="hidden lg:block w-72 flex-shrink-0 space-y-4">
                    <?php include("./components/sidebar.php"); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <nav class="sm:hidden fixed bottom-0 left-0 right-0 z-50 glass" style="background: rgba(from var(--bg-secondary) r g b / 0.9); border-top: 1px solid var(--border);">
        <div class="flex justify-around py-2">
            <a href="index.php" class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs transition-all <?= !isset($_GET['profile']) && !isset($_GET['settings']) && !isset($_GET['explore']) ? 'text-indigo-400 scale-105' : 'text-muted hover:text-white' ?>">
                <i class="bi bi-house-door-fill text-lg"></i>
                <span>Inicio</span>
            </a>
            <a href="?explore=1" class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs transition-all <?= isset($_GET['explore']) ? 'text-indigo-400 scale-105' : 'text-muted hover:text-white' ?>">
                <i class="bi bi-compass-fill text-lg"></i>
                <span>Explorar</span>
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="?profile=<?= urlencode($_SESSION['user_id']) ?>" class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs transition-all <?= isset($_GET['profile']) && $_GET['profile'] === $_SESSION['user_id'] ? 'text-indigo-400 scale-105' : 'text-muted hover:text-white' ?>">
                    <i class="bi bi-person-fill text-lg"></i>
                    <span>Perfil</span>
                </a>
                <a href="?settings" class="flex flex-col items-center gap-0.5 px-4 py-1 text-xs transition-all <?= isset($_GET['settings']) ? 'text-indigo-400 scale-105' : 'text-muted hover:text-white' ?>">
                    <i class="bi bi-gear-fill text-lg"></i>
                    <span>Ajustes</span>
                </a>
            <?php endif; ?>
        </div>
    </nav>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="h-16 sm:hidden"></div>
    <?php endif; ?>
</body>
</html>
