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

$follower_id = $_SESSION['user_id'];
$followed_id = $_POST['followed_id'] ?? null;

if (!$followed_id || $followed_id === $follower_id) {
    header("Location: ../../index.php");
    exit;
}

$check = $conn->prepare("SELECT 1 FROM follows WHERE follower_id = ? AND followed_id = ?");
$check->bind_param("ss", $follower_id, $followed_id);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    $stmt = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND followed_id = ?");
    $stmt->bind_param("ss", $follower_id, $followed_id);
    $stmt->execute();
} else {
    $stmt = $conn->prepare("INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)");
    $stmt->bind_param("ss", $follower_id, $followed_id);
    $stmt->execute();
}

$referer = $_SERVER['HTTP_REFERER'] ?? '';
$host = $_SERVER['HTTP_HOST'] ?? '';
$allowed = $host && strpos($referer, $host) !== false;
header("Location: " . ($allowed ? $referer : '../../index.php'));
exit;
