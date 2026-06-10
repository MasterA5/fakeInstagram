<?php
session_start();
include("../db/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;

if (!$post_id) {
    die("Error");
}

// 🔐 verificar que el post sea del usuario
$check = $conn->prepare("SELECT id FROM posts WHERE id = ? AND user_id = ?");
$check->bind_param("ss", $post_id, $user_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    die("No autorizado");
}

// 🔥 BORRAR TODO RELACIONADO
$conn->begin_transaction();

try {

    // borrar comentarios
    $stmt1 = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
    $stmt1->bind_param("s", $post_id);
    $stmt1->execute();

    // borrar likes
    $stmt2 = $conn->prepare("DELETE FROM likes WHERE post_id = ?");
    $stmt2->bind_param("s", $post_id);
    $stmt2->execute();

    // borrar post
    $stmt3 = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt3->bind_param("s", $post_id);
    $stmt3->execute();

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    die("Error al eliminar");
}

// volver
header("Location: ../../index.php");
exit;
?>