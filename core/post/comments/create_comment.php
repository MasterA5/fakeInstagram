<?php
session_start();
include("../../db/db.php");
include("../../extras/generate_uuid.php");
include("../../extras/csrf.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrf_token)) {
    die("Error de validación");
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;
$content = trim($_POST['content'] ?? '');

if (!$post_id || empty($content)) {
    die("Error");
}

$id = generateUUID();

// insertar comentario
$sql = "INSERT INTO comments (id, user_id, post_id, content) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $id, $user_id, $post_id, $content);
$result = $stmt->execute();

// volver
header("Location: ../../../index.php");
exit;
?>