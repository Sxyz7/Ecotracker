<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTracker | Collaborate</title>
    <link rel="stylesheet" href="Collaborate.css">
</head>

<body class="collaborate-page">

<?php include 'navbar.php'; ?>

<main class="collaborate-main">

    <section class="hero-card">
        <div>
            <p class="eyebrow">Collaborate with EcoTracker</p>
            <h1>Small actions grow stronger when people work together.</h1>
            <p>
                EcoTracker is about building a community where students, schools, organizations,
                and everyday people can help create a greener future.
            </p>
            <a href="Contact.php" class="btn-main">Start Collaborating</a>
        </div>

        <img src="collaborateimg.png" alt="People collaborating for the environment" class="hero-img">
    </section>

    <section class="intro-section">
        <h2>How you can help</h2>
        <p>
            Collaboration does not need to be complicated. Sharing an idea, joining a challenge,
            supporting a campaign, or helping improve the platform can all make a difference.
        </p>
    </section>

    <section class="cards-grid section-spacing">
        <article class="simple-card">
            <h3>Individuals</h3>
            <p>Complete sustainable actions, grow your tree, and inspire others through your progress.</p>
        </article>

        <article class="simple-card">
            <h3>Schools</h3>
            <p>Support school projects, awareness campaigns, and student challenges focused on sustainability.</p>
        </article>

        <article class="simple-card">
            <h3>Communities</h3>
            <p>Share ideas, promote cleanups, and encourage more people to take practical actions.</p>
        </article>

        <article class="simple-card">
            <h3>Organizations</h3>
            <p>Future partnerships can connect digital progress with real-world impact, such as tree planting.</p>
        </article>
    </section>

    <section class="section-spacing">
        <h2 class="section-title">How collaboration helps EcoTracker</h2>

        <div class="info-block">
            <h3>Better ideas</h3>
            <p>
                Suggestions from users help improve EcoTracker’s features, design, and usability.
                Every piece of feedback can make the platform clearer and more useful.
            </p>
        </div>

        <div class="info-block">
            <h3>More awareness</h3>
            <p>
                Sharing EcoTracker with classmates, friends, schools, or local groups helps spread
                environmental awareness and encourages more people to take action.
            </p>
        </div>

        <div class="info-block">
            <h3>Future partnerships</h3>
            <p>
                In the future, EcoTracker aims to connect digital tree growth with real environmental
                initiatives such as tree planting, campaigns, or sustainability projects.
            </p>
        </div>
    </section>

    <section class="github-box">
        <p class="eyebrow">Open Project</p>
        <h2>EcoTracker on GitHub</h2>
        <p>
            The EcoTracker repository is available on GitHub, where the project can be shared,
            improved, documented, and reviewed.
        </p>
        <a href="https://github.com/Sxyz7/Ecotracker" class="btn-main" target="_blank">View GitHub Repository</a>
    </section>

    <section class="cta-box">
        <div>
            <h2>Want to collaborate with EcoTracker?</h2>
            <p>
                Whether you have an idea, a project, or simply want to support the mission,
                we would love to hear from you.
            </p>
        </div>

        <div class="cta-actions">
            <a href="Contact.php" class="btn-main">Contact Us</a>
            <a href="Community.php" class="btn-outline-main">Join Community</a>
        </div>
    </section>

</main>

</body>
</html>