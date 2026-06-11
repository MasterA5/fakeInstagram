<?php
function getProfile($conn, $profile_id) {
    $stmt = $conn->prepare("SELECT id, username, display_name, bio, avatar, theme, created_at FROM users WHERE id = ?");
    $stmt->bind_param("s", $profile_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getProfileByUsername($conn, $username) {
    $stmt = $conn->prepare("SELECT id, username, display_name, bio, avatar, theme, created_at FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getFollowerCount($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM follows WHERE followed_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

function getFollowingCount($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM follows WHERE follower_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'];
}

function isFollowing($conn, $follower_id, $followed_id) {
    if ($follower_id === $followed_id) return false;
    $stmt = $conn->prepare("SELECT 1 FROM follows WHERE follower_id = ? AND followed_id = ?");
    $stmt->bind_param("ss", $follower_id, $followed_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

function getSuggestedUsers($conn, $user_id, $limit = 5) {
    $stmt = $conn->prepare("SELECT id, username, display_name, avatar FROM users WHERE id != ? AND id NOT IN (SELECT followed_id FROM follows WHERE follower_id = ?) ORDER BY RAND() LIMIT ?");
    $stmt->bind_param("ssi", $user_id, $user_id, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
