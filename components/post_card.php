<?php require_once(__DIR__ . '/../core/extras/format_datetime.php') ?>
<div class="card border rounded-none sm:rounded-xl mb-4 animate-slide-up" style="border-color: var(--border);">
    <div class="p-3 sm:p-4">
        <div class="flex items-center justify-between mb-3">
            <a href="?profile=<?= urlencode($data['user_id']) ?>" class="flex items-center gap-3 group">
                <img src="<?= htmlspecialchars($data['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-8 h-8 rounded-full ring-2 ring-transparent group-hover:ring-[var(--accent)]/50 transition-all">
                <div>
                    <p class="font-semibold text-sm" style="color: var(--text-primary);"><?= htmlspecialchars($data['display_name'] ?? $data['username']) ?></p>
                    <p class="text-[11px] text-muted">@<?= htmlspecialchars($data['username']) ?></p>
                </div>
            </a>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $data['user_id']): ?>
                <div class="relative">
                    <button onclick="toggleMenu('menu-<?= $data['id'] ?>')" class="text-muted hover:text-white text-lg px-1.5 py-0.5 rounded-lg hover:bg-[var(--bg-card-hover)] transition">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <div id="menu-<?= $data['id'] ?>" class="hidden absolute right-0 mt-1 w-36 card border rounded-xl shadow-2xl z-50 overflow-hidden" style="background: var(--bg-card); border-color: var(--border);">
                        <button onclick="toggleEdit('<?= $data['id'] ?>')" class="w-full text-left px-3 py-2.5 text-sm hover:bg-[var(--bg-card-hover)] flex items-center gap-2 transition" style="color: #eab308;">
                            <i class="bi bi-pencil text-xs"></i> Editar
                        </button>
                        <form action="./core/post/delete_post.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="post_id" value="<?= $data['id'] ?>">
                            <button onclick="return confirm('¿Eliminar post?')" class="w-full text-left px-3 py-2.5 text-sm hover:bg-[var(--bg-card-hover)] flex items-center gap-2 transition" style="color: #ef4444;">
                                <i class="bi bi-trash text-xs"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($data['image'])): ?>
            <div class="-mx-3 sm:-mx-4 mb-3" style="background: #000;">
                <img src="<?= htmlspecialchars($data['image']) ?>" class="w-full" style="max-height: 585px; object-fit: contain;" loading="lazy">
            </div>
        <?php endif; ?>

        <div class="post-actions mb-2">
            <button class="like-btn flex items-center gap-1 text-2xl transition" data-post-id="<?= $data['id'] ?>" data-csrf="<?= generateCsrfToken() ?>" data-liked="<?= !empty($data['is_liked']) ? '1' : '0' ?>">
                <i class="bi <?= !empty($data['is_liked']) ? 'bi-heart-fill' : 'bi-heart' ?> <?= !empty($data['is_liked']) ? 'text-pink-500' : 'text-muted hover:text-pink-500' ?>"></i>
            </button>
            <button onclick="document.getElementById('comment-input-<?= $data['id'] ?>').focus()" class="flex items-center gap-1 text-muted hover:text-[var(--accent)] transition text-2xl">
                <i class="bi bi-chat"></i>
            </button>
        </div>

        <div class="text-sm font-semibold mb-1 likes-text" style="color: var(--text-primary);">
            <?= $data['likes_count'] ?> me gusta
        </div>

        <p class="text-sm leading-relaxed" style="color: var(--text-primary);">
            <span class="font-semibold mr-1.5"><?= htmlspecialchars($data['display_name'] ?? $data['username']) ?></span>
            <?= htmlspecialchars($data['content']) ?>
        </p>

        <div class="mt-2" style="border-top: 1px solid var(--border);"></div>
    </div>

    <div class="px-3 sm:px-4 pb-3">
        <?php
        $stmt = $conn->prepare("SELECT comments.*, users.username, users.avatar FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at ASC LIMIT 5");
        $stmt->bind_param("s", $data['id']);
        $stmt->execute();
        $comments = $stmt->get_result();
        ?>
        <div class="space-y-2 mb-2 comments-container" data-post-id="<?= $data['id'] ?>">
            <?php if ($comments->num_rows > 0): ?>
                <?php while ($comment = $comments->fetch_assoc()): ?>
                    <div class="flex items-start gap-2 text-sm group/comment" data-comment-id="<?= $comment['id'] ?>">
                        <img src="<?= htmlspecialchars($comment['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-6 h-6 rounded-full flex-shrink-0 mt-0.5">
                        <div class="flex-1 min-w-0">
                            <a href="?profile=<?= urlencode($comment['user_id']) ?>" class="font-semibold text-xs" style="color: var(--text-primary);"><?= htmlspecialchars($comment['username']) ?></a>
                            <p class="text-sm" style="color: var(--text-primary);"><?= htmlspecialchars($comment['content']) ?></p>
                        </div>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $comment['user_id']): ?>
                            <button class="delete-comment opacity-0 group-hover/comment:opacity-100 transition shrink-0 text-muted hover:text-red-400 text-xs p-1" data-comment-id="<?= $comment['id'] ?>" data-csrf="<?= generateCsrfToken() ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <div class="text-[11px] text-muted mb-2">
            <?= timeAgo($data['created_at']) ?>
        </div>

        <div id="edit-<?= $data['id'] ?>" class="hidden mb-3 p-3 rounded-xl animate-fade-in" style="background: var(--bg-primary);">
            <form action="./core/post/edit_post.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="post_id" value="<?= $data['id'] ?>">
                <textarea name="content" rows="2" class="w-full px-3 py-2 rounded-xl text-sm transition mb-2" style="background: var(--bg-card); border: 1px solid var(--border); color: var(--text-primary);"><?= htmlspecialchars($data['content']) ?></textarea>
                <input type="file" name="image" class="text-xs text-muted file:mr-2 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-[var(--bg-card-hover)] file:text-white hover:file:bg-[var(--border)]">
                <div class="flex gap-2 mt-3">
                    <button class="btn-accent px-4 py-2 rounded-xl text-sm text-white font-semibold transition shadow-lg">
                        <i class="bi bi-check-lg"></i> Guardar
                    </button>
                    <button type="button" onclick="toggleEdit('<?= $data['id'] ?>')" class="btn-ghost px-4 py-2 rounded-xl text-sm transition text-muted hover:text-white">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form class="comment-form flex items-center gap-2" style="border-top: 1px solid var(--border); padding-top: 8px;">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="post_id" value="<?= $data['id'] ?>">
                <input id="comment-input-<?= $data['id'] ?>" type="text" name="content" placeholder="Agrega un comentario..." class="flex-1 bg-transparent text-sm py-1 border-0 focus:outline-none" style="color: var(--text-primary);" autocomplete="off">
                <button type="submit" class="text-sm font-semibold transition opacity-60 hover:opacity-100" style="color: var(--accent);">Publicar</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
