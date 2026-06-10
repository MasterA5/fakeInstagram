<?php require_once(__DIR__ . '/../core/extras/format_datetime.php') ?>
<div class="card border rounded-2xl card-hover shadow-sm overflow-hidden animate-slide-up" style="border-color: var(--border);">
    <div class="p-4">
        <div class="flex items-center justify-between">
            <a href="?profile=<?= urlencode($data['user_id']) ?>" class="flex items-center gap-3 group">
                <div class="relative">
                    <img src="<?= htmlspecialchars($data['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-10 h-10 rounded-full ring-2 ring-transparent group-hover:ring-indigo-500/50 transition-all">
                    <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-zinc-900 rounded-full"></div>
                </div>
                <div>
                    <p class="font-semibold text-sm group-hover" style="color: var(--text-primary);"><?= htmlspecialchars($data['display_name'] ?? $data['username']) ?></p>
                    <p class="text-xs text-muted">@<?= htmlspecialchars($data['username']) ?></p>
                </div>
            </a>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $data['user_id']): ?>
                <div class="relative">
                    <button onclick="toggleMenu('menu-<?= $data['id'] ?>')" class="text-muted hover:text-white text-xl px-2 py-1 rounded-lg hover:bg-zinc-800 transition">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <div id="menu-<?= $data['id'] ?>" class="hidden absolute right-0 mt-2 w-40 card border rounded-xl shadow-2xl z-50 overflow-hidden" style="background: var(--bg-card); border-color: var(--border);">
                        <button onclick="toggleEdit('<?= $data['id'] ?>')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-zinc-800 flex items-center gap-2.5 transition" style="color: #eab308;">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                        <form action="./core/post/delete_post.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            <input type="hidden" name="post_id" value="<?= $data['id'] ?>">
                            <button onclick="return confirm('¿Eliminar post?')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-zinc-800 flex items-center gap-2.5 transition" style="color: #ef4444;">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($data['image'])): ?>
            <div class="mt-3 rounded-2xl img-zoom bg-black" style="aspect-ratio: 4/3;">
                <img src="<?= htmlspecialchars($data['image']) ?>" class="w-full h-full object-cover" loading="lazy">
            </div>
        <?php endif; ?>

        <p class="mt-3 text-sm leading-relaxed" style="color: var(--text-primary);">
            <?= htmlspecialchars($data['content']) ?>
        </p>

        <div class="flex items-center gap-4 mt-3 pt-3" style="border-top: 1px solid var(--border);">
            <form action="./core/post/like/like.php" method="POST" class="heart-animate">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="post_id" value="<?= $data['id'] ?>">
                <button class="flex items-center gap-1.5 text-muted hover:text-pink-500 transition-all text-sm group">
                    <i class="bi bi-heart text-lg group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium"><?= $data['likes_count'] ?></span>
                </button>
            </form>

            <button onclick="document.getElementById('comment-input-<?= $data['id'] ?>').focus()" class="flex items-center gap-1.5 text-muted hover:text-sky-400 transition-all text-sm group">
                <i class="bi bi-chat text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Comentar</span>
            </button>

            <span class="text-xs text-muted ml-auto flex items-center gap-1">
                <i class="bi bi-clock"></i><?= timeAgo($data['created_at']) ?>
            </span>
        </div>

        <div id="edit-<?= $data['id'] ?>" class="hidden mt-3 pt-3 animate-fade-in" style="border-top: 1px solid var(--border);">
            <form action="./core/post/edit_post.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="post_id" value="<?= $data['id'] ?>">
                <textarea name="content" rows="3" class="w-full px-3 py-2 rounded-xl text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);"><?= htmlspecialchars($data['content']) ?></textarea>
                <input type="file" name="image" class="mt-2 text-xs text-muted file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-zinc-700 file:text-white hover:file:bg-zinc-600">
                <div class="flex gap-2 mt-3">
                    <button class="btn-accent px-4 py-2 rounded-xl text-sm text-white font-semibold transition flex items-center gap-1.5 shadow-lg shadow-indigo-500/20">
                        <i class="bi bi-check-lg"></i> Guardar
                    </button>
                    <button type="button" onclick="toggleEdit('<?= $data['id'] ?>')" class="btn-ghost px-4 py-2 rounded-xl text-sm transition flex items-center gap-1.5 text-muted hover:text-white">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="px-4 pb-4">
        <?php
        $stmt = $conn->prepare("SELECT comments.*, users.username, users.avatar FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at ASC");
        $stmt->bind_param("s", $data['id']);
        $stmt->execute();
        $comments = $stmt->get_result();
        ?>
        <?php if ($comments->num_rows > 0): ?>
            <div class="space-y-3 mt-2 pt-3" style="border-top: 1px solid var(--border);">
                <?php while ($comment = $comments->fetch_assoc()): ?>
                    <div class="flex items-start gap-2.5 text-sm group/comment">
                        <img src="<?= htmlspecialchars($comment['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-7 h-7 rounded-full bg-zinc-800 mt-0.5 flex-shrink-0">
                        <div class="flex-1 min-w-0 px-3 py-2 rounded-xl" style="background: var(--bg-primary);">
                            <a href="?profile=<?= urlencode($comment['user_id']) ?>" class="font-semibold text-xs" style="color: var(--accent);">@<?= htmlspecialchars($comment['username']) ?></a>
                            <p class="text-xs mt-0.5" style="color: var(--text-primary);"><?= htmlspecialchars($comment['content']) ?></p>
                        </div>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] === $comment['user_id']): ?>
                            <form action="./core/post/comments/delete_comment.php" method="POST" class="opacity-0 group-hover/comment:opacity-100 transition">
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

        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="./core/post/comments/create_comment.php" method="POST" class="mt-3 flex gap-2">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="post_id" value="<?= $data['id'] ?>">
                <input id="comment-input-<?= $data['id'] ?>" type="text" name="content" placeholder="Escribe un comentario..." class="flex-1 px-4 py-2.5 rounded-xl text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
                <button class="btn-accent px-4 rounded-xl text-sm text-white transition flex items-center gap-1.5 shadow-lg shadow-indigo-500/20">
                    <i class="bi bi-send"></i>
                </button>
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
