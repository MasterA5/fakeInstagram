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
            <form action="./core/post/like/like.php" method="POST" class="like-btn">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="post_id" value="<?= $data['id'] ?>">
                <button class="flex items-center gap-1 text-muted hover:text-pink-500 transition text-2xl">
                    <i class="bi bi-heart"></i>
                </button>
            </form>
            <button onclick="document.getElementById('comment-input-<?= $data['id'] ?>').focus()" class="flex items-center gap-1 text-muted hover:text-[var(--accent)] transition text-2xl">
                <i class="bi bi-chat"></i>
            </button>
        </div>

        <div class="text-sm font-semibold mb-1" style="color: var(--text-primary);">
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
        <?php if ($comments->num_rows > 0): ?>
            <div class="space-y-2 mb-2">
                <?php while ($comment = $comments->fetch_assoc()): ?>
                    <div class="flex items-start gap-2 text-sm group/comment">
                        <img src="<?= htmlspecialchars($comment['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-6 h-6 rounded-full flex-shrink-0 mt-0.5">
                        <div class="flex-1 min-w-0">
                            <a href="?profile=<?= urlencode($comment['user_id']) ?>" class="font-semibold text-xs" style="color: var(--text-primary);"><?= htmlspecialchars($comment['username']) ?></a>
                            <p class="text-sm" style="color: var(--text-primary);"><?= htmlspecialchars($comment['content']) ?></p>
                        </div>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $comment['user_id']): ?>
                            <form action="./core/post/comments/delete_comment.php" method="POST" class="opacity-0 group-hover/comment:opacity-100 transition shrink-0">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                <button class="text-muted hover:text-red-400 text-xs transition p-1">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

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
                    <button class="btn-accent px-4 py-2 rounded-xl text-sm text-white font-semibold transition shadow-lg" style="box-shadow: 0 4px 14px 0 color-mix(in srgb, var(--accent) 40%, transparent);">
                        <i class="bi bi-check-lg"></i> Guardar
                    </button>
                    <button type="button" onclick="toggleEdit('<?= $data['id'] ?>')" class="btn-ghost px-4 py-2 rounded-xl text-sm transition text-muted hover:text-white">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="./core/post/comments/create_comment.php" method="POST" class="flex items-center gap-2" style="border-top: 1px solid var(--border); padding-top: 8px;">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="post_id" value="<?= $data['id'] ?>">
                <input id="comment-input-<?= $data['id'] ?>" type="text" name="content" placeholder="Agrega un comentario..." class="flex-1 bg-transparent text-sm py-1 border-0 focus:outline-none" style="color: var(--text-primary);">
                <button class="text-sm font-semibold transition opacity-60 hover:opacity-100" style="color: var(--accent);">Publicar</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleEdit(id) { const el = document.getElementById("edit-" + id); if (el) el.classList.toggle("hidden"); }
    function toggleMenu(id) { const el = document.getElementById(id); if (el) el.classList.toggle("hidden"); }
    document.addEventListener("click", function(e) {
        document.querySelectorAll("[id^='menu-']").forEach(menu => {
            if (!menu.previousElementSibling?.contains(e.target) && !menu.contains(e.target)) menu.classList.add("hidden");
        });
    });
</script>
