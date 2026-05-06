<?php
session_start();
include 'Conection.php';
require_once 'admin_guard.php';

header('Content-Type: application/json');

function respond(int $status, bool $ok, string $message): void
{
    http_response_code($status);
    echo json_encode([
        'ok' => $ok,
        'message' => $message
    ]);
    exit();
}

require_admin($conn, true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, false, 'Method not allowed.');
}

$commentId = (int) ($_POST['comment_id'] ?? 0);

if ($commentId <= 0) {
    respond(400, false, 'Invalid comment.');
}

$stmt = $conn->prepare("DELETE FROM community_comments WHERE id = ?");
if (!$stmt) {
    respond(500, false, 'Could not prepare delete query.');
}

$stmt->bind_param("i", $commentId);

if (!$stmt->execute()) {
    $stmt->close();
    respond(500, false, 'Could not delete comment.');
}

$stmt->close();
$conn->close();

respond(200, true, 'Comment deleted.');
?>