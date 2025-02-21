<?php
session_start();
include 'header.php';
?>
<link rel="stylesheet" href="styles.css">
<title>Task Allocator Pro</title>
<main class="landing">
    <section class="container">
        <h2>Welcome Again</h2>
        <p>Click Log in if you have an account and Sign up if you do not:</p>
        <form class="form-no-margin" action="login.php" method="get">
            <button type="submit" class="fancy-button">Log in</button>
        </form>
        <form class="form-no-margin" action="registration_step1.php" method="get">
            <button type="submit" class="fancy-button">Sign up</button>
        </form>
    </section>
</main>
<?php include 'footer.php'; ?>