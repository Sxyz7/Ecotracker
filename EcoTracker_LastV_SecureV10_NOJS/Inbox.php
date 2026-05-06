<?php
session_start();
include 'Conection.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: Login.html");
    exit();
}

$userId = (int) $_SESSION['usuario_id'];

$stmt = $conn->prepare("
    SELECT id, title, message, message_type, is_read, created_at
    FROM inbox_messages
    WHERE user_id = ?
    ORDER BY created_at DESC
");

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$messages = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EcoTracker | Inbox</title>
    <link rel="stylesheet" href="Inbox.css">
</head>

<body class="inbox-page">

<?php include 'navbar.php'; ?>

<main class="inbox-main">
    <section class="inbox-header">
        <p class="eyebrow">EcoTracker Inbox</p>
        <h1>Your messages</h1>
        <p>Important updates and system messages.</p>
    </section>

    <section class="inbox-card">
        <div class="inbox-list">

            <?php if ($messages && $messages->num_rows > 0): ?>
                
                <?php while ($msg = $messages->fetch_assoc()): ?>
                    
                    <?php
                        $type = $msg['message_type'] ?: 'info';
                        $isUnread = (int)$msg['is_read'] === 0;
                    ?>

                    <article class="message-item <?= $isUnread ? 'unread' : '' ?>">

                        <div class="message-top">
                            <span class="message-type <?= htmlspecialchars($type) ?>">
                                <?= htmlspecialchars($type) ?>
                            </span>

                            <time>
                                <?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?>
                            </time>
                        </div>

                        <h2><?= htmlspecialchars($msg['title']) ?></h2>
                        <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>

                        <?php if ($isUnread): ?>
                            <form method="POST" action="MarkMessageRead.php">
                                <input type="hidden" name="message_id" value="<?= (int)$msg['id'] ?>">
                                <button class="mark-read-btn">Mark as read</button>
                            </form>
                        <?php else: ?>
                            <span class="read-label">Read</span>
                        <?php endif; ?>

                    </article>

                <?php endwhile; ?>

            <?php else: ?>
                <p class="empty-message">Your inbox is empty.</p>
            <?php endif; ?>

        </div>
    </section>
</main>

</body>
</html>
