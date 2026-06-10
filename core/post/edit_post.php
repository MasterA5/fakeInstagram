<?php
session_start();
include("../db/db.php");
require("./images/upload_image.php");

if (!isset($_SESSION['user_id'])) {
    die("No autorizado");
}

$post_id = $_POST['post_id'];
$user_id = $_SESSION['user_id'];
$content = $_POST['content'];

// verificar dueño
$stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->bind_param("s", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post || $post['user_id'] !== $user_id) {
    die("No autorizado");
}

// subir nueva imagen si hay
$newImage = uploadImage($_FILES['image'] ?? []);

if (!empty($newImage['url'])) {

    $imageUrl = $newImage['url'];

    $stmt = $conn->prepare("UPDATE posts SET content = ?, image = ? WHERE id = ?");
    $stmt->bind_param("ssss", $content, $imageUrl, $deleteUrl, $post_id);

} else {
    // solo texto
    $stmt = $conn->prepare("UPDATE posts SET content = ? WHERE id = ?");
    $stmt->bind_param("ss", $content, $post_id);
}

$stmt->execute();

header("Location: ../../index.php");
exit;