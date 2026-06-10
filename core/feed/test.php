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

<?php while ($data = $result->fetch_assoc()): ?>
    <?php include("./components/post_card.php"); ?>
<?php endwhile; ?>