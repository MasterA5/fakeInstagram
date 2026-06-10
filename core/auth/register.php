<?php
session_start();

// 🔒 si ya está logueado, redirigir al login (NOTE: sacado de stack overflow)
if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

// 🔒 solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../index.php");
    exit;
}

include("../db/db.php");
include("../extras/generate_uuid.php");

// TODO[☑]: Crear un sistema de uuid para identificadores unicos (uuid)

// Traer datos
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validación del usuario
// TODO: Mejorar la autenticacion
// TODO: Buscar mejores maneras de autenticar y validar datos
if (empty($username) || empty($email) || empty($password)) {
    die("Campos vacíos");
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

// Hash de contraseña
// NOTE: ❌ No se debe guardar la contraseña en texto plano en la base de datos
// NOTE: ❌ Tampoco el hash debe ser demasiado grande
$hash = password_hash($password, PASSWORD_DEFAULT);

// Generar un uuid para un id mas seguro
$id = generateUUID();

// Insertar al usuario
$sql = "INSERT INTO users (id, username, email, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $id, $username, $email, $hash);

if ($stmt->execute()) {

    // ✔ Crear la sesión automáticamente
    $_SESSION['user_id'] = $id;
    $_SESSION['username'] = $username;

    // Redirigir al index.php
    // TODO[☑]: Hacer un render condicional en el index para 
    // No mostrar nada protejido a un usuario no autenticado
    header("Location: ../../index.php");
    exit;

} else {
    die("Error al registrar usuario");
}
?>
