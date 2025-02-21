<?php
session_start();
require 'db.inc.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['step2'])) {
    $_SESSION['username'] = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);

    if (strlen($_SESSION['username']) < 6 || strlen($_SESSION['username']) > 13) {
        $error = "Username must be between 6 and 13 characters.";
    } elseif (strlen($password) < 8 || strlen($password) > 12) {
        $error = "Password must be between 8 and 12 characters.";
    } elseif ($password !== $password_confirm) {
        $error = "Passwords do not match.";
    } else {
        $_SESSION['password'] = password_hash($password, PASSWORD_BCRYPT);
        header('Location: registration_step3.php');
        exit;
    }
}

include 'header.php';
?>
<link rel="stylesheet" href="styles.css">
<title>registration Step Two</title>
<main>
    <section class="container">
        <h2>User Registration - Step 2</h2>
        <?php if (!empty($error)): ?>
            <p class='error-message'><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="step2" value="1">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="password_confirm">Password Confirmation:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
            <button type="submit">Proceed to Confirmation</button>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>