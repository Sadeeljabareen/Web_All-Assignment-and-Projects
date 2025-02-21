<?php
session_start();
include 'header.php';
?>
<link rel="stylesheet" href="styles.css">
<title>registration Sucsses</title>
<main>
    <section class="container">
        <h2>Registration Successful</h2>
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success-message"><?php echo $_SESSION['success']; ?></p>
            <?php unset($_SESSION['success']); ?>
        <?php else: ?>
            <p class="error-message">No success message found. Please try registering again.</p>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>