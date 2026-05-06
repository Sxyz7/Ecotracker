<?php
session_start();
include 'Conection.php';
require_once 'admin_guard.php';

$adminId = require_admin($conn, false);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: admin.php");
    exit();
}

$userId = (int)($_POST['user_id'] ?? 0);
$action = trim($_POST['action'] ?? '');

if ($userId <= 0) {
    header("Location: admin.php?ban=invalid");
    exit();
}

if ($userId === $adminId) {
    header("Location: admin.php?ban=self");
    exit();
}

if ($action === 'unban') {
    $stmt = $conn->prepare("UPDATE usuarios SET is_banned = 0, ban_until = NULL, ban_reason = NULL WHERE id = ?");
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        $stmt->close();

        $title = "Account unbanned";
        $message = "Your EcoTracker account has been unbanned. You can now use the platform again.";
        $type = "info";
        $sender = "admin";

        $msg = $conn->prepare("INSERT INTO inbox_messages (user_id, sender_role, title, message, message_type) VALUES (?, ?, ?, ?, ?)");
        if ($msg) {
            $msg->bind_param("issss", $userId, $sender, $title, $message, $type);
            $msg->execute();
            $msg->close();
        }

        $conn->close();
        header("Location: admin.php?ban=unbanned");
        exit();
    }

    $stmt->close();
    $conn->close();
    header("Location: admin.php?ban=error");
    exit();
}

$duration = trim($_POST['duration'] ?? '');
$reason = trim($_POST['reason'] ?? '');

if ($reason === '') {
    $reason = 'Violation of community rules.';
}

$banUntil = null;

if ($duration === '1') {
    $banUntil = date('Y-m-d H:i:s', strtotime('+1 day'));
} elseif ($duration === '3') {
    $banUntil = date('Y-m-d H:i:s', strtotime('+3 days'));
} elseif ($duration === '7') {
    $banUntil = date('Y-m-d H:i:s', strtotime('+7 days'));
} elseif ($duration === '30') {
    $banUntil = date('Y-m-d H:i:s', strtotime('+30 days'));
} elseif ($duration === 'permanent') {
    $banUntil = null;
} else {
    header("Location: admin.php?ban=invalid");
    exit();
}

$stmt = $conn->prepare("UPDATE usuarios SET is_banned = 1, ban_until = ?, ban_reason = ? WHERE id = ?");
$stmt->bind_param("ssi", $banUntil, $reason, $userId);

if ($stmt->execute()) {
    $stmt->close();

    $title = $duration === 'permanent' ? "Account permanently banned" : "Account temporarily suspended";

    if ($duration === 'permanent') {
        $message = "Your EcoTracker account has been permanently banned.\n\nReason: " . $reason;
    } else {
        $message = "Your EcoTracker account has been temporarily suspended until " . $banUntil . ".\n\nReason: " . $reason;
    }

    $type = "ban";
    $sender = "admin";

    $msg = $conn->prepare("INSERT INTO inbox_messages (user_id, sender_role, title, message, message_type) VALUES (?, ?, ?, ?, ?)");
    if ($msg) {
        $msg->bind_param("issss", $userId, $sender, $title, $message, $type);
        $msg->execute();
        $msg->close();
    }

    $conn->close();
    header("Location: admin.php?ban=banned");
    exit();
}

$stmt->close();
$conn->close();

header("Location: admin.php?ban=error");
exit();
?>