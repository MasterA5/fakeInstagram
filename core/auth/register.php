<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../index.php");
    exit;
}

include("../db/db.php");
include("../extras/generate_uuid.php");
include("../extras/csrf.php");

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrf_token)) {
    die("Error de validación");
}

// Traer datos
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$display_name = $_POST['display_name'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

// Validación del usuario
if (empty($username) || empty($email) || empty($password)) {
    die("Campos vacíos");
}

if ($password !== $password_confirm) {
    die("Las contraseñas no coinciden");
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Email inválido");
}

// Verificar si ya existe usuario o email
$sql = "SELECT id FROM users WHERE email = ? OR username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Usuario o email ya existe");
}

$hash = password_hash($password, PASSWORD_DEFAULT);

// Generar un uuid para un id mas seguro
$id = generateUUID();

$avatar = 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($username);

// Insertar al usuario
$sql = "INSERT INTO users (id, username, email, password, avatar, display_name) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $id, $username, $email, $hash, $avatar, $display_name);

if ($stmt->execute()) {

    $_SESSION['user_id'] = $id;
    $_SESSION['username'] = $username;
    $_SESSION['avatar'] = 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($username);

    header("Location: ../../index.php");
    exit;

} else {
    die("Error al registrar usuario");
}
?>
