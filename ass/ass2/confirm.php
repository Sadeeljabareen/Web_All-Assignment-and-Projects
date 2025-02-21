<?php
session_start();
require 'dbconfig.in.php'; // Ensure this file contains your PDO connection setup

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the ticket_id from the query parameter
$ticket_id = $_GET['ticket_id'] ?? 0;

if ($ticket_id == 0) {
    die("Invalid ticket ID.");
}

// Fetch the user's name from the database
$sql_user = "SELECT name FROM users WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->bindValue(':user_id', $user_id);
$stmt_user->execute();
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);
$user_name = $user ? $user['name'] : 'Guest'; // Default to 'Guest' if user name is not found

// Fetch the ticket details for confirmation
$sql = "SELECT * FROM tickets WHERE id = :ticket_id AND customer_id = :customer_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':ticket_id', $ticket_id);
$stmt->bindValue(':customer_id', $user_id);

if ($stmt->execute()) {
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($ticket) {
        // Display ticket details for confirmation
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Confirmation of Maintenance Request</title>
        </head>
        <body>
            <?php include 'header.php'; // Include the header file ?>
            
            <main>
                <section>
                    <h2>Request submitted successfully</h2>
                    <p>Dear <?php echo htmlspecialchars($user_name); ?>, thank you for submitting your maintenance request. Your ticket has been created in the system with reference number # <?php echo htmlspecialchars($ticket['id']); ?>. Here is a summary of the information we have received:</p>
                  <ul>
    <li><strong>Ticket ID:</strong> <?php echo htmlspecialchars($ticket['id']); ?></li>
    <li><strong>Name:</strong> <?php echo htmlspecialchars($ticket['name']); ?></li>
    <li><strong>Email:</strong> <?php echo htmlspecialchars($ticket['email']); ?></li>
    <li><strong>Location:</strong> <?php echo htmlspecialchars($ticket['location']); ?></li>
    <li><strong>Description:</strong> <?php echo htmlspecialchars($ticket['description']); ?></li>
    <li><strong>Emergency Level:</strong> <?php echo htmlspecialchars($ticket['emergency_level']); ?></li>
    <li><strong>Submission Date:</strong> <?php echo htmlspecialchars($ticket['submission_date']); ?></li>
    <li><strong>Status:</strong> <?php echo htmlspecialchars($ticket['status']); ?></li>


<?php if (!empty($ticket['ticket_image'])): ?>
    
        <li><strong>Uploaded Image:</strong> True</li>
        <img src="images/<?php echo htmlspecialchars($ticket['ticket_image']); ?>" alt="Ticket Image" width="300">
    
    <?php else: ?>
        <li><strong>Uploaded Image:</strong> False</li>
        <img src="images/<?php echo htmlspecialchars($ticket['ticket_image']); ?>" alt="Ticket Image" width="300">
    </ul>
<?php endif; ?>

        <p>Our maintenance team will respond to your request shortly</p>
                </section>
            </main>
            
            <hr>
            <footer>
                <section>
                    <address>
                        <p>123 Main St, Ramallah, Palestine</p>
                        <p>Contact Us: Phone: 05677354287 | Email: <a href="Email.html">naturalSolutions@gmail.com</a></p>
                    </address>
                   <p>&copy; 2024 Natural Solutions. All rights reserved | <a href="/index.html">Sadeel Jabareen Home Page</a></p>
                </section>
            </footer>
        </body>
        </html>
        <?php
    } else {
        echo "No ticket found for this user.";
    }
} else {
    die("Error fetching ticket: " . implode(", ", $stmt->errorInfo()));
}
?>
