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
$badgeId = (int) ($_POST['badge_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($userId <= 0 || $badgeId <= 0) {
    header("Location: admin.php?badge=invalid");
    exit();
}

if ($action === 'add') {
    $stmt = $conn->prepare("INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $badgeId);
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php?badge=added");
    exit();
}

if ($action === 'remove') {
    $stmt = $conn->prepare("DELETE FROM user_badges WHERE user_id = ? AND badge_id = ?");
    $stmt->bind_param("ii", $userId, $badgeId);
    $stmt->execute();
    $stmt->close();

    header("Location: admin.php?badge=removed");
    exit();
}

header("Location: admin.php?badge=invalid");
exit();
?>