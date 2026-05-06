<?php
session_start();
include 'Conection.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

function respond(int $status, bool $ok, string $message, array $extra = []): void
{
    http_response_code($status);
    echo json_encode(array_merge([
        'ok' => $ok,
        'message' => $message
    ], $extra));
    exit();
}

function give_badge_by_name(mysqli $conn, int $userId, string $badgeName): void
{
    $badgeStmt = $conn->prepare("SELECT id FROM badges WHERE name = ? LIMIT 1");
    if (!$badgeStmt) {
        return;
    }

    $badgeStmt->bind_param("s", $badgeName);
    $badgeStmt->execute();
    $badgeResult = $badgeStmt->get_result();

    if (!$badgeResult || $badgeResult->num_rows === 0) {
        $badgeStmt->close();
        return;
    }

    $badge = $badgeResult->fetch_assoc();
    $badgeId = (int) $badge['id'];
    $badgeStmt->close();

    $insertStmt = $conn->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)");
    if (!$insertStmt) {
        return;
    }

    $insertStmt->bind_param("ii", $userId, $badgeId);
    $insertStmt->execute();
    $insertStmt->close();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, false, 'Method not allowed.');
}

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    respond(401, false, 'You must be logged in to post.');
}

$content = trim($_POST['content'] ?? '');

if ($content === '') {
    respond(422, false, 'Comment cannot be empty.');
}

if (mb_strlen($content) > 1000) {
    respond(422, false, 'Comment is too long. Maximum is 1000 characters.');
}

$userId = (int) $_SESSION['usuario_id'];
$authorName = trim((string) ($_SESSION['usuario_username'] ?? $_SESSION['usuario_nome'] ?? 'User'));

if ($authorName === '') {
    $authorName = 'User';
}

$insertStmt = $conn->prepare("INSERT INTO community_comments (user_id, author_name, content) VALUES (?, ?, ?)");

if (!$insertStmt) {
    respond(500, false, 'Could not prepare the comment insert query.');
}

$insertStmt->bind_param("iss", $userId, $authorName, $content);

if (!$insertStmt->execute()) {
    $insertStmt->close();
    respond(500, false, 'Could not post your comment.');
}

$commentId = (int) $insertStmt->insert_id;
$insertStmt->close();

/* BADGES: FIRST POST + COMMUNITY VOICE */
give_badge_by_name($conn, $userId, 'First Post');

$postCountStmt = $conn->prepare("SELECT COUNT(*) AS total FROM community_comments WHERE user_id = ?");
if ($postCountStmt) {
    $postCountStmt->bind_param("i", $userId);
    $postCountStmt->execute();
    $postCount = (int) $postCountStmt->get_result()->fetch_assoc()['total'];
    $postCountStmt->close();

    if ($postCount >= 10) {
        give_badge_by_name($conn, $userId, 'Community Voice');
    }
}

$readStmt = $conn->prepare("SELECT
        cc.id,
        cc.user_id AS author_id,
        COALESCE(NULLIF(u.usuario, ''), cc.author_name) AS author_username,
        COALESCE(NULLIF(u.avatar_path, ''), 'default-avatar.svg') AS author_avatar,
        cc.content,
        cc.created_at
    FROM community_comments cc
    LEFT JOIN usuarios u ON u.id = cc.user_id
    WHERE cc.id = ?
    LIMIT 1");

if (!$readStmt) {
    respond(200, true, 'Comment posted.', [
        'comment' => [
            'id' => $commentId,
            'author_id' => $userId,
            'author_username' => $authorName,
            'author_avatar' => $_SESSION['usuario_avatar'] ?? 'default-avatar.svg',
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);
}

$readStmt->bind_param("i", $commentId);
$readStmt->execute();
$readResult = $readStmt->get_result();

$comment = $readResult && $readResult->num_rows === 1
    ? $readResult->fetch_assoc()
    : [
        'id' => $commentId,
        'author_id' => $userId,
        'author_username' => $authorName,
        'author_avatar' => $_SESSION['usuario_avatar'] ?? 'default-avatar.svg',
        'content' => $content,
        'created_at' => date('Y-m-d H:i:s')
    ];

$readStmt->close();
$conn->close();

respond(200, true, 'Comment posted.', ['comment' => $comment]);
?>