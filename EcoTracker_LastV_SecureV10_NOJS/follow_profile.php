<?php
session_start();
include 'Conection.php';

header('Content-Type: application/json');

function respond(int $status, bool $ok, array $extra = []): void
{
    http_response_code($status);
    echo json_encode(array_merge(['ok' => $ok], $extra));
    exit();
}

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    respond(401, false, ['message' => 'You need to log in first.']);
}

$viewerId = (int) $_SESSION['usuario_id'];
$followedUserId = (int) ($_POST['followed_user_id'] ?? 0);

if ($followedUserId <= 0) {
    respond(400, false, ['message' => 'Invalid profile.']);
}

if ($followedUserId === $viewerId) {
    respond(400, false, ['message' => 'You cannot follow yourself.']);
}

$userCheck = $conn->prepare("SELECT id FROM usuarios WHERE id = ? LIMIT 1");
$userCheck->bind_param("i", $followedUserId);
$userCheck->execute();
$userExists = $userCheck->get_result()->num_rows > 0;
$userCheck->close();

if (!$userExists) {
    respond(404, false, ['message' => 'User not found.']);
}

$check = $conn->prepare("SELECT id FROM profile_follows WHERE followed_user_id = ? AND follower_user_id = ? LIMIT 1");
$check->bind_param("ii", $followedUserId, $viewerId);
$check->execute();
$result = $check->get_result();

if ($result && $result->num_rows > 0) {
    $delete = $conn->prepare("DELETE FROM profile_follows WHERE followed_user_id = ? AND follower_user_id = ?");
    $delete->bind_param("ii", $followedUserId, $viewerId);
    $delete->execute();
    $delete->close();

    $following = false;
} else {
    $insert = $conn->prepare("INSERT INTO profile_follows (followed_user_id, follower_user_id) VALUES (?, ?)");
    $insert->bind_param("ii", $followedUserId, $viewerId);
    $insert->execute();
    $insert->close();

    $following = true;
}

$check->close();

$followersStmt = $conn->prepare("SELECT COUNT(*) AS total FROM profile_follows WHERE followed_user_id = ?");
$followersStmt->bind_param("i", $followedUserId);
$followersStmt->execute();
$followers = (int) $followersStmt->get_result()->fetch_assoc()['total'];
$followersStmt->close();

$followingStmt = $conn->prepare("SELECT COUNT(*) AS total FROM profile_follows WHERE follower_user_id = ?");
$followingStmt->bind_param("i", $followedUserId);
$followingStmt->execute();
$followingCount = (int) $followingStmt->get_result()->fetch_assoc()['total'];
$followingStmt->close();

$conn->close();

respond(200, true, [
    'following' => $following,
    'followers' => $followers,
    'following_count' => $followingCount
]);
?>