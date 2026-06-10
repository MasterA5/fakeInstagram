<div class="space-y-6">
    <div class="bg-zinc-900 border border-zinc-800 p-4 rounded-xl shadow border-2">
        <!-- 👤 Header -->
        <div class="flex items-center justify-between">

            <div class="flex items-center gap-3">
                <i class="bi bi-person-circle text-4xl text-zinc-400"></i>

                <h3 class="text-indigo-100 font-semibold">
                    @<?= htmlspecialchars($data['username']) ?>
                </h3>
            </div>

            <!-- Dropdown -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $data['user_id']): ?>
                <div class="relative">
                    <button
                        onclick="toggleMenu('menu-<?= $data['id'] ?>')"
                        class="text-zinc-400 hover:text-white text-xl px-2">
                        <i class="bi bi-three-dots text-white"></i>
                    </button>

                    <div id="menu-<?= $data['id'] ?>"
                        class="hidden absolute right-0 mt-2 w-36 bg-zinc-800 border border-zinc-700 rounded-xl shadow-lg z-50">

                        <button
                            onclick="toggleEdit('<?= $data['id'] ?>')"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-zinc-700 text-yellow-400 flex items-center gap-2">
                            <i class="bi bi-pencil"></i> Editar
                        </button>

                        <form action="./core/post/delete_post.php" method="POST">
                            <input type="hidden" name="post_id" value="<?= $data['id'] ?>">
                            <button
                                onclick="return confirm('¿Eliminar post?')"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-zinc-700 text-red-400 flex items-center gap-2">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </form>

                    </div>
                </div>
            <?php endif; ?>

        </div>
        
        <!-- 📸 Imagen -->
        <?php if (!empty($data['image'])): ?>
            <div class="mt-3 rounded-2xl overflow-hidden border border-zinc-800 bg-black aspect-square">
                <img
                    src="<?= htmlspecialchars($data['image']) ?>"
                    class="w-full h-full object-cover transition duration-300"
                    loading="lazy">
            </div>
        <?php endif; ?>

        <!-- 📝 Contenido -->
        <p class="mt-3 text-zinc-200">
            <?= htmlspecialchars($data['content']) ?>
        </p>

        <!-- 📅 Fecha -->
        <p class="text-xs text-zinc-500 mt-2 flex items-center gap-2">
            <i class="bi bi-clock"></i>
            <?= timeAgo($data['created_at']) ?>
        </p>

        <!-- ❤️ Like -->
        <form action="./core/post/like/like.php" method="POST" class="mt-3">
            <input type="hidden" name="post_id" value="<?= $data['id'] ?>">

            <button class="flex items-center gap-2 text-zinc-400 hover:text-pink-500 transition group">
                <i class="bi bi-heart text-lg group-hover:scale-110 transition"></i>
                <span class="text-sm"><?= $data['likes_count'] ?></span>
            </button>
        </form>

        <!-- ✏️ Edit Form -->
        <div id="edit-<?= $data['id'] ?>" class="hidden mt-3">
            <form action="./core/post/edit_post.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="post_id" value="<?= $data['id'] ?>">

                <textarea
                    name="content"
                    class="w-full bg-zinc-800 border border-zinc-700 rounded-lg p-2 text-sm text-white"><?= htmlspecialchars($data['content']) ?></textarea>

                <input
                    type="file"
                    name="image"
                    class="mt-2 text-xs text-zinc-400">

                <div class="flex gap-2 mt-2">
                    <button class="bg-indigo-600 px-3 py-1 rounded text-sm flex items-center gap-1">
                        <i class="bi bi-check-lg"></i> Guardar
                    </button>

                    <button
                        type="button"
                        onclick="toggleEdit('<?= $data['id'] ?>')"
                        class="text-zinc-400 text-sm flex items-center gap-1">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </button>
                </div>

            </form>

        </div>

        <!-- 💬 Comentarios -->
        <div class="mt-4 space-y-2">
            <?php
            $stmt = $conn->prepare("SELECT comments.*, users.username 
                        FROM comments
                        JOIN users ON comments.user_id = users.id
                        WHERE comments.post_id = ?
                        ORDER BY comments.created_at ASC");

            $stmt->bind_param("s", $data['id']);
            $stmt->execute();
            $comments = $stmt->get_result();
            ?>

            <?php while ($comment = $comments->fetch_assoc()): ?>

                <div class="bg-zinc-800 p-2 rounded-lg text-sm flex justify-between items-center">

                    <div class="flex items-center gap-2">
                        <i class="bi bi-person text-zinc-500"></i>

                        <div>
                            <span class="text-indigo-400 font-semibold">
                                @<?= htmlspecialchars($comment['username']) ?>:
                            </span>
                            <span class="text-zinc-300">
                                <?= htmlspecialchars($comment['content']) ?>
                            </span>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                        <form action="./core/post/comments/delete_comment.php" method="POST">
                            <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                            <button class="text-xs text-red-400 hover:text-red-600">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    <?php endif; ?>

                </div>

            <?php endwhile; ?>

        </div>

        <!-- ✍️ Formulario -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="./core/post/comments/create_comment.php" method="POST" class="mt-3 flex gap-2">
                <input type="hidden" name="post_id" value="<?= $data['id'] ?>">

                <input
                    type="text"
                    name="content"
                    placeholder="Escribe un comentario..."
                    class="flex-1 px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-sm text-white">

                <button class="bg-indigo-600 px-3 py-2 rounded-lg text-sm flex items-center gap-1">
                    <i class="bi bi-send"></i>
                </button>

            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleEdit(id) {
        document.getElementById("edit-" + id).classList.toggle("hidden");
    }

    function toggleMenu(id) {
        document.getElementById(id).classList.toggle("hidden");
    }

    // cerrar dropdown al hacer click afuera (totalmente insescesario)
    document.addEventListener("click", function(e) {
        document.querySelectorAll("[id^='menu-']").forEach(menu => {
            if (!menu.previousElementSibling.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add("hidden");
            }
        });
    });
</script>