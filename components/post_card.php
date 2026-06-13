<?php require_once(__DIR__ . '/../core/extras/format_datetime.php') ?>
<div class="card border rounded-none sm:rounded-xl mb-4 animate-slide-up" style="border-color: var(--border);" data-post-card="<?= $data['id'] ?>">
    <div class="p-3 sm:p-4">
        <div class="flex items-center justify-between mb-3">
            <a href="?profile=<?= urlencode($data['user_id']) ?>" class="flex items-center gap-3 group">
                <img src="<?= htmlspecialchars($data['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-8 h-8 rounded-full ring-2 ring-transparent group-hover:ring-[var(--accent)]/50 transition-all object-cover">
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
                        <button class="delete-post-btn w-full text-left px-3 py-2.5 text-sm hover:bg-[var(--bg-card-hover)] flex items-center gap-2 transition" style="color: #ef4444;" data-post-id="<?= $data['id'] ?>" data-csrf="<?= generateCsrfToken() ?>">
                            <i class="bi bi-trash text-xs"></i> Eliminar
                        </button>
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
            <button class="comment-btn flex items-center gap-1 text-muted hover:text-[var(--accent)] transition text-2xl" data-post-id="<?= $data['id'] ?>" data-csrf="<?= generateCsrfToken() ?>" data-author-name="<?= htmlspecialchars($data['display_name'] ?? $data['username']) ?>" data-author-avatar="<?= htmlspecialchars($data['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" data-content="<?= htmlspecialchars($data['content']) ?>" data-image="<?= htmlspecialchars($data['image'] ?? '') ?>">
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

        <div class="text-[11px] text-muted mt-2">
            <?= timeAgo($data['created_at']) ?>
        </div>

        <div id="edit-<?= $data['id'] ?>" class="hidden mt-3 p-3 rounded-xl animate-fade-in" style="background: var(--bg-primary);">
            <form class="edit-post-form" action="./core/post/edit_post.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="post_id" value="<?= $data['id'] ?>">
                <textarea name="content" rows="2" class="w-full px-3 py-2 rounded-xl text-sm transition mb-2" style="background: var(--bg-card); border: 1px solid var(--border); color: var(--text-primary);"><?= htmlspecialchars($data['content']) ?></textarea>
                <input type="file" name="image" class="text-xs text-muted file:mr-2 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-[var(--bg-card-hover)] file:text-white hover:file:bg-[var(--border)]">
                <div class="flex gap-2 mt-3">
                    <button type="submit" class="btn-accent px-4 py-2 rounded-xl text-sm text-white font-semibold transition shadow-lg">
                        <i class="bi bi-check-lg"></i> Guardar
                    </button>
                    <button type="button" onclick="toggleEdit('<?= $data['id'] ?>')" class="btn-ghost px-4 py-2 rounded-xl text-sm transition text-muted hover:text-white">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
