<div class="space-y-5">
    <form action="./core/profile/update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

        <div class="flex items-center gap-4 mb-5 p-4 rounded-xl" style="background: var(--bg-primary);">
            <img src="<?= htmlspecialchars($profile['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-16 h-16 rounded-full ring-2 ring-indigo-500/30 object-cover" id="avatarPreview">
            <div class="flex-1 space-y-3">
                <div>
                    <label class="block text-xs mb-1 text-muted font-medium">Subir foto</label>
                    <input type="file" name="avatar_file" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 transition" style="color: var(--text-muted);">
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-muted">o</span>
                    <input type="url" name="avatar" value="<?= htmlspecialchars($profile['avatar'] ?? '') ?>" placeholder="URL externa..." class="flex-1 px-3 py-1.5 rounded-xl text-xs transition" style="background: var(--bg-card); border: 1px solid var(--border); color: var(--text-primary);">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs mb-1.5 text-muted font-medium">Nombre de usuario</label>
            <input type="text" name="username" value="<?= htmlspecialchars($profile['username'] ?? '') ?>" required class="w-full px-4 py-2.5 rounded-xl text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
        </div>

        <div>
            <label class="block text-xs mb-1.5 text-muted font-medium">Nombre visible</label>
            <input type="text" name="display_name" value="<?= htmlspecialchars($profile['display_name'] ?? '') ?>" placeholder="Tu nombre público" class="w-full px-4 py-2.5 rounded-xl text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
        </div>

        <div>
            <label class="block text-xs mb-1.5 text-muted font-medium">Biografía</label>
            <textarea name="bio" rows="3" placeholder="Cuéntanos sobre ti" class="w-full px-4 py-2.5 rounded-xl text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
        </div>

        <div>
            <label class="block text-xs mb-1.5 text-muted font-medium">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" required class="w-full px-4 py-2.5 rounded-xl text-sm transition" style="background: var(--bg-primary); border: 1px solid var(--border); color: var(--text-primary);">
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-accent px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition flex items-center gap-1.5 shadow-lg shadow-indigo-500/20">
                <i class="bi bi-check-lg"></i> Guardar cambios
            </button>
            <a href="?settings" class="btn-ghost px-6 py-2.5 rounded-xl text-sm transition flex items-center gap-1.5 text-muted hover:text-white">
                <i class="bi bi-x-lg"></i> Cancelar
            </a>
        </div>
    </form>
</div>
