<?php
session_start();
include("./core/db/db.php");
include("./core/extras/csrf.php");
include("./core/extras/theme.php");
include("./core/profile/view.php");

$logged = isset($_SESSION['user_id']);

// If logged in but user no longer exists in DB → force logout
if ($logged) {
    $stmt = $conn->prepare("SELECT 1 FROM users WHERE id = ?");
    $stmt->bind_param("s", $_SESSION['user_id']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        session_destroy();
        header("Location: index.php");
        exit;
    }
}

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
} elseif (isset($_GET['post'])) {
    $page = 'post';
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
        .story-circle { width: 64px; height: 64px; border-radius: 50%; padding: 2px; background: conic-gradient(from 0deg, var(--accent), #f472b6, var(--accent)); flex-shrink: 0; display: flex; align-items: center; justify-content: center; position: relative; }
        .story-circle-inner { width: 100%; height: 100%; border-radius: 50%; overflow: hidden; border: 2px solid var(--bg-primary); }

        .post-actions { display: flex; align-items: center; gap: 16px; }
        .post-actions button { transition: all 0.2s ease; }
        .like-btn:hover i { transform: scale(1.15); }
        .like-btn.liked i { font-weight: 900; }

        .comment-btn[data-post-id] { cursor: pointer; }
        @keyframes slideUpBottom { from { transform: translateY(100%); } to { transform: translateY(0); } }
        @keyframes fadeInBg { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        #comments-sheet-mobile:not(.hidden) { display: flex; }
        #comments-sheet-desktop:not(.hidden) { display: flex; }
        #comments-sheet-mobile > .absolute { animation: fadeInBg 0.2s ease-out; }
        #comments-sheet-mobile > .relative { animation: slideUpBottom 0.3s ease-out; }
        #comments-sheet-desktop > .absolute { animation: fadeInBg 0.2s ease-out; }
        #comments-sheet-desktop > .relative { display: none; }
        @media (min-width: 1280px) {
            #comments-sheet-desktop:not(.hidden) > .relative { display: flex; animation: scaleIn 0.25s ease-out; }
        }
        .sheet-handle { width: 36px; height: 4px; border-radius: 2px; background: var(--border); margin: 0 auto; }
        .sheet-comments-list::-webkit-scrollbar { width: 4px; }
        .sheet-comments-list::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
        .sheet-close-trigger { cursor: pointer; }
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

            <?php elseif ($page === 'post'): ?>
                <?php include("./core/post/view.php"); ?>

            <?php else: ?>
                <div class="flex flex-col items-center xl:flex-row xl:items-start xl:gap-8 xl:justify-center">
                    <div class="w-full max-w-[470px] space-y-4">
                            <?php if ($logged): ?>
                                <?php include("./components/stories_bar.php"); ?>
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
<!-- Bottom Sheet (mobile) -->
<div id="comments-sheet-mobile" class="fixed inset-0 z-[60] hidden xl:hidden">
    <div class="absolute inset-0 bg-black/60 sheet-close-trigger"></div>
    <div class="relative mt-auto w-full max-h-[85vh] rounded-t-2xl overflow-hidden flex flex-col" style="background: var(--bg-card);">
        <div class="flex justify-center pt-3 pb-1 shrink-0">
            <div class="sheet-handle"></div>
        </div>
        <div class="flex items-center justify-between px-4 pb-3 shrink-0">
            <p class="font-semibold text-sm">Comentarios</p>
            <button class="sheet-close-btn text-muted hover:text-white text-lg p-1"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="px-4 overflow-y-auto space-y-3 flex-1 min-h-0 sheet-comments-list"></div>
        <div class="comment-form flex items-center gap-2 px-4 py-3 shrink-0" style="border-top: 1px solid var(--border);">
            <input type="hidden" class="csrf-input" value="">
            <input type="hidden" class="post-id-input" value="">
            <input type="text" placeholder="Agrega un comentario..." class="flex-1 bg-transparent text-sm py-1 border-0 focus:outline-none comment-input" style="color: var(--text-primary);" autocomplete="off">
            <button type="button" class="comment-submit text-sm font-semibold transition opacity-60 hover:opacity-100" style="color: var(--accent);">Publicar</button>
        </div>
    </div>
</div>

<!-- Desktop Detail Modal (xl+) -->
<div id="comments-sheet-desktop" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/70 sheet-close-trigger"></div>
    <div class="relative m-auto w-[95vw] max-w-[1000px] h-[85vh] rounded-2xl overflow-hidden" style="background: var(--bg-card);">
        <!-- Image -->
        <div class="w-1/2 bg-black flex items-center justify-center min-w-0">
            <img id="desk-img" class="max-w-full max-h-full object-contain" src="" alt="">
        </div>
        <!-- Comments panel -->
        <div class="w-1/2 flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 shrink-0" style="border-bottom: 1px solid var(--border);">
                <div class="flex items-center gap-3">
                    <img id="desk-avatar" class="w-8 h-8 rounded-full object-cover">
                    <span id="desk-username" class="font-semibold text-sm"></span>
                </div>
                <button class="sheet-close-btn text-muted hover:text-white text-lg p-1"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4 sheet-comments-list"></div>
            <div class="comment-form flex items-center gap-2 px-5 py-4 shrink-0" style="border-top: 1px solid var(--border);">
                <input type="hidden" class="csrf-input" value="">
                <input type="hidden" class="post-id-input" value="">
                <input type="text" placeholder="Agrega un comentario..." class="flex-1 bg-transparent text-sm py-1 border-0 focus:outline-none comment-input" style="color: var(--text-primary);" autocomplete="off">
                <button type="button" class="comment-submit text-sm font-semibold transition opacity-60 hover:opacity-100" style="color: var(--accent);">Publicar</button>
            </div>
        </div>
    </div>
</div>

<!-- Story Viewer Overlay -->
<div id="story-viewer" class="fixed inset-0 z-[70] hidden" style="animation: fadeIn 0.2s ease-out;">
    <div class="absolute inset-0 bg-black/50 story-close-trigger"></div>
    <div class="relative w-full h-full max-w-[480px] mx-auto flex flex-col">
        <!-- Progress bar -->
        <div id="story-progress" class="absolute top-0 left-0 right-0 z-20 flex gap-1 px-2 pt-2">
        </div>

        <!-- Top bar -->
        <div class="flex items-center justify-between px-4 py-3 absolute top-3 left-0 right-0 z-10" style="background: linear-gradient(180deg, rgba(0,0,0,0.5) 0%, transparent);">
            <div class="flex items-center gap-3">
                <img id="story-avatar" class="w-9 h-9 rounded-full object-cover ring-2 ring-white/40">
                <p id="story-username" class="font-semibold text-sm text-white"></p>
            </div>
            <button class="story-close-btn text-white/80 hover:text-white text-2xl p-1"><i class="bi bi-x-lg"></i></button>
        </div>

        <!-- Navigation zones -->
        <div id="story-prev" class="absolute top-0 left-0 bottom-0 w-1/3 z-10 cursor-pointer" style="display: none;"></div>
        <div id="story-next" class="absolute top-0 right-0 bottom-0 w-1/3 z-10 cursor-pointer" style="display: none;"></div>

        <!-- Image -->
        <div class="flex-1 flex items-center justify-center p-2">
            <img id="story-image" class="w-full h-full object-contain transition-opacity duration-300" style="max-width: 100%; max-height: 100vh;">
        </div>

        <!-- Bottom actions -->
        <div id="story-bottom" class="absolute bottom-0 left-0 right-0 z-10 flex items-center justify-center gap-6 py-5" style="background: linear-gradient(0deg, rgba(0,0,0,0.5) 0%, transparent);">
            <button id="story-like-btn" class="flex items-center gap-2 text-3xl transition" style="display: none;">
                <i class="bi bi-heart"></i>
            </button>
        </div>
    </div>
</div>

<!-- Story Creation Overlay -->
<div id="story-create" class="fixed inset-0 z-[70] hidden">
    <div class="absolute inset-0 bg-black/60 story-close-trigger"></div>
    <div class="relative m-auto w-full max-w-[420px] h-full sm:h-auto sm:max-h-[90vh] sm:rounded-2xl overflow-hidden" style="background: var(--bg-card);">
        <div class="flex items-center justify-between px-4 py-3" style="border-bottom: 1px solid var(--border);">
            <h2 class="font-semibold text-sm">Crear historia</h2>
            <button class="story-close-btn text-muted hover:text-white text-lg p-1"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="p-4">
            <label class="flex flex-col items-center justify-center gap-3 cursor-pointer px-6 py-12 rounded-xl transition" style="background: var(--bg-primary); border: 2px dashed var(--border);" id="story-dropzone">
                <i class="bi bi-camera text-4xl text-muted"></i>
                <p class="text-muted text-sm font-medium">Haz clic para subir una imagen</p>
                <input type="file" id="story-create-image" accept="image/*" class="hidden">
            </label>
            <img id="story-create-preview" class="rounded-lg hidden max-h-[60vh] object-contain w-full mb-3" style="background: #000;">
            <button id="story-create-submit" class="w-full px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition hidden" style="background: var(--accent);">
                Publicar historia
            </button>
        </div>
    </div>
</div>

<script>
function toggleEdit(id) { var el = document.getElementById("edit-" + id); if (el) el.classList.toggle("hidden"); }
function toggleMenu(id) { var el = document.getElementById(id); if (el) el.classList.toggle("hidden"); }

var CURRENT_USER_ID = <?= json_encode($_SESSION['user_id'] ?? null) ?>;

function escapeHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function refreshStoriesBar() {
    var bar = document.querySelector('.stories-row');
    if (!bar) return;
    fetch('./core/stories/render_bar.php')
        .then(function(r) { return r.text(); })
        .then(function(html) {
            if (html) bar.outerHTML = html;
        })
        .catch(function() {});
}

function loadComments(postId, listEl, csrf) {
    listEl.innerHTML = '<div class="flex justify-center py-8"><div class="w-6 h-6 border-2 border-[var(--accent)] border-t-transparent rounded-full animate-spin"></div></div>';
    fetch('./core/post/comments/get_comments.php?post_id=' + encodeURIComponent(postId))
        .then(function(r) { return r.json(); })
        .then(function(comments) {
            if (!comments || !comments.length) {
                listEl.innerHTML = '<p class="text-center text-muted text-sm py-8">No hay comentarios. ¡Sé el primero!</p>';
                return;
            }
            var html = '';
            for (var i = 0; i < comments.length; i++) {
                var c = comments[i];
                html += '<div class="flex items-start gap-2 text-sm group/comment" data-comment-id="' + c.id + '">' +
                    '<img src="' + escapeHtml(c.avatar || 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') + '" class="w-6 h-6 rounded-full flex-shrink-0 mt-0.5 object-cover">' +
                    '<div class="flex-1 min-w-0"><a href="?profile=' + encodeURIComponent(c.user_id) + '" class="font-semibold text-xs" style="color: var(--text-primary);">' + escapeHtml(c.username) + '</a>' +
                    '<p class="text-sm" style="color: var(--text-primary);">' + escapeHtml(c.content) + '</p></div>';
                if (CURRENT_USER_ID && c.user_id === CURRENT_USER_ID) {
                    html += '<button class="delete-comment opacity-0 group-hover/comment:opacity-100 transition shrink-0 text-muted hover:text-red-400 text-xs p-1" data-comment-id="' + c.id + '" data-csrf="' + csrf + '"><i class="bi bi-trash"></i></button>';
                }
                html += '</div>';
            }
            listEl.innerHTML = html;
        })
        .catch(function() {
            listEl.innerHTML = '<p class="text-center text-muted text-sm py-8">Error al cargar comentarios</p>';
        });
}

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
                    var el = delBtn.parentElement;
                    if (el && el.hasAttribute('data-comment-id')) el.remove();
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

            fetch('./core/post/comments/create_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'csrf_token=' + encodeURIComponent(csrf) + '&post_id=' + encodeURIComponent(postId) + '&content=' + encodeURIComponent(content)
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.error || !data.success) { console.error('Comment error:', data); return; }
                // Refresh the full comment list from server (reliable)
                var mob = document.getElementById('comments-sheet-mobile');
                var desk = document.getElementById('comments-sheet-desktop');
                var sheet = (!mob.classList.contains('hidden')) ? mob : (!desk.classList.contains('hidden')) ? desk : null;
                var list = sheet ? sheet.querySelector('.sheet-comments-list') : null;
                if (list) loadComments(postId, list, csrf);
                input.value = '';
            })
            .catch(function(err) { console.error('Comment fetch error:', err); });
        }

        // comment button -> open comments overlay
        var commentBtn = e.target.closest('.comment-btn');
        if (commentBtn) {
            var postId = commentBtn.dataset.postId;
            var csrf = commentBtn.dataset.csrf;
            var isDesktop = window.innerWidth >= 1280;

            if (isDesktop) {
                var sheet = document.getElementById('comments-sheet-desktop');
                var list = sheet.querySelector('.sheet-comments-list');
                sheet.querySelector('.csrf-input').value = csrf;
                sheet.querySelector('.post-id-input').value = postId;
                document.getElementById('desk-img').src = commentBtn.dataset.image || '';
                document.getElementById('desk-avatar').src = commentBtn.dataset.authorAvatar;
                document.getElementById('desk-username').textContent = commentBtn.dataset.authorName;
                loadComments(postId, list, csrf);
                sheet.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                var sheet = document.getElementById('comments-sheet-mobile');
                var list = sheet.querySelector('.sheet-comments-list');
                sheet.querySelector('.csrf-input').value = csrf;
                sheet.querySelector('.post-id-input').value = postId;
                loadComments(postId, list, csrf);
                sheet.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            return;
        }

        // close comments overlay
        if (e.target.classList.contains('sheet-close-trigger') || e.target.closest('.sheet-close-btn') || e.target.classList.contains('story-close-trigger') || e.target.closest('.story-close-btn')) {
            var mobile = document.getElementById('comments-sheet-mobile');
            var desktop = document.getElementById('comments-sheet-desktop');
            mobile.classList.add('hidden');
            desktop.classList.add('hidden');
            var sv = document.getElementById('story-viewer');
            sv.classList.add('hidden');
            if (sv._navHandler) { sv.removeEventListener('click', sv._navHandler); sv._navHandler = null; }
            document.getElementById('story-create')?.classList.add('hidden');
            document.body.style.overflow = '';
        }

        // follow/unfollow
        var followBtn = e.target.closest('.follow-btn');
        if (followBtn) {
            var fid = followBtn.dataset.followedId;
            var cs = followBtn.dataset.csrf;
            fetch('./core/follow/follow.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'csrf_token=' + encodeURIComponent(cs) + '&followed_id=' + encodeURIComponent(fid)
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.error) return;
                var following = data.following;
                followBtn.dataset.following = following ? '1' : '0';
                followBtn.textContent = following ? 'Siguiendo' : 'Seguir';
                if (following) {
                    followBtn.className = 'follow-btn px-6 py-1.5 rounded-lg text-xs font-semibold transition-all bg-transparent border text-muted';
                    followBtn.style.cssText = 'border-color: var(--border); color: var(--text-primary);';
                } else {
                    followBtn.className = 'follow-btn px-6 py-1.5 rounded-lg text-xs font-semibold transition-all text-white shadow-sm';
                    followBtn.style.cssText = 'background: var(--accent);';
                }
                var fc = document.querySelector('[data-follower-count]');
                if (fc) fc.textContent = data.follower_count;
            })
            .catch(function() {});
            return;
        }

        // open story viewer
        var storyItem = e.target.closest('.story-item');
        if (storyItem) {
            var userId = storyItem.dataset.userId;
            var viewer = document.getElementById('story-viewer');
            var imgEl = document.getElementById('story-image');
            var likeBtn = document.getElementById('story-like-btn');
            var prevZone = document.getElementById('story-prev');
            var nextZone = document.getElementById('story-next');
            var progressEl = document.getElementById('story-progress');
            var avatarEl = document.getElementById('story-avatar');
            var usernameEl = document.getElementById('story-username');

            imgEl.style.opacity = '0';
            viewer.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            var currentIndex = 0;
            var storiesData = [];

            function showStory(index) {
                if (!storiesData.length) return;
                currentIndex = index;
                var s = storiesData[currentIndex];
                imgEl.style.opacity = '0';
                setTimeout(function() {
                    imgEl.src = s.image || '';
                    imgEl.style.opacity = '1';
                }, 80);

                avatarEl.src = s.avatar;
                usernameEl.textContent = s.username;

                var dots = '';
                for (var d = 0; d < storiesData.length; d++) {
                    var fill = d <= currentIndex ? 'var(--accent)' : 'rgba(255,255,255,0.3)';
                    dots += '<div class="flex-1 h-0.5 rounded-full" style="background: ' + fill + '; transition: background 0.3s;"></div>';
                }
                progressEl.innerHTML = dots;

                prevZone.style.display = currentIndex > 0 ? 'block' : 'none';
                nextZone.style.display = currentIndex < storiesData.length - 1 ? 'block' : 'none';

                if (s.is_owner) {
                    likeBtn.style.display = 'none';
                } else {
                    likeBtn.style.display = 'flex';
                    likeBtn.dataset.storyId = s.id;
                    var icon = likeBtn.querySelector('i');
                    icon.className = 'bi ' + (s.is_liked ? 'bi-heart-fill text-pink-500' : 'bi-heart text-white/70 hover:text-pink-500');
                    likeBtn.innerHTML = icon.outerHTML + ' <span class="text-sm text-white/70 font-semibold">' + s.likes_count + '</span>';
                }
            }

            fetch('./core/stories/get_user_stories.php?user_id=' + encodeURIComponent(userId))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (!data || data.error || !data.length) { viewer.classList.add('hidden'); document.body.style.overflow = ''; return; }
                    storiesData = data;
                    showStory(0);
                })
                .catch(function() { viewer.classList.add('hidden'); document.body.style.overflow = ''; });

            // Navigation via click on viewer
            viewer._navHandler = function(e) {
                var t = e.target;
                if (t.closest('.story-close-btn') || t.classList.contains('story-close-trigger')) return;
                if (t.closest('#story-like-btn')) return;

                var rect = viewer.getBoundingClientRect();
                var relX = (e.clientX - rect.left) / rect.width;

                if (relX < 0.4 && currentIndex > 0) {
                    showStory(currentIndex - 1);
                } else if (relX > 0.6 && currentIndex < storiesData.length - 1) {
                    showStory(currentIndex + 1);
                }
            };
            viewer.addEventListener('click', viewer._navHandler);
            return;
        }

        // story like/unlike
        var storyLikeBtn = e.target.closest('#story-like-btn');
        if (storyLikeBtn) {
            var storyId = storyLikeBtn.dataset.storyId;
            var liked = storyLikeBtn.dataset.liked === '1';
            var csrf = '<?= generateCsrfToken() ?>';
            fetch('./core/stories/like_story.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'csrf_token=' + encodeURIComponent(csrf) + '&story_id=' + encodeURIComponent(storyId)
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.error) return;
                var icon = storyLikeBtn.querySelector('i');
                icon.className = 'bi ' + (data.liked ? 'bi-heart-fill text-pink-500' : 'bi-heart text-white/70 hover:text-pink-500');
                storyLikeBtn.dataset.liked = data.liked ? '1' : '0';
                storyLikeBtn.innerHTML = icon.outerHTML + ' <span class="text-sm text-white/70 font-semibold">' + data.count + '</span>';
            })
            .catch(function() {});
            return;
        }

        // "Tu historia" button -> open creation overlay
        var storyCreate = e.target.closest('.story-create-btn');
        if (storyCreate) {
            document.getElementById('story-create').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            return;
        }

        // delete post
        var delPostBtn = e.target.closest('.delete-post-btn');
        if (delPostBtn) {
            if (!confirm('¿Eliminar post?')) return;
            var pid = delPostBtn.dataset.postId;
            var cs = delPostBtn.dataset.csrf;
            fetch('./core/post/delete_post.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'csrf_token=' + encodeURIComponent(cs) + '&post_id=' + encodeURIComponent(pid)
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    var card = delPostBtn.closest('[data-post-card]');
                    if (card) card.remove();
                }
            })
            .catch(function() {});
            return;
        }
    });
})();

