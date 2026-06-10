<?php
session_start();
include("./core/db/db.php");
include("./core/extras/csrf.php");
include("./core/extras/theme.php");
include("./core/profile/view.php");

$logged = isset($_SESSION['user_id']);

$activeTheme = 'dark';
$paletteJson = null;
if ($logged) {
    $themeData = getUserTheme($conn, $_SESSION['user_id']);
    $activeTheme = $themeData['theme'];
    $paletteJson = $themeData['palette'];
}
$themeCSS = applyTheme($activeTheme, $paletteJson);

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>FakeSocial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        <?= $themeCSS ?>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg-primary); color: var(--text-primary); -webkit-font-smoothing: antialiased; margin: 0; }
        .card { background: var(--bg-card); border-color: var(--border); }
        .text-muted { color: var(--text-secondary); }
        .btn-accent { background: var(--accent); }
        .btn-accent:hover { background: var(--accent-hover); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        .img-zoom { overflow: hidden; }
        .img-zoom img { transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .img-zoom:hover img { transform: scale(1.05); }
        .glass { backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
        .btn-ghost { background: transparent; border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--bg-card-hover); }
        .skeleton { background: linear-gradient(90deg, var(--bg-card) 25%, var(--bg-card-hover) 50%, var(--bg-card) 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; }
        @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes heartBeat { 0% { transform: scale(1); } 50% { transform: scale(1.3); } 100% { transform: scale(1); } }
        .animate-fade-in { animation: fadeIn 0.4s ease-out; }
        .animate-slide-up { animation: slideUp 0.4s ease-out; }
        .heart-animate:active i { animation: heartBeat 0.3s ease-in-out; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-secondary); }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--text-secondary); }
        input:focus-visible, textarea:focus-visible, button:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        * { transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease; }

        .profile-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px; }
        @media (min-width: 640px) { .profile-grid { gap: 8px; } }
        .profile-grid-item { aspect-ratio: 1; overflow: hidden; border-radius: 0; }
        .profile-grid-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
        .profile-grid-item:hover img { transform: scale(1.05); }

        .stories-row { display: flex; gap: 16px; overflow-x: auto; padding: 16px 0; scrollbar-width: none; }
        .stories-row::-webkit-scrollbar { display: none; }
        .story-circle { width: 64px; height: 64px; border-radius: 50%; padding: 2px; background: conic-gradient(from 0deg, var(--accent), #f472b6, var(--accent)); flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .story-circle-inner { width: 100%; height: 100%; border-radius: 50%; overflow: hidden; border: 2px solid var(--bg-primary); }

        .post-actions { display: flex; align-items: center; gap: 16px; }
        .post-actions button { transition: all 0.2s ease; }
        .like-btn:hover i { transform: scale(1.15); }
        .like-btn.liked i { font-weight: 900; }
    </style>
</head>
<body class="min-h-screen bg-[var(--bg-primary)]">

    <?php include("./components/appbar.php"); ?>

    <main class="pb-20 sm:pb-6">
        <div class="max-w-[935px] mx-auto px-0 sm:px-4 pt-0 sm:pt-6 animate-fade-in">

            <?php if ($page === 'profile'): ?>
                <div class="max-w-[935px] mx-auto">
                    <?php include("./components/profile_header.php"); ?>
                </div>

            <?php elseif ($page === 'settings' && $logged): ?>
                <?php include("./core/profile/settings.php"); ?>

            <?php elseif ($page === 'explore'): ?>
                <?php include("./core/feed/explore.php"); ?>

            <?php else: ?>
                <div class="flex gap-8 justify-center">
                    <div class="w-full max-w-[470px] space-y-4">
                        <?php if ($logged): ?>
                            <?php include("./components/upload_card.php"); ?>
                            <?php include("./core/feed/test.php"); ?>
                        <?php else: ?>
                            <?php include("./core/forms/forms.php"); ?>
                        <?php endif; ?>
                    </div>
                    <div class="hidden xl:block w-[319px] flex-shrink-0">
                        <div class="sticky top-20 space-y-4">
                            <?php include("./components/sidebar.php"); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <nav class="sm:hidden fixed bottom-0 left-0 right-0 z-50" style="background: var(--bg-secondary); border-top: 1px solid var(--border);">
        <div class="flex justify-around items-center h-12">
            <a href="index.php" class="flex flex-col items-center justify-center w-full h-full <?= $page === 'feed' ? 'text-[var(--accent)]' : 'text-muted' ?>">
                <i class="bi <?= $page === 'feed' ? 'bi-house-door-fill' : 'bi-house-door' ?> text-xl"></i>
            </a>
            <a href="?explore=1" class="flex flex-col items-center justify-center w-full h-full <?= $page === 'explore' ? 'text-[var(--accent)]' : 'text-muted' ?>">
                <i class="bi <?= $page === 'explore' ? 'bi-compass-fill' : 'bi-compass' ?> text-xl"></i>
            </a>
            <?php if ($logged): ?>
                <a href="?profile=<?= urlencode($_SESSION['user_id']) ?>" class="flex flex-col items-center justify-center w-full h-full <?= $page === 'profile' && isset($_GET['profile']) && $_GET['profile'] === $_SESSION['user_id'] ? 'text-[var(--accent)]' : 'text-muted' ?>">
                    <i class="bi <?= $page === 'profile' && isset($_GET['profile']) && $_GET['profile'] === $_SESSION['user_id'] ? 'bi-person-fill' : 'bi-person' ?> text-xl"></i>
                </a>
                <a href="?settings" class="flex flex-col items-center justify-center w-full h-full <?= $page === 'settings' ? 'text-[var(--accent)]' : 'text-muted' ?>">
                    <i class="bi <?= $page === 'settings' ? 'bi-gear-fill' : 'bi-gear' ?> text-xl"></i>
                </a>
            <?php else: ?>
                <a href="?mode=login" class="flex flex-col items-center justify-center w-full h-full text-muted">
                    <i class="bi bi-box-arrow-in-right text-xl"></i>
                </a>
            <?php endif; ?>
        </div>
    </nav>
</body>
</html>
