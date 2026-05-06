<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTracker</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>

<?php include 'navbar.php'; ?>

<?php if (isset($_SESSION['usuario_nome'])): ?>
    <section class="welcome-back">
        Welcome back, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!
    </section>
<?php endif; ?>

<section class="hero">
    <div class="container">
        <h1>Welcome to</h1>
        <h2>EcoTracker</h2>

        <p class="hero-text">
            Grow your tree, track good actions, and connect with people making a positive impact.
        </p>

        <div class="hero-buttons">
            <a class="btn-main" href="About.php">More about</a>
            <a class="btn-dark-main" href="Resources.php">Environment</a>
        </div>
    </div>
</section>

<section class="video-section">
    <div class="container video-grid">
        <div>
            <video autoplay muted loop playsinline>
                <source src="intro.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

        <div class="video-text">
            <h3>Why Choose EcoTracker?</h3>
            <p>
                Every good action you take helps your tree grow. EcoTracker makes your positive impact visible,
                turning everyday choices like recycling, volunteering, or helping others into a thriving digital forest.
            </p>
        </div>
    </div>
</section>

<section class="logo-section">
    <div class="container">
        <img src="Logo.png" alt="EcoTracker logo" class="center-logo">

        <p class="subtitle">
            “Be Positive”<br>
            <span>Help the Environment</span>
        </p>
    </div>
</section>

</body>
</html>