// Upload post with AJAX
(function() {
    var form = document.getElementById('upload-form');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = form.querySelector('button[type="submit"]');
        var original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mx-auto"></div>';
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.error) { console.error(data.error); return; }
            if (data.html) {
                var feed = document.getElementById('feed-container');
                if (feed) {
                    if (!feed.querySelector('.card')) {
                        feed.innerHTML = data.html;
                    } else {
                        feed.insertAdjacentHTML('afterbegin', data.html);
                    }
                }
            }
            form.reset();
            var prev = document.getElementById('preview');
            if (prev) prev.classList.add('hidden');
        })
        .catch(function(err) { console.error('Upload error:', err); })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = original;
        });
    });
})();

// Edit post with AJAX (delegated)
document.addEventListener('submit', function(e) {
    var editForm = e.target.closest('.edit-post-form');
    if (!editForm) return;
    e.preventDefault();
    var card = editForm.closest('.card');
    if (!card) return;
    var btn = editForm.querySelector('button[type="submit"]');
    var original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<div class="w-4 h-4 border-2 border-[var(--accent)] border-t-transparent rounded-full animate-spin mx-auto"></div>';
    fetch(editForm.action, {
        method: 'POST',
        body: new FormData(editForm)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.error) { console.error(data.error); return; }
        var contentP = card.querySelector('p.text-sm.leading-relaxed');
        if (contentP) {
            var nameSpan = contentP.querySelector('span.font-semibold');
            if (nameSpan) {
                contentP.innerHTML = nameSpan.outerHTML + ' ' + escapeHtml(editForm.querySelector('[name="content"]').value);
            }
        }
        if (data.image_url) {
            var postImg = card.querySelector('img[style*="max-height"]');
            if (postImg) {
                postImg.src = data.image_url;
            } else {
                var imgContainer = card.querySelector('div.-mx-3');
                if (!imgContainer) {
                    var newContainer = document.createElement('div');
                    newContainer.className = '-mx-3 sm:-mx-4 mb-3';
                    newContainer.style.cssText = 'background: #000;';
                    var newImg = document.createElement('img');
                    newImg.className = 'w-full';
                    newImg.style.cssText = 'max-height: 585px; object-fit: contain;';
                    newImg.loading = 'lazy';
                    newImg.src = data.image_url;
                    newContainer.appendChild(newImg);
                    var actions = card.querySelector('.post-actions');
                    if (actions) {
                        card.insertBefore(newContainer, actions);
                    }
                }
            }
        }
        var editBox = editForm.closest('[id^="edit-"]');
        if (editBox) editBox.classList.add('hidden');
    })
    .catch(function(err) { console.error('Edit error:', err); })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = original;
    });
});

