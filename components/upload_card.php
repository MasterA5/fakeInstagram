<form action="./core/post/create_post.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

    <div class="card border rounded-xl p-4 mb-4 shadow-sm" style="border-color: var(--border);">
        <div class="flex gap-3">
            <img src="<?= htmlspecialchars($_SESSION['avatar'] ?? 'https://api.dicebear.com/7.x/avataaars/svg?seed=default') ?>" class="w-8 h-8 rounded-full flex-shrink-0">
            <textarea name="content" placeholder="¿Qué está pasando?" rows="1" class="flex-1 bg-transparent text-sm resize-none focus:outline-none py-1" style="color: var(--text-primary);"></textarea>
        </div>

        <div class="flex items-center justify-between mt-3 pt-3" style="border-top: 1px solid var(--border);">
            <label class="flex items-center gap-1.5 text-sm text-muted hover:text-[var(--accent)] cursor-pointer transition px-2 py-1 rounded-lg hover:bg-[var(--bg-card-hover)]">
                <i class="bi bi-image text-lg"></i>
                <input type="file" name="image" accept="image/*" onchange="previewImage(event)" class="hidden">
            </label>

            <button type="submit" class="px-5 py-1.5 rounded-lg text-sm font-semibold text-white transition" style="background: var(--accent);">
                Compartir
            </button>
        </div>

        <img id="preview" class="mt-3 rounded-lg hidden max-h-60 object-cover w-full">
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
