<?php
session_start();
include 'Conection.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

function respond(int $status, bool $ok, string $message = '', array $extra = []): void
{
    http_response_code($status);
    echo json_encode(array_merge([
        'ok' => $ok,
        'message' => $message
    ], $extra));
    exit();
}

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    respond(401, false, 'You need to log in first.');
}

$userId = (int) $_SESSION['usuario_id'];

$stmt = $conn->prepare("
    SELECT id, sender_role, title, message, message_type, is_read, created_at
    FROM inbox_messages
    WHERE user_id = ?
    ORDER BY is_read ASC, created_at DESC
    LIMIT 100
");

if (!$stmt) {
    respond(500, false, 'Could not prepare inbox query.');
}

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$stmt->close();
$conn->close();

respond(200, true, '', ['messages' => $messages]);
?>