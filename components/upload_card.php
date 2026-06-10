<form action="./core/post/create_post.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

    <div class="card border rounded-2xl p-4 card-hover shadow-sm" style="border-color: var(--border);">
        <div class="flex gap-3">
            <img src="<?= htmlspecialchars($_SESSION['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-10 h-10 rounded-full flex-shrink-0">
            <div class="flex-1">
                <textarea name="content" placeholder="¿Qué estás pensando?" rows="2" class="w-full bg-transparent text-sm placeholder-zinc-500 resize-none focus:outline-none" style="color: var(--text-primary);"></textarea>
            </div>
        </div>

        <div class="flex items-center justify-between mt-3 pt-3" style="border-top: 1px solid var(--border);">
            <label class="flex items-center gap-2 text-xs text-muted hover:text-indigo-400 cursor-pointer transition px-3 py-1.5 rounded-lg hover:bg-zinc-800">
                <i class="bi bi-image text-lg"></i>
                <span class="hidden sm:inline">Foto</span>
                <input type="file" name="image" accept="image/*" onchange="previewImage(event)" class="hidden">
            </label>

            <button type="submit" class="btn-accent px-5 py-2 rounded-xl text-sm font-semibold transition text-white flex items-center gap-1.5 shadow-lg shadow-indigo-500/20 hover:shadow-xl hover:shadow-indigo-500/30">
                <i class="bi bi-send"></i>
                Publicar
            </button>
        </div>

        <img id="preview" class="mt-3 rounded-xl hidden max-h-60 object-cover w-full">
    </div>
</form>

<script>
    function previewImage(event) {
        const img = document.getElementById("preview");
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) { img.src = e.target.result; img.classList.remove("hidden"); };
            reader.readAsDataURL(file);
        }
    }
</script>
