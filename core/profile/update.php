<?php
session_start();
include("../db/db.php");
include("../extras/csrf.php");
include("../extras/generate_uuid.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrf_token)) {
    die("Error de validación");
}

$user_id = $_SESSION['user_id'];
$username = trim($_POST['username'] ?? '');
$display_name = trim($_POST['display_name'] ?? '');
$bio = trim($_POST['bio'] ?? '');
$email = trim($_POST['email'] ?? '');
$avatar = trim($_POST['avatar'] ?? '');

// Handle local file upload (takes priority over URL)
if (!empty($_FILES['avatar_file']['tmp_name'])) {
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['avatar_file']['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed)) {
        die("Formato de imagen no permitido (JPEG, PNG, GIF, WebP)");
    }

    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
        default      => 'jpg'
    };

    $filename = generateUUID() . '.' . $ext;
    $dest = __DIR__ . '/../../uploads/avatars/' . $filename;

    if (!move_uploaded_file($_FILES['avatar_file']['tmp_name'], $dest)) {
        die("Error al subir la imagen");
    }

    $avatar = 'uploads/avatars/' . $filename;
}

if (empty($username) || empty($email)) {
    die("Campos requeridos vacíos");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Email inválido");
}

$check = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
$check->bind_param("sss", $username, $email, $user_id);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    die("Usuario o email ya en uso");
}

$stmt = $conn->prepare("UPDATE users SET username = ?, display_name = ?, bio = ?, email = ?, avatar = ? WHERE id = ?");
$stmt->bind_param("ssssss", $username, $display_name, $bio, $email, $avatar, $user_id);
$stmt->execute();

$_SESSION['username'] = $username;
$_SESSION['avatar'] = $avatar;
header("Location: ../../index.php?settings");
exit;
