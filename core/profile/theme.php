<?php
session_start();
include("../db/db.php");
include("../extras/csrf.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrf_token)) {
    die("Error de validación");
}

$theme = $_POST['theme'] ?? 'dark';
$allowed = ['dark', 'light', 'ocean', 'sunset'];

if (!in_array($theme, $allowed)) {
    $theme = 'dark';
}

$stmt = $conn->prepare("UPDATE users SET theme = ? WHERE id = ?");
$stmt->bind_param("ss", $theme, $_SESSION['user_id']);
$stmt->execute();

header("Location: ../../index.php?settings&tab=theme");
exit;
