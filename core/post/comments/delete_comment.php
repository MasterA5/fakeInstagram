<?php
session_start();
include("../../db/db.php");

// 🔒 proteger
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$comment_id = $_POST['comment_id'] ?? null;

if (!$comment_id) {
    die("Error");
}

// 🧠 borrar SOLO si es del usuario
$sql = "DELETE FROM comments WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $comment_id, $user_id);
$stmt->execute();

// volver
header("Location: ../../../index.php");
exit;
?>