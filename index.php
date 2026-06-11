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
                <div class="flex flex-col items-center xl:flex-row xl:items-start xl:gap-8 xl:justify-center">
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
<script>
function toggleEdit(id) { var el = document.getElementById("edit-" + id); if (el) el.classList.toggle("hidden"); }
function toggleMenu(id) { var el = document.getElementById(id); if (el) el.classList.toggle("hidden"); }

(function() {
    document.addEventListener('click', function(e) {
        // close menus
        document.querySelectorAll("[id^='menu-']").forEach(function(menu) {
            if (!menu.previousElementSibling?.contains(e.target) && !menu.contains(e.target)) menu.classList.add("hidden");
        });

        // like
        var likeBtn = e.target.closest('.like-btn');
        if (likeBtn) {
            var postId = likeBtn.dataset.postId;
            var csrf = likeBtn.dataset.csrf;
            var icon = likeBtn.querySelector('i');
            var card = likeBtn.closest('[class*="rounded"]');
            var likesText = card?.querySelector('.likes-text');

            fetch('./core/post/like/like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'csrf_token=' + encodeURIComponent(csrf) + '&post_id=' + encodeURIComponent(postId)
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.error) return;
                icon.className = 'bi ' + (data.liked ? 'bi-heart-fill text-pink-500' : 'bi-heart text-muted hover:text-pink-500');
                likeBtn.dataset.liked = data.liked ? '1' : '0';
                if (likesText) likesText.textContent = data.count + ' me gusta';
            })
            .catch(function() {});
            return;
        }

        // delete comment
        var delBtn = e.target.closest('.delete-comment');
        if (delBtn) {
            var commentId = delBtn.dataset.commentId;
            var csrf = delBtn.dataset.csrf;
            if (!commentId) return;
            fetch('./core/post/comments/delete_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'csrf_token=' + encodeURIComponent(csrf) + '&comment_id=' + encodeURIComponent(commentId)
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    var el = delBtn.closest('[data-comment-id="' + commentId + '"]');
                    if (el) el.remove();
                }
            })
            .catch(function() {});
        }

        // comment submit
        var submitBtn = e.target.closest('.comment-submit');
        if (submitBtn) {
            var form = submitBtn.closest('.comment-form');
            if (!form) return;
            var input = form.querySelector('.comment-input');
            var content = input.value.trim();
            if (!content) return;
            var csrf = form.querySelector('.csrf-input').value;
            var postId = form.querySelector('.post-id-input').value;
            var container = document.querySelector('.comments-container[data-post-id="' + postId + '"]');

            fetch('./core/post/comments/create_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'csrf_token=' + encodeURIComponent(csrf) + '&post_id=' + encodeURIComponent(postId) + '&content=' + encodeURIComponent(content)
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.error || !data.success) return;
                var div = document.createElement('div');
                div.className = 'flex items-start gap-2 text-sm group/comment';
                div.dataset.commentId = data.id;
                div.innerHTML =
                    '<img src="' + (data.avatar || 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') + '" class="w-6 h-6 rounded-full flex-shrink-0 mt-0.5">' +
                    '<div class="flex-1 min-w-0"><a href="?profile=' + data.user_id + '" class="font-semibold text-xs" style="color: var(--text-primary);">' + data.username + '</a>' +
                    '<p class="text-sm" style="color: var(--text-primary);">' + data.content.replace(/</g, '&lt;') + '</p></div>' +
                    '<button class="delete-comment opacity-0 group-hover/comment:opacity-100 transition shrink-0 text-muted hover:text-red-400 text-xs p-1" data-comment-id="' + data.id + '" data-csrf="' + csrf + '"><i class="bi bi-trash"></i></button>';
                if (container) container.prepend(div);
                input.value = '';
            })
            .catch(function() {});
        }
    });
})();
</script>
</body>
</html>
