<?php
session_start();
include 'Conection.php';
require_once 'admin_guard.php';

require_admin($conn, false);

$search = trim($_GET['search'] ?? '');

$badges = [];
$badgeResult = $conn->query("SELECT id, name, icon, color FROM badges ORDER BY id ASC");

if ($badgeResult) {
    while ($badge = $badgeResult->fetch_assoc()) {
        $badges[] = $badge;
    }
}

if ($search !== '') {
    $like = '%' . $search . '%';

    $stmt = $conn->prepare("
        SELECT 
            id, nome, usuario, email, role,
            is_banned, ban_until, ban_reason,
            COALESCE(NULLIF(avatar_path, ''), 'default-avatar.svg') AS avatar_path
        FROM usuarios
        WHERE nome LIKE ? OR usuario LIKE ? OR email LIKE ? OR role LIKE ?
        ORDER BY id ASC
    ");

    $stmt->bind_param("ssss", $like, $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("
        SELECT 
            id, nome, usuario, email, role,
            is_banned, ban_until, ban_reason,
            COALESCE(NULLIF(avatar_path, ''), 'default-avatar.svg') AS avatar_path
        FROM usuarios
        ORDER BY id ASC
    ");
}

$totalUsers = 0;
$adminUsers = 0;
$bannedUsers = 0;

$countResult = $conn->query("SELECT role, is_banned, COUNT(*) AS total FROM usuarios GROUP BY role, is_banned");

if ($countResult) {
    while ($row = $countResult->fetch_assoc()) {
        $role = strtolower(trim($row['role']));
        $count = (int)$row['total'];

        $totalUsers += $count;

        if ($role === 'admin' || $role === 'administrator') {
            $adminUsers += $count;
        }

        if ((int)$row['is_banned'] === 1) {
            $bannedUsers += $count;
        }
    }
}

function get_user_badges(mysqli $conn, int $userId): array
{
    $items = [];

    $stmt = $conn->prepare("
        SELECT b.id, b.name, b.icon, b.color
        FROM user_badges ub
        INNER JOIN badges b ON b.id = ub.badge_id
        WHERE ub.user_id = ?
        ORDER BY b.id ASC
    ");

    if (!$stmt) return $items;

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $badgeResult = $stmt->get_result();

    while ($badge = $badgeResult->fetch_assoc()) {
        $items[] = $badge;
    }

    $stmt->close();
    return $items;
}

function admin_notice(): string
{
    if (isset($_GET['badge'])) {
        if ($_GET['badge'] === 'added') return 'Badge added.';
        if ($_GET['badge'] === 'removed') return 'Badge removed.';
        return 'Could not update badge.';
    }

    if (isset($_GET['message'])) {
        if ($_GET['message'] === 'sent') return 'Message sent.';
        if ($_GET['message'] === 'invalid') return 'Fill in the title and message.';
        if ($_GET['message'] === 'notfound') return 'User not found.';
        return 'Could not send message.';
    }

    if (isset($_GET['ban'])) {
        if ($_GET['ban'] === 'banned') return 'User banned.';
        if ($_GET['ban'] === 'unbanned') return 'User unbanned.';
        if ($_GET['ban'] === 'self') return 'You cannot ban yourself.';
        if ($_GET['ban'] === 'invalid') return 'Invalid ban request.';
        return 'Could not update ban status.';
    }

    return '';
}

$notice = admin_notice();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EcoTracker | Admin</title>
    <link rel="stylesheet" href="Admin.css">
</head>

<body class="admin-page">

<header class="admin-nav">
    <div class="brand">EcoTracker</div>

    <nav>
        <a class="active" href="admin.php">Admin</a>
        <a href="index.php">Website</a>
        <a href="Community.php">Community</a>
        <a href="Resources.php">Resources</a>
        <a href="Contact.php">Contact</a>
        <a href="logout.php">Log out</a>
    </nav>
</header>

<main class="admin-main">

    <section class="admin-header">
        <div>
            <p class="eyebrow">Admin Panel</p>
            <h1>Users</h1>
            <p>Manage accounts, badges, bans and inbox messages.</p>
        </div>

        <form method="GET" class="search-box">
            <input type="text" name="search" placeholder="Search users..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>
    </section>

    <?php if ($notice !== ''): ?>
        <p class="admin-notice"><?= htmlspecialchars($notice) ?></p>
    <?php endif; ?>

    <section class="stats-row">
        <div>
            <span>Total</span>
            <strong><?= $totalUsers ?></strong>
        </div>

        <div>
            <span>Admins</span>
            <strong><?= $adminUsers ?></strong>
        </div>

        <div>
            <span>Banned</span>
            <strong><?= $bannedUsers ?></strong>
        </div>
    </section>

    <section class="users-list">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($user = $result->fetch_assoc()): ?>
                <?php
                    $listedUserId = (int)$user['id'];
                    $role = strtolower(trim($user['role'] ?? 'user'));
                    $isAdmin = $role === 'admin' || $role === 'administrator';
                    $isBanned = (int)($user['is_banned'] ?? 0) === 1;
                    $userBadges = get_user_badges($conn, $listedUserId);
                ?>

                <article class="user-card">
                    <div class="user-top">
                        <div class="user-left">
                            <img 
                                src="<?= htmlspecialchars($user['avatar_path']) ?>" 
                                alt="Avatar"
                                class="user-avatar"
                                onerror="this.onerror=null;this.src='default-avatar.svg'"
                            >

                            <div>
                                <h2><?= htmlspecialchars($user['nome']) ?></h2>
                                <p>@<?= htmlspecialchars($user['usuario']) ?></p>
                                <small><?= htmlspecialchars($user['email']) ?></small>
                            </div>
                        </div>

                        <div class="user-status">
                            <span class="role-pill <?= $isAdmin ? 'admin' : 'user' ?>">
                                <?= htmlspecialchars($role) ?>
                            </span>

                            <?php if ($isBanned): ?>
                                <span class="ban-pill banned">Banned</span>
                            <?php else: ?>
                                <span class="ban-pill active">Active</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($isBanned): ?>
                        <p class="ban-text">
                            Until: <?= !empty($user['ban_until']) ? htmlspecialchars($user['ban_until']) : 'Permanent' ?><br>
                            Reason: <?= htmlspecialchars($user['ban_reason'] ?? 'No reason given.') ?>
                        </p>
                    <?php endif; ?>

                    <div class="user-sections">
                        <div class="admin-block">
                            <h3>Badges</h3>

                            <div class="badges-list">
                                <?php if (!empty($userBadges)): ?>
                                    <?php foreach ($userBadges as $badge): ?>
                                        <form method="POST" action="admin_badges.php">
                                            <input type="hidden" name="user_id" value="<?= $listedUserId ?>">
                                            <input type="hidden" name="badge_id" value="<?= (int)$badge['id'] ?>">
                                            <input type="hidden" name="action" value="remove">

                                            <button 
                                                type="submit" 
                                                class="badge-pill"
                                                style="background-color: <?= htmlspecialchars($badge['color']) ?>;"
                                                title="Remove badge"
                                            >
                                                <?= htmlspecialchars($badge['icon']) ?>
                                                <?= htmlspecialchars($badge['name']) ?> ×
                                            </button>
                                        </form>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="muted">No badges yet.</p>
                                <?php endif; ?>
                            </div>

                            <form method="POST" action="admin_badges.php" class="small-form inline-form">
                                <input type="hidden" name="user_id" value="<?= $listedUserId ?>">
                                <input type="hidden" name="action" value="add">

                                <select name="badge_id" required>
                                    <option value="">Choose badge</option>
                                    <?php foreach ($badges as $badge): ?>
                                        <option value="<?= (int)$badge['id'] ?>">
                                            <?= htmlspecialchars($badge['icon'] . ' ' . $badge['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <button type="submit">Give</button>
                            </form>
                        </div>

                        <div class="admin-block">
                            <h3>Ban</h3>

                            <?php if ($isBanned): ?>
                                <form method="POST" action="AdminBanUser.php" class="small-form">
                                    <input type="hidden" name="user_id" value="<?= $listedUserId ?>">
                                    <input type="hidden" name="action" value="unban">
                                    <button type="submit" class="dark-btn">Unban user</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="AdminBanUser.php" class="small-form">
                                    <input type="hidden" name="user_id" value="<?= $listedUserId ?>">
                                    <input type="hidden" name="action" value="ban">

                                    <select name="duration" required>
                                        <option value="">Duration</option>
                                        <option value="1">1 day</option>
                                        <option value="3">3 days</option>
                                        <option value="7">7 days</option>
                                        <option value="30">30 days</option>
                                        <option value="permanent">Permanent</option>
                                    </select>

                                    <input type="text" name="reason" placeholder="Reason">

                                    <button type="submit" class="danger-btn">Ban user</button>
                                </form>
                            <?php endif; ?>
                        </div>

                        <div class="admin-block">
                            <h3>Inbox message</h3>

                            <form method="POST" action="AdminSendMessage.php" class="small-form">
                                <input type="hidden" name="user_id" value="<?= $listedUserId ?>">

                                <select name="message_type">
                                    <option value="info">Info</option>
                                    <option value="news">News</option>
                                    <option value="warning">Warning</option>
                                    <option value="ban">Ban Message</option>
                                    <option value="danger">Danger</option>
                                </select>

                                <input type="text" name="title" placeholder="Title" required>
                                <textarea name="message" placeholder="Message..." required></textarea>

                                <button type="submit">Send</button>
                            </form>
                        </div>
                    </div>

                    <div class="user-actions">
                        <a href="EditUser.php?id=<?= urlencode($listedUserId) ?>">Edit</a>
                        <a href="user.php?id=<?= urlencode($listedUserId) ?>">View profile</a>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="empty">No users found.</p>
        <?php endif; ?>
    </section>

</main>

</body>
</html>
<?php $conn->close(); ?>