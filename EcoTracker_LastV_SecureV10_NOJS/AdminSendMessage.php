<?php
session_start();
include 'Conection.php';
require_once 'admin_guard.php';

require_admin($conn, false);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin.php");
    exit();
}

$userId = (int) ($_POST['user_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$message = trim($_POST['message'] ?? '');
$messageType = trim($_POST['message_type'] ?? 'info');

$allowedTypes = ['info', 'warning', 'danger'];

if ($userId <= 0 || $title === '' || $message === '') {
    header("Location: admin.php?message=invalid");
    exit();
}

if (!in_array($messageType, $allowedTypes, true)) {
    $messageType = 'info';
}

$userCheck = $conn->prepare("SELECT id FROM usuarios WHERE id = ? LIMIT 1");
$userCheck->bind_param("i", $userId);
$userCheck->execute();
$userExists = $userCheck->get_result()->num_rows > 0;
$userCheck->close();

if (!$userExists) {
    header("Location: admin.php?message=notfound");
    exit();
}

$senderRole = 'admin';

$stmt = $conn->prepare("
    INSERT INTO inbox_messages (user_id, sender_role, title, message, message_type)
    VALUES (?, ?, ?, ?, ?)
");

if (!$stmt) {
    header("Location: admin.php?message=error");
    exit();
}

$stmt->bind_param("issss", $userId, $senderRole, $title, $message, $messageType);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: admin.php?message=sent");
    exit();
}

$stmt->close();
$conn->close();

header("Location: admin.php?message=error");
exit();
?>