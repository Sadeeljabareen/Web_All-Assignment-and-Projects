<?php
session_start();
require 'dbconfig.in.php'; // Ensure this file contains your PDO connection setup

// Redirect to login if user is not authenticated or not a manager
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'manager') {
    header("Location: login.php");
    exit();
}

$ticket_id = $_GET['id'];

// Retrieve ticket details
$sql = "SELECT * FROM tickets WHERE id = :ticket_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':ticket_id', $ticket_id);
$stmt->execute();
$ticket = $stmt->fetch();

if (!$ticket) {
    echo "Ticket not found.";
    exit();
}

// Retrieve staff list
$sql = "SELECT * FROM staff";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$staff_list = $stmt->fetchAll();

if (empty($staff_list)) {
    echo "No staff members found.";
    exit(); // Exit to avoid further processing
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = $_POST['staff_id'];

    // Check ticket status before updating
    if ($ticket['status'] === 'Pending') {
        $sql = "UPDATE tickets SET assigned_staff_id = :staff_id, status = 'Assigned' WHERE id = :ticket_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':staff_id', $staff_id);
        $stmt->bindParam(':ticket_id', $ticket_id);
        $stmt->execute();
        
        header("Location: ticketsys.php"); // Redirect after update
        exit();
    } else {
        echo "This ticket cannot be assigned because it is already assigned or completed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Ticket</title>
</head>
<body>

    <?php include 'header.php'; ?> 

    <main>
        <section>
            <h2>Assign Ticket #<?php echo htmlspecialchars($ticket_id); ?></h2>
            
            <article>
                <p><strong>Issue Description:</strong> <?php echo htmlspecialchars($ticket['description']); ?></p>
                <p><strong>Urgency Level:</strong> <?php echo htmlspecialchars($ticket['emergency_level']); ?></p>
                <p><strong>Date Submitted:</strong> <?php echo htmlspecialchars($ticket['submitted_date']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($ticket['status']); ?></p>
            </article>

            <form method="POST">
                <label for="staff_id">Assign to Staff Member:</label>
                <select name="staff_id" required>
                    <option value="" disabled selected>Choose Staff</option>
                    <?php foreach ($staff_list as $staff) { 
                        // Check if 'name' key exists
                        if (isset($staff['user_id'])) { // Assuming user_id is linked to get the name
                            $user_sql = "SELECT name FROM users WHERE id = :user_id";
                            $user_stmt = $pdo->prepare($user_sql);
                            $user_stmt->bindParam(':user_id', $staff['user_id']);
                            $user_stmt->execute();
                            $user = $user_stmt->fetch();
                            $staff_name = $user ? htmlspecialchars($user['name']) : 'Unknown Staff';
                    ?>
                        <option value="<?php echo htmlspecialchars($staff['id']); ?>"><?php echo $staff_name; ?></option>
                    <?php 
                        } 
                    } ?>
                </select>
                <br><br>
                <button type="submit">Assign Ticket</button>
            </form>
        </section>
    </main>

    <hr>
    <footer>
        <address>
            <p>123 Main St, Ramallah, Palestine</p>
            <p>Contact Us: Phone: 05677354287 | Email: <a href="Email.html">naturalSolutions@gmail.com</a></p>
        </address>
        <p>&copy; 2024 Natural Solutions. All rights reserved | <a href="PrivacyPolicy.html">Privacy Policy</a></p>
    </footer>
</body>
</html>