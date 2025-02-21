<?php
session_start();
require 'dbconfig.in.php'; // Ensure this file contains your PDO connection setup

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? ''; // Default to empty if not set

// Check if necessary columns exist in the tickets table
$columns_to_check = ['name', 'submission_date', 'customer_id'];
foreach ($columns_to_check as $column) {
    $sql_check_column = "SHOW COLUMNS FROM tickets LIKE '$column'";
    $check_stmt = $pdo->prepare($sql_check_column);
    $check_stmt->execute();
    $column_exists = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$column_exists) {
        die("Error: The '$column' column does not exist in the 'tickets' table. Please add the column to the database.");
    }
}

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Use the name entered by the user
    $name = $_POST['name'] ?? $user_name; // Get the name from the form or session
    $email = $_POST['email'] ?? ''; // Get the email from the form
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $emergency_level = $_POST['urgency'] ?? '';
    $ticket_image = $_FILES['photo'] ?? null;

    // Check for valid image type (only JPEG)
    if ($ticket_image && $ticket_image['error'] == UPLOAD_ERR_OK) {
        $imageFileType = strtolower(pathinfo($ticket_image['name'], PATHINFO_EXTENSION));
        if ($imageFileType !== 'jpeg') {
            die("Only JPEG files are allowed.");
        }
    }

    // Insert the ticket into the database
    $sql = "INSERT INTO tickets (customer_id, name, email, location, description, emergency_level, ticket_image, submission_date, status) 
            VALUES (:customer_id, :name, :email, :location, :description, :emergency_level, '', NOW(), 'Pending')";
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters using bindValue
    $stmt->bindValue(':customer_id', $user_id);
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':location', $location);
    $stmt->bindValue(':description', $description);
    $stmt->bindValue(':emergency_level', $emergency_level);
    
    // Execute the statement and check for errors
    if ($stmt->execute()) {
        // Get the ticket ID
        $ticket_id = $pdo->lastInsertId();
        $_SESSION['ticket_id'] = $ticket_id; // Store ticket ID in session

        // Handle image upload
        if ($ticket_image && $ticket_image['error'] == UPLOAD_ERR_OK) {
            // Define the target directory
            $target_dir = "images/";
            $target_file = $target_dir . $ticket_id . '.jpeg';

            // Check if the target directory exists, if not, create it
            if (!file_exists($target_dir)) {
                if (!mkdir($target_dir, 0777, true)) {
                    die("Failed to create the directory: $target_dir");
                }
            }

            // Move the uploaded file to the target directory
            if (move_uploaded_file($ticket_image['tmp_name'], $target_file)) {
                // Update the ticket image in the database
                $update_sql = "UPDATE tickets SET ticket_image = :ticket_image WHERE id = :ticket_id";
                $update_stmt = $pdo->prepare($update_sql);
                $update_stmt->bindValue(':ticket_image', $ticket_id . '.jpeg');
                $update_stmt->bindValue(':ticket_id', $ticket_id);
                
                if (!$update_stmt->execute()) {
                    die("Error updating ticket image: " . implode(", ", $update_stmt->errorInfo()));
                }
            } else {
                die("There was an error uploading your file.");
            }
        }
        
        // Redirect to the confirmation page
        header("Location: confirm.php?ticket_id=" . $ticket_id);
        exit();
    } else {
        die("Error inserting ticket: " . implode(", ", $stmt->errorInfo()));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Maintenance Request</title>
</head>
<body>
    <?php include 'header.php'; // Include the header file ?>
    
    <main>
        <section>
            <h2>Submit a Maintenance Request</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_name); ?>" required><br><br>

                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required><br><br>

                <label for="location">Location/Room Number:</label>
                <input type="text" id="location" name="location" placeholder="Enter your Location or Room Number" required><br><br>

                <label for="description">Issue Description:</label><br>
                <textarea id="description" name="description" placeholder="Describe the issue..." required></textarea><br><br>

                <label for="urgency">Urgency Level:</label>
                <select id="urgency" name="urgency" required>
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select><br><br>

                <label for="photo">Upload a Photo of the Issue (optional):</label>
                <input type="file" id="photo" name="photo" accept="image/jpeg"><br><br>
                
                <button type="submit">Submit Request</button>
            </form>
        </section>
    </main>
    
    <hr>
    <footer> 
        <section>
            <address>
                <p>123 Main St, Ramallah, Palestine</p>
                <p>Contact Us: Phone: 05677354287 | Email: <a href="Email.html">naturalSolutions@gmail.com</a></p>
            </address>
           <p>&copy; 2024 Natural Solutions. All rights reserved | <a href="/index.html">Sadeel Jabareen Home Page</a>
</p>
        </section>
    </footer>
</body>
</html>