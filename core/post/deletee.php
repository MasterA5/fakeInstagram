<?php
session_start();
include("../db/db.php");

// 🔒 proteger ruta
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;

if (!$post_id) {
    die("Error");
}

// 🧠 borrar SOLO si el post es del usuario
$sql = "DELETE FROM posts WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $post_id, $user_id);
$stmt->execute();

// volver al feed
header("Location: ../../index.php");
exit;
?>