<?php
session_start();
include("../../db/db.php");
include("../../extras/generate_uuid.php");

// 🔒 proteger
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;

if (!$post_id) {
    die("Error");
}

// ¿ya existe like?
$sql = "SELECT id FROM likes WHERE user_id = ? AND post_id = ?";
$id = generateUUID();
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user_id, $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ❌ quitar like
    $sql = "DELETE FROM likes WHERE user_id = ? AND post_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_id, $post_id);
    $stmt->execute();
} else {
    // ❤️ dar like
    $sql = "INSERT INTO likes (id, user_id, post_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $id, $user_id, $post_id);
    $stmt->execute();
}

// volver al feed
header("Location: ../../../index.php");
exit;
?>