<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<?php include 'header.php'; ?>
<?php include 'navigation.php'; ?>
<link rel="stylesheet" href="styles.css">
<title>Dash Board</title>
<main>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <h3>Your role: <?php echo htmlspecialchars($_SESSION['role']); ?></h3>
</main>

<?php include 'footer.php'; ?>