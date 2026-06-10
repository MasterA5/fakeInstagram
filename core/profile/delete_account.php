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

$user_id = $_SESSION['user_id'];

$conn->begin_transaction();
try {
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM comments WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM follows WHERE follower_id = ? OR followed_id = ?");
    $stmt->bind_param("ss", $user_id, $user_id);
    $stmt->execute();

    $posts = $conn->prepare("SELECT id FROM posts WHERE user_id = ?");
    $posts->bind_param("s", $user_id);
    $posts->execute();
    $result = $posts->get_result();

    while ($post = $result->fetch_assoc()) {
        $stmt = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
        $stmt->bind_param("s", $post['id']);
        $stmt->execute();

        $stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ?");
        $stmt->bind_param("s", $post['id']);
        $stmt->execute();
    }

    $stmt = $conn->prepare("DELETE FROM posts WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();

    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    die("Error al eliminar cuenta");
}

session_destroy();
header("Location: ../../index.php");
exit;
