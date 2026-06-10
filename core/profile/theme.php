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

$palette = trim($_POST['palette'] ?? '');
if (!empty($palette)) {
    $paletteData = json_decode($palette, true);
    if (!is_array($paletteData)) {
        $palette = null;
    }
} else {
    $palette = null;
}

$stmt = $conn->prepare("UPDATE users SET theme = ?, palette = ? WHERE id = ?");
$stmt->bind_param("sss", $theme, $palette, $_SESSION['user_id']);
$stmt->execute();

$_SESSION['theme'] = $theme;
header("Location: ../../index.php?settings&tab=theme");
exit;
