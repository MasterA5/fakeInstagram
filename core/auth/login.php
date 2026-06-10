<?php
session_start();
include("../db/db.php");
include("../extras/csrf.php");

// Si ya está logueado → Se manda al index
if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

// Traer datos
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$csrf_token = $_POST['csrf_token'] ?? '';

// Validar CSRF
if (!verifyCsrfToken($csrf_token)) {
    $_SESSION['login_error'] = "Error de validación";
    header("Location: ../../index.php");
    exit;
}

// Validar
if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = "Campos vacíos";
    header("Location: ../../index.php");
    exit;
}

// Buscar al usuario
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verificar password
if ($user && password_verify($password, $user['password'])) {

    // Crear sesión
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['avatar'] = $user['avatar'];
    unset($_SESSION['login_error']);

    // Redirigir
    header("Location: ../../index.php");
    exit;

} else {
    $_SESSION['login_error'] = "Usuario o contraseña incorrectos";
    header("Location: ../../index.php");
    exit;
}
?>