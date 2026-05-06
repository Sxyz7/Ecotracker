<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTracker | About</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="About.css">
</head>

<body>

<header class="navbar navbar-expand-lg eco-navbar">
    <div class="container">
        <a class="navbar-brand logo" href="index.html">EcoTracker</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <nav class="navbar-nav mx-auto">
                <a class="nav-link" href="index.html">Home</a>
                <a class="nav-link" href="Tree.php">Your Tree</a>
                <a class="nav-link" href="Community.html">Community</a>
                <a class="nav-link" href="Resources.html">Resources</a>
                <a class="nav-link" href="Collaborate.html">Collaborate</a>
                <a class="nav-link" href="Contact.html">Contact</a>
                <a class="nav-link" href="Rules.html">Rules</a>
            </nav>

            <div class="auth"></div>
        </div>
    </div>
</header>

<main class="about-page container">

    <section class="hero-section">
        <img src="kristinStorm.jpg" alt="Awareness background">

        <div class="hero-content">
            <p class="eyebrow">Awareness | Climate</p>
            <h1>Leiria, Portugal</h1>
            <p>Honoring Kristin Storm, every action matters.</p>

            <div class="hero-actions">
                <a href="Tree.php" class="btn-main">Take Action</a>
                <a href="Resources.html" class="btn-light-outline">Learn More</a>
            </div>
        </div>
    </section>

    <section class="row g-4 align-items-center section-spacing">
        <div class="col-lg-3">
            <div class="topic-list">
                <span class="active">Climate</span>
                <span>Oceans</span>
                <span>Forests</span>
                <span>Communities</span>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="simple-card row g-4 align-items-center">
                <div class="col-md-4">
                    <img src="polarDanger.jpg" alt="Polar bear awareness" class="rounded-img">
                </div>

                <div class="col-md-8">
                    <h2>Why it matters</h2>
                    <p>
                        As Arctic ice continues to vanish, polar bears struggle to hunt and survive.
                        It is a real reminder that climate change affects wildlife, ecosystems, and people.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="row g-4 section-spacing">
        <div class="col-md-4">
            <article class="info-card">
                <img src="Ocean.jpg" alt="Ocean pollution">
                <div>
                    <h3>Ocean pollution</h3>
                    <p>Plastic and waste harm marine life. Small choices can help keep oceans cleaner.</p>
                </div>
            </article>
        </div>

        <div class="col-md-4">
            <article class="info-card">
                <img src="forest.jpg" alt="Forest devastation">
                <div>
                    <h3>Forest devastation</h3>
                    <p>Deforestation causes habitat loss and makes climate imbalance worse.</p>
                </div>
            </article>
        </div>

        <div class="col-md-4">
            <article class="info-card">
                <img src="en.jpg" alt="Community action">
                <div>
                    <h3>Community strength</h3>
                    <p>Real change becomes easier when people act together and share simple habits.</p>
                </div>
            </article>
        </div>
    </section>

    <section class="cta-box">
        <div>
            <h2>Stay informed</h2>
            <p>Learn more, share awareness, and take small actions that help the planet.</p>
        </div>

        <a href="Resources.html" class="btn-main">Get Updates</a>
    </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="auth_ui.js"></script>
<script>
    if (window.EcoAuth && typeof window.EcoAuth.init === 'function') {
        EcoAuth.init();
    }
</script>

</body>
</html>