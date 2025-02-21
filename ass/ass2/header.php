<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbconfig.in.php'; // Ensure this file contains your PDO connection setup

$user_id = $_SESSION['user_id'] ?? null;
$user_type = $_SESSION['user_type'] ?? 'guest';

if ($user_id) {
    try {
        // Fetch the user's name from the database
        $sql = "SELECT name FROM users WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            $user_name = htmlspecialchars($user['name']);
        } else {
            $user_name = 'Unknown User';
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $user_name = 'Unknown User';
    }
} else {
    $user_name = 'Guest';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
</head>
<header>
    <figure>
        <img src="Natural_Solutions.png" alt="Natural Solutions Logo" width="100" height="100">
    </figure>
    <h1>Maintenance Request System</h1>
</header>
<body>
    <nav>
        <ul>
            <li><a href="login.php">Home</a></li>
            <?php if ($user_type === 'customer'): ?>
                <li><a href="request.php">Submit a Maintenance Request</a></li>
            <?php elseif ($user_type === 'manager'): ?>
                <li><a href="ticketsys.php">View Tickets</a></li>
            <?php endif; ?>
        </ul> 
    </nav>

    <p>Welcome, <?php echo $user_name; ?>!</p>
    <hr>
</body>
</html>