if (!window._postInteractionsInitialized) {
    window._postInteractionsInitialized = true;

    function toggleEdit(id) { const el = document.getElementById("edit-" + id); if (el) el.classList.toggle("hidden"); }
    function toggleMenu(id) { const el = document.getElementById(id); if (el) el.classList.toggle("hidden"); }

    document.addEventListener('click', function(e) {
        // close menus
        document.querySelectorAll("[id^='menu-']").forEach(menu => {
            if (!menu.previousElementSibling?.contains(e.target) && !menu.contains(e.target)) menu.classList.add("hidden");
        });

        // like
        const likeBtn = e.target.closest('.like-btn');
        if (likeBtn) {
            const postId = likeBtn.dataset.postId;
            const csrf = likeBtn.dataset.csrf;
            const icon = likeBtn.querySelector('i');
            const card = likeBtn.closest('[class*="rounded"]');
            const likesText = card?.querySelector('.likes-text');

            fetch('./core/post/like/like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'csrf_token=' + encodeURIComponent(csrf) + '&post_id=' + encodeURIComponent(postId)
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) return;
                icon.className = 'bi ' + (data.liked ? 'bi-heart-fill text-pink-500' : 'bi-heart text-muted hover:text-pink-500');
                likeBtn.dataset.liked = data.liked ? '1' : '0';
                if (likesText) likesText.textContent = data.count + ' me gusta';
            })
            .catch(() => {});
            return;
        }

        // delete comment
        const delBtn = e.target.closest('.delete-comment');
        if (delBtn) {
            const commentId = delBtn.dataset.commentId;
            const csrf = delBtn.dataset.csrf;
            if (!commentId) return;
            fetch('./core/post/comments/delete_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'csrf_token=' + encodeURIComponent(csrf) + '&comment_id=' + encodeURIComponent(commentId)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const el = delBtn.closest('[data-comment-id="' + commentId + '"]');
                    if (el) el.remove();
                }
            })
            .catch(() => {});
        }
    });

    // comment submit delegation
    document.addEventListener('submit', function(e) {
        const form = e.target.closest('.comment-form');
        if (!form) return;
        e.preventDefault();
        const input = form.querySelector('input[name="content"]');
        const content = input.value.trim();
        if (!content) return;
        const csrf = form.querySelector('input[name="csrf_token"]').value;
        const postId = form.querySelector('input[name="post_id"]').value;
        const container = document.querySelector('.comments-container[data-post-id="' + postId + '"]');

        fetch('./core/post/comments/create_comment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'csrf_token=' + encodeURIComponent(csrf) + '&post_id=' + encodeURIComponent(postId) + '&content=' + encodeURIComponent(content)
        })
        .then(r => r.json())
        .then(data => {
            if (data.error || !data.success) return;
            const div = document.createElement('div');
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
        .catch(() => {});
    });
}
</script>
