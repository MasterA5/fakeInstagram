<div class="space-y-4">
    <form action="./core/profile/update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

        <div class="flex items-center gap-4 mb-5">
            <img src="<?= htmlspecialchars($profile['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-14 h-14 rounded-full ring-2 ring-[var(--accent)]/30 object-cover" id="avatarPreview">
            <div class="flex-1 space-y-2">
                <div>
                    <input type="file" name="avatar_file" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full text-xs file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[var(--accent)] file:text-white hover:file:opacity-90 transition">
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] text-muted">o URL:</span>
                    <input type="url" name="avatar" value="<?= htmlspecialchars($profile['avatar'] ?? '') ?>" placeholder="https://..." class="flex-1 px-2 py-1 rounded-lg text-xs transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
                </div>
            </div>
        </div>

        <div>
            <input type="text" name="username" value="<?= htmlspecialchars($profile['username'] ?? '') ?>" required placeholder="Usuario" class="w-full px-3 py-2 rounded-lg text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
        </div>

        <div>
            <input type="text" name="display_name" value="<?= htmlspecialchars($profile['display_name'] ?? '') ?>" placeholder="Nombre visible" class="w-full px-3 py-2 rounded-lg text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
        </div>

        <div>
            <textarea name="bio" rows="2" placeholder="Biografía" class="w-full px-3 py-2 rounded-lg text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
        </div>

        <div>
            <input type="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" required placeholder="Email" class="w-full px-3 py-2 rounded-lg text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
        </div>

        <div class="flex gap-2 pt-1">
            <button type="submit" class="px-5 py-2 rounded-lg text-xs font-semibold text-white transition" style="background: var(--accent);">
                <i class="bi bi-check-lg mr-1"></i> Guardar
            </button>
            <a href="?settings" class="px-4 py-2 rounded-lg text-xs transition" style="background: var(--bg-card); border: 1px solid var(--border); color: var(--text-secondary);">
                Cancelar
            </a>
        </div>
    </form>
</div>
