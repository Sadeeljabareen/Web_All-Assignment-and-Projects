<?php
session_start();
require 'dbconfig.in.php'; // Ensure this file contains your PDO connection setup

// Fetch user ID from session
$user_id = $_SESSION['user_id'] ?? null;
$user_name = 'Guest';

// Fetch the user's name from the database if user ID is set
if ($user_id) {
    try {
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
}

// Check if ticket ID is provided
if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];

    // Validate the ticket ID
    if (is_numeric($ticket_id)) {
        // Fetch ticket details from the database
        $sql = "SELECT t.*, u.name AS customer_name, u.email AS customer_email 
                FROM tickets t 
                JOIN users u ON t.customer_id = u.id 
                WHERE t.id = :ticket_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
        $stmt->execute();
        $ticket = $stmt->fetch();

        if ($ticket) {
            // Prepare ticket information
            $ticket_number = htmlspecialchars($ticket['id']);
            $customer_name = isset($ticket['customer_name']) ? htmlspecialchars($ticket['customer_name']) : 'Not provided';
            $email = htmlspecialchars($ticket['customer_email'] ?? 'Not provided');
            $location = htmlspecialchars($ticket['location'] ?? 'Not provided');
            $description = htmlspecialchars($ticket['description'] ?? 'Not provided');
            $emergency_level = htmlspecialchars($ticket['emergency_level'] ?? 'Not provided');
            $photo_uploaded = isset($ticket['photo_uploaded']) && $ticket['photo_uploaded'] ? 'Yes' : 'No';
            $assigned_to = htmlspecialchars($ticket['assigned_to'] ?? 'Not assigned');
            $submitted_date = htmlspecialchars($ticket['submitted_date'] ?? 'Not provided');
        } else {
            $ticket_number = 'N/A';
            $customer_name = 'N/A';
            $email = 'N/A';
        }
    } else {
        $ticket_number = 'Invalid Ticket ID';
        $customer_name = 'N/A';
        $email = 'N/A';
    }
} else {
    $ticket_number = 'Ticket ID not provided';
    $customer_name = 'N/A';
    $email = 'N/A';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket</title>
</head>
<body>

    <?php include 'header.php'; ?> 

    <main>
        <section>
            <h2>View Ticket #<?php echo $ticket_number; ?></h2>
            <p>Here is a summary of the information we have received about ticket #<?php echo $ticket_number; ?>:</p>

            <article>
                <h3>Request Details</h3> 
                <ul>
                    <li><strong>Full Name:</strong> <?php echo $customer_name; ?></li>
                    <li><strong>Email Address:</strong> <?php echo $email; ?></li>
                    <li><strong>Location/Room Number:</strong> <?php echo $location; ?></li>
                    <li><strong>Issue Description:</strong> <?php echo $description; ?></li>
                    <li><strong>Urgency Level:</strong> <?php echo $emergency_level; ?></li>
                    <li><strong>Assigned To:</strong> <?php echo $assigned_to; ?></li>
                    <li><strong>Submitted Date:</strong> <?php echo $submitted_date; ?></li>
                    <li><strong>Photo Uploaded:</strong> <?php echo $photo_uploaded; ?></li>
                </ul>
                <?php if (isset($ticket['photo_path']) && !empty($ticket['photo_path'])): ?>
                    <h4>Uploaded Photo:</h4>
                    <img src="<?php echo htmlspecialchars($ticket['photo_path']); ?>" alt="Ticket Photo" style="max-width: 100%; height: auto;">
                <?php endif; ?>
            </article>
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
