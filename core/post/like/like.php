<?php
session_start();
include("../../db/db.php");
include("../../extras/generate_uuid.php");
include("../../extras/csrf.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No autorizado']);
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
    echo json_encode(['error' => 'Falta post_id']);
    exit;
}

$check = $conn->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
$check->bind_param("ss", $user_id, $post_id);
$check->execute();
$existing = $check->get_result();

if ($existing->num_rows > 0) {
    $del = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
    $del->bind_param("ss", $user_id, $post_id);
    $del->execute();
    $liked = false;
} else {
    $id = generateUUID();
    $ins = $conn->prepare("INSERT INTO likes (id, user_id, post_id) VALUES (?, ?, ?)");
    $ins->bind_param("sss", $id, $user_id, $post_id);
    $ins->execute();
    $liked = true;
}

$countQ = $conn->prepare("SELECT COUNT(*) AS c FROM likes WHERE post_id = ?");
$countQ->bind_param("s", $post_id);
$countQ->execute();
$count = $countQ->get_result()->fetch_assoc()['c'];

echo json_encode(['liked' => $liked, 'count' => (int)$count]);
exit;
