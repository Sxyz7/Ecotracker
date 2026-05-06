<?php
include 'Conection.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$sql = "SELECT
            cc.id,
            cc.user_id AS author_id,
            COALESCE(NULLIF(u.usuario, ''), cc.author_name) AS author_username,
            COALESCE(NULLIF(u.avatar_path, ''), 'default-avatar.svg') AS author_avatar,
            cc.content,
            cc.is_pinned,
            cc.created_at
        FROM community_comments cc
        LEFT JOIN usuarios u ON u.id = cc.user_id
        WHERE cc.is_visible = 1
        ORDER BY cc.is_pinned DESC, cc.created_at DESC
        LIMIT 200";

$result = $conn->query($sql);

if ($result === false) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'Could not load community comments.'
    ]);
    $conn->close();
    exit();
}

$comments = [];

while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

$conn->close();

echo json_encode([
    'ok' => true,
    'comments' => $comments
]);
?>