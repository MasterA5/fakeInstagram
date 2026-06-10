<?php
session_start();
include("../db/db.php");
require("./images/upload_image.php");
include("../extras/csrf.php");

if (!isset($_SESSION['user_id'])) {
    die("No autorizado");
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrf_token)) {
    die("Error de validación");
}

$post_id = $_POST['post_id'] ?? null;
$user_id = $_SESSION['user_id'];
$content = trim($_POST['content'] ?? '');

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
$imageUrl = uploadImage($_FILES['image'] ?? []);

if (!empty($imageUrl)) {
    $stmt = $conn->prepare("UPDATE posts SET content = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sss", $content, $imageUrl, $post_id);
} else {
    $stmt = $conn->prepare("UPDATE posts SET content = ? WHERE id = ?");
    $stmt->bind_param("ss", $content, $post_id);
}

$stmt->execute();

header("Location: ../../index.php");
exit;