<?php
session_start();
include("../db/db.php");
include("../extras/generate_uuid.php");
include("./images/upload_image.php");

// 🔒 proteger ruta
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

// obtener datos
$content = $_POST['content'] ?? '';

// validar
if (empty($content)) {
    die("Post vacío");
}

// obtener usuario desde sesión
$user_id = $_SESSION['user_id'];

// 🔥 subir imagen
$imageUrl = uploadImage($_FILES['image'] ?? []);

$id = generateUUID();

// insertar
$sql = "INSERT INTO posts (id, user_id, image, content) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $id, $user_id, $imageUrl, $content);
$stmt->execute();

// redirigir
header("Location: ../../index.php");
exit;
?>