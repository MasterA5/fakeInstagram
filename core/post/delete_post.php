<?php
session_start();
include("../db/db.php");
include("../extras/csrf.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrf_token)) {
    echo json_encode(['error' => 'Error de validación']);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'] ?? null;

if (!$post_id) {
    echo json_encode(['error' => 'ID no válido']);
    exit;
}

$check = $conn->prepare("SELECT id FROM posts WHERE id = ? AND user_id = ?");
$check->bind_param("ss", $post_id, $user_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$conn->begin_transaction();

try {

    $stmt1 = $conn->prepare("DELETE FROM comments WHERE post_id = ?");
    $stmt1->bind_param("s", $post_id);
    $stmt1->execute();

    $stmt2 = $conn->prepare("DELETE FROM likes WHERE post_id = ?");
    $stmt2->bind_param("s", $post_id);
    $stmt2->execute();

    $stmt3 = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt3->bind_param("s", $post_id);
    $stmt3->execute();

    $conn->commit();
    echo json_encode(['success' => true]);
    exit;

} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['error' => 'Error al eliminar']);
    exit;
}
