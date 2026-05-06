<?php
session_start();

$name = $_SESSION['banned_user_name'] ?? 'User';
$reason = $_SESSION['ban_reason'] ?? 'No reason was provided.';
$banUntil = $_SESSION['ban_until'] ?? 'Permanent';

$isPermanent = strtolower((string)$banUntil) === 'permanent';

$displayUntil = $isPermanent
    ? 'Permanent'
    : date('d/m/Y H:i', strtotime($banUntil));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EcoTracker | Account Banned</title>
    <link rel="stylesheet" href="Banned.css">
</head>

<body class="banned-page">

    <main class="banned-card">
        <img src="Logo.png" alt="EcoTracker Logo" class="logo">

        <h1>Account suspended</h1>

        <p class="intro">
            Hi <?php echo htmlspecialchars($name); ?>, your account is currently banned from EcoTracker.
        </p>

        <div class="ban-info">
            <p>
                <strong>Reason</strong>
                <?php echo htmlspecialchars($reason); ?>
            </p>

            <p>
                <strong>Ban duration</strong>
                <?php echo htmlspecialchars($displayUntil); ?>
            </p>
        </div>

        <p class="note">
            If you believe this was a mistake, contact EcoTracker support or the site administrator.
        </p>

        <a href="logout.php" class="logout-btn">Log out</a>
    </main>

</body>
</html>