// Story creation - image preview
document.getElementById('story-create-image')?.addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function(ev) {
        var preview = document.getElementById('story-create-preview');
        preview.src = ev.target.result;
        preview.classList.remove('hidden');
        document.getElementById('story-dropzone').classList.add('hidden');
        document.getElementById('story-create-submit').classList.remove('hidden');
    };
    reader.readAsDataURL(file);
});

// Story creation - submit
document.getElementById('story-create-submit')?.addEventListener('click', function() {
    var overlay = document.getElementById('story-create');
    var fileInput = document.getElementById('story-create-image');
    if (!fileInput.files[0]) { alert('Selecciona una imagen'); return; }
    var btn = this;
    var original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mx-auto"></div>';

    var formData = new FormData();
    formData.append('csrf_token', '<?= generateCsrfToken() ?>');
    formData.append('content', ' ');
    formData.append('image', fileInput.files[0]);

    fetch('./core/stories/create_story.php', {
        method: 'POST',
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.error) { alert(data.error); return; }
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
        refreshStoriesBar();
    })
    .catch(function() { alert('Error al crear historia'); })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = original;
        fileInput.value = '';
        document.getElementById('story-create-preview').classList.add('hidden');
        document.getElementById('story-dropzone').classList.remove('hidden');
        document.getElementById('story-create-submit').classList.add('hidden');
    });
});

// Lazy load posts on scroll
(function() {
    var feed = document.getElementById('feed-container');
    if (!feed) return;
    var loader = document.getElementById('feed-loader');
    if (!loader) return;
    var loading = false;
    var offset = 5;
    var hasMore = true;

    function loadMore() {
        if (loading || !hasMore) return;
        loading = true;
        loader.classList.remove('hidden');
        fetch('./core/feed/load_posts.php?offset=' + offset)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.html) {
                    feed.insertAdjacentHTML('beforeend', data.html);
                    offset += 5;
                }
                hasMore = data.hasMore;
                loading = false;
                loader.classList.toggle('hidden', !hasMore);
            })
            .catch(function() {
                loading = false;
                loader.classList.add('hidden');
            });
    }

    window.addEventListener('scroll', function() {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 600) {
            loadMore();
        }
    });
})();
</script>
</body>
</html>
