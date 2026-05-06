<?php
session_start();
include 'Conection.php';

$isLoggedIn = isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
$userId = $isLoggedIn ? (int) $_SESSION['usuario_id'] : 0;

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isLoggedIn) {
        $message = 'You must be logged in to post.';
        $messageType = 'error';
    } else {
        $content = trim($_POST['content'] ?? '');

        if ($content === '') {
            $message = 'Please write something before posting.';
            $messageType = 'error';
        } elseif (mb_strlen($content) > 1000) {
            $message = 'Post is too long. Maximum is 1000 characters.';
            $messageType = 'error';
        } else {
            $authorName = $_SESSION['usuario_username'] ?? $_SESSION['usuario_nome'] ?? 'User';

            $stmt = $conn->prepare("
                INSERT INTO community_comments (user_id, author_name, content)
                VALUES (?, ?, ?)
            ");

            if ($stmt) {
                $stmt->bind_param("iss", $userId, $authorName, $content);
                $stmt->execute();
                $stmt->close();

                header("Location: Community.php?posted=1");
                exit();
            }

            $message = 'Could not post right now.';
            $messageType = 'error';
        }
    }
}

if (isset($_GET['posted'])) {
    $message = 'Post added successfully.';
    $messageType = 'success';
}

$comments = [];

$result = $conn->query("
    SELECT
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
    LIMIT 200
");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTracker | Community</title>
    <link rel="stylesheet" href="Community.css">
</head>

<body class="community-page">

<?php include 'navbar.php'; ?>

<main class="community-main">

    <section class="community-header">
        <p class="community-label">EcoTracker Community</p>
        <h1>Share ideas that help the planet.</h1>
        <p>A simple space to ask questions, share useful eco tips, and suggest improvements for EcoTracker.</p>
    </section>

    <section class="community-card">
        <div class="card-title-row">
            <h2>Write a post</h2>

            <?php if ($isLoggedIn): ?>
                <p>Posting as @<?= htmlspecialchars($_SESSION['usuario_username'] ?? $_SESSION['usuario_nome'] ?? 'user') ?></p>
            <?php else: ?>
                <p>Log in to post in the community.</p>
            <?php endif; ?>
        </div>

        <?php if ($message !== ''): ?>
            <p class="form-feedback <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <?php if ($isLoggedIn): ?>
            <form method="POST" action="Community.php">
                <textarea
                    name="content"
                    maxlength="1000"
                    placeholder="Write your idea, question, or eco tip..."
                    required
                ></textarea>

                <div class="form-row">
                    <span>Maximum 1000 characters</span>
                    <button type="submit" class="post-submit">Post</button>
                </div>
            </form>
        <?php else: ?>
            <p class="empty-message">
                You need to <a href="Login.html">log in</a> before posting.
            </p>
        <?php endif; ?>
    </section>

    <section class="community-card">
        <div class="card-title-row">
            <h2>Latest posts</h2>
            <p>Recent messages from the community.</p>
        </div>

        <div class="comments-list">
            <?php if (empty($comments)): ?>
                <p class="empty-message">No posts yet. Be the first to write something.</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <article class="comment-item <?= (int)$comment['is_pinned'] === 1 ? 'pinned' : '' ?>">
                        <?php if ((int)$comment['is_pinned'] === 1): ?>
                            <div class="pinned-label">Pinned comment</div>
                        <?php endif; ?>

                        <div class="comment-head">
                            <a href="user.php?id=<?= (int)$comment['author_id'] ?>" class="comment-avatar-link">
                                <img
                                    src="<?= htmlspecialchars($comment['author_avatar']) ?>"
                                    alt="Profile photo"
                                    class="comment-avatar"
                                    onerror="this.src='default-avatar.svg'"
                                >
                            </a>

                            <div class="comment-meta">
                                <a href="user.php?id=<?= (int)$comment['author_id'] ?>" class="comment-author">
                                    @<?= htmlspecialchars($comment['author_username']) ?>
                                </a>

                                <time class="comment-date">
                                    <?= htmlspecialchars(date('d/m/Y H:i', strtotime($comment['created_at']))) ?>
                                </time>
                            </div>
                        </div>

                        <p class="comment-text"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

</main>

</body>
</html>
<?php
$conn->close();
?>