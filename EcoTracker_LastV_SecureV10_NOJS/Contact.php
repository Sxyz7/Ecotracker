<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTracker | Contact</title>
    <link rel="stylesheet" href="Contact.css">
</head>

<body class="contact-page">

<?php include 'navbar.php'; ?>

<main class="contact-main">

    <section class="contact-hero">
        <div class="contact-info">
            <p class="eyebrow">Contact</p>
            <h1>Contact us</h1>
            <p class="intro">
                Have a question, suggestion, or collaboration idea? Get in touch with EcoTracker.
            </p>

            <div class="contact-details">
                <p><strong>Email:</strong> EcoAdmin@ecotrackerhub.eu</p>
                <p><strong>Website:</strong> www.ecotrackerhub.eu</p>
                <p><strong>Project:</strong> EcoTracker Hub</p>
            </div>
        </div>

        <div class="contact-image-wrap">
            <img src="Contact.png" alt="EcoTracker contact" class="contact-img">
        </div>
    </section>

    <section class="contact-cards">
        <div class="contact-card">
            <h3>Call us</h3>
            <p>Available soon</p>
            <p>EcoTracker support</p>
        </div>

        <div class="contact-card">
            <h3>Location</h3>
            <p>Leiria, Portugal</p>
            <p>EcoTracker Hub</p>
        </div>

        <div class="contact-card">
            <h3>Hours</h3>
            <p>Monday – Friday</p>
            <p>9:00 – 18:00</p>
        </div>
    </section>

</main>

</body>
</html>