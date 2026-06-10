<?php
session_start();
include("../db/db.php");

// Si ya está logueado → Se manda al index
if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

// Traer datos
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Validar
if (empty($username) || empty($password)) {
    die("Campos vacíos");
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

    // Redirigir
    header("Location: ../../index.php");
    exit;

}
?>