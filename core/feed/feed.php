<?php
include("./core/db/db.php");
include("./core/extras/format_datetime.php");

// NOTE: Si es posible en vez de usar una api gratis de imagenes random
// NOTE: Usar o implementar un sistema de subida de imagenes
// TODO: Buscar Servidores de Alojamiento de imagenes

// timezone adecuado para que la funcion de parsear el tiempo funcione correctamente
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Traer posts + likes
$sql = "SELECT posts.*, users.username,
               COUNT(likes.id) AS likes_count
        FROM posts
        JOIN users ON posts.user_id = users.id
        LEFT JOIN likes ON likes.post_id = posts.id
        GROUP BY posts.id
        ORDER BY posts.created_at DESC";

$result = $conn->query($sql);
?>

<div class="space-y-6">

    <?php while ($data = $result->fetch_assoc()): ?>

        <div class="bg-zinc-900 border border-zinc-800 p-4 rounded-xl shadow border-2">

            <!-- 👤 Usuario -->
            <div class="flex items-center gap-3">

                <h3 class="text-indigo-100 font-semibold">
                    @<?= htmlspecialchars($data['username']) ?>
                </h3>
            </div>

            <?php if (!empty($data['image'])): ?>
                <div class="mt-3 rounded-2xl overflow-hidden border border-zinc-800 bg-zinc-900 aspect-square">
                    <img
                        src="<?= htmlspecialchars($data['image']) ?>"
                        class="w-full h-full object-cover"
                        loading="lazy" ,>
                </div>
            <?php endif; ?>

            <!-- 📝 Contenido -->
            <p class="mt-2 text-zinc-200">
                <?= htmlspecialchars($data['content']) ?>
            </p>

            <!-- 📅 Fecha -->
            <p class="text-xs text-zinc-500 mt-2">
                <?= timeAgo($data['created_at']) ?>
            </p>

            <!-- ❤️ Like -->
            <form action="./core/post/like/like.php" method="POST" class="mt-3">
                <input type="hidden" name="post_id" value="<?= $data['id'] ?>">
                <button
                    class="flex items-center gap-2 text-zinc-400 hover:text-pink-500 transition-colors group">

                    <!-- icon -->
                    <span class="text-lg transform group-hover:scale-110 transition-transform">
                        ❤️
                    </span>

                    <!-- contador -->
                    <span class="text-sm">
                        <?= $data['likes_count'] ?>
                    </span>

                </button>

            </form>

            <!-- Editar post -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $data['user_id']): ?>

                <button
                    onclick="toggleEdit('<?= $data['id'] ?>')"
                    class="text-sm text-yellow-400 hover:text-yellow-600 ml-2">
                    ✏️ Editar
                </button>

            <?php endif; ?>

            <div id="edit-<?= $data['id'] ?>" class="hidden mt-3">

                <form action="./core/post/edit_post.php" method="POST" enctype="multipart/form-data">

                    <input type="hidden" name="post_id" value="<?= $data['id'] ?>">

                    <textarea
                        name="content"
                        class="w-full bg-zinc-800 border border-zinc-700 rounded-lg p-2 text-sm text-white"><?= htmlspecialchars($data['content']) ?></textarea>

                    <!-- cambiar imagen -->
                    <input
                        type="file"
                        name="image"
                        class="mt-2 text-xs text-zinc-400">

                    <div class="flex gap-2 mt-2">
                        <button class="bg-indigo-600 px-3 py-1 rounded text-sm">
                            Guardar
                        </button>

                        <button
                            type="button"
                            onclick="toggleEdit('<?= $data['id'] ?>')"
                            class="text-zinc-400 text-sm">
                            Cancelar
                        </button>
                    </div>

                </form>

            </div>

            <script>
                function toggleEdit(id) {
                    const el = document.getElementById("edit-" + id);
                    el.classList.toggle("hidden");
                }
            </script>

            <!-- 🗑 Eliminar post -->
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $data['user_id']): ?>
                <form action="./core/post/delete_post.php" method="POST" class="mt-2">
                    <input type="hidden" name="post_id" value="<?= $data['id'] ?>">
                    <!-- En firefox no funciona -->
                    <button onclick="return confirm('¿Eliminar post?')"
                        class="text-sm text-red-400 hover:text-red-600">
                        🗑 Eliminar
                    </button>
                </form>
            <?php endif; ?>

            <!-- 💬 Comentarios -->
            <div class="mt-4 space-y-2">

                <?php
                $comments_sql = "SELECT comments.*, users.username 
                     FROM comments
                     JOIN users ON comments.user_id = users.id
                     WHERE comments.post_id = ?
                     ORDER BY comments.created_at ASC";

                $stmt = $conn->prepare($comments_sql);
                // el comentario no se mostraba porque le estaba pasando el id del usuario en vez del id del post
                $stmt->bind_param("s", $data['id']); // <- Corregido
                $stmt->execute();
                $comments = $stmt->get_result();
                ?>

                <?php while ($comment = $comments->fetch_assoc()): ?>

                    <div class="bg-zinc-800 p-2 rounded-lg text-sm flex justify-between items-center">

                        <div>
                            <span class="text-indigo-400 font-semibold">
                                <?= htmlspecialchars($comment['username']) ?>:
                            </span>
                            <span class="text-zinc-300">
                                <?= htmlspecialchars($comment['content']) ?>
                            </span>
                        </div>

                        <!-- 🗑 eliminar comentario -->
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']): ?>
                            <form action="./core/post/comments/delete_comment.php" method="POST">
                                <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                                <button onclick="return confirm('¿Eliminar comentario?')"
                                    class="text-xs text-red-400 hover:text-red-600 ml-2">
                                    🗑
                                </button>
                            </form>
                        <?php endif; ?>

                    </div>

                <?php endwhile; ?>

            </div>

            <!-- ✍️ Formulario comentario -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="./core/post/comments/create_comment.php" method="POST" class="mt-3 flex gap-2">
                    <input type="hidden" name="post_id" value="<?= $data['id'] ?>">

                    <input
                        type="text"
                        name="content"
                        placeholder="Escribe un comentario..."
                        class="flex-1 px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-lg text-sm focus:outline-none text-white">

                    <button class="bg-indigo-600 px-3 py-2 rounded-lg text-sm">
                        Enviar
                    </button>
                </form>
            <?php endif; ?>


        </div>

    <?php endwhile; ?>

</div>