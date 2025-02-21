<?php
session_start();
require 'dbconfig.in.php'; // Ensure this file contains your PDO connection setup

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

class Ticket {
    private $id;
    private $customerName;
    private $description;
    private $submittedDate;
    private $status;
    private $emergencyLevel;
    private $assignedDate;
    private $assignedStaff;
    private $imageName;

    public function __construct($id, $customerName, $description, $submittedDate, $status, $emergencyLevel, $assignedDate = null, $assignedStaff = null, $imageName = null) {
        $this->id = $id;
        $this->customerName = $customerName;
        $this->description = $description;
        $this->submittedDate = $submittedDate;
        $this->status = $status;
        $this->emergencyLevel = $emergencyLevel;
        $this->assignedDate = $assignedDate;
        $this->assignedStaff = $assignedStaff;
        $this->imageName = $imageName;
    }

    public function displayTable() {
        $assignAction = "";
        $viewAction = "<a href='view.php?id={$this->id}'><img src='view.jpg' alt='View' width='30' height='30'></a>";
        if ($_SESSION['user_type'] === 'staff' || $_SESSION['user_type'] === 'manager') {
            $assignAction = "<a href='assign.php?id={$this->id}'><img src='assign.jpg' alt='Assign' width='30' height='30'></a>";
        }

        return "<tr>
                    <td>#{$this->id}</td>
                    <td>{$this->customerName}</td>
                    <td>{$this->description}</td>
                    <td>{$this->submittedDate}</td>
                    <td>{$this->emergencyLevel}</td>
                    <td>{$this->status}</td>
                    <td>{$assignAction} {$viewAction}</td>
                </tr>";
    }

    public function displayTicketPage() {
        $imagePath = $this->imageName ? "images/{$this->imageName}" : "images/default.jpg";
        return "<main>
                    <h1>Ticket Details</h1>
                    <section class='ticket-details'>
                        <img src='{$imagePath}' alt='Ticket Image' width='200'>
                        <p><strong>ID:</strong> {$this->id}</p>
                        <p><strong>Customer Name:</strong> {$this->customerName}</p>
                        <p><strong>Description:</strong> {$this->description}</p>
                        <p><strong>Submitted Date:</strong> {$this->submittedDate}</p>
                        <p><strong>Status:</strong> {$this->status}</p>
                        <p><strong>Emergency Level:</strong> {$this->emergencyLevel}</p>
                        <p><strong>Assigned Date:</strong> {$this->assignedDate}</p>
                        <p><strong>Assigned Staff:</strong> {$this->assignedStaff}</p>
                    </section>
                </main>";
    }
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

define('DEFAULT_TICKET_IMAGE', 'default.jpg');

// Fetch tickets based on user type
if ($user_type == 'manager') {
    $sql = "SELECT t.*, u.name AS customer_name FROM tickets t JOIN users u ON t.customer_id = u.id WHERE t.status IN ('Pending', 'Assigned')";
} elseif ($user_type == 'customer') {
    $sql = "SELECT t.*, u.name AS customer_name FROM tickets t JOIN users u ON t.customer_id = u.id WHERE t.customer_id = :user_id";
}
$stmt = $pdo->prepare($sql);
if ($user_type == 'customer') {
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}
$stmt->execute();
$ticketsData = $stmt->fetchAll();
$tickets = [];

foreach ($ticketsData as $data) {
    $tickets[] = new Ticket(
        $data['id'],
        $data['customer_name'],
        $data['description'],
        $data['submitted_date'],
        $data['status'],
        $data['emergency_level'],
        $data['assigned_date'] ?? null,
        $data['assigned_staff'] ?? null,
        $data['image_name'] ?? DEFAULT_TICKET_IMAGE
    );
}

// Include the header file
include 'header.php';
?>

<main>
    <section>
        <form method="POST" action="ticketsys.php">
            <fieldset>
                <legend>Advanced Ticket Search</legend>
                <label for="description">Description:</label>
                <input type="text" id="description" name="description" placeholder="Search...">

                <label for="submission-date">Submission Date:</label>
                <input type="date" id="submission-date" name="submission-date">

                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="">Select Status</option>
                    <option value="Assigned">Assigned</option>
                    <option value="Completed">Completed</option>
                    <option value="Pending">Pending</option>
                </select>

                <label for="emergency-level">Emergency Level:</label>
                <select id="emergency-level" name="emergency-level">
                    <option value="">Select Level</option>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>

                <button type="submit" name="search">Filter</button>
            </fieldset>
        </form>

        <article>
            <h1>Ticket List</h1>
            <table border="1">
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Customer Name</th>
                        <th>Issue Description</th>
                        <th>Date Submitted</th>
                        <th>Urgency Level</th>
                        <th>Status</th>
                        <th>Ticket Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if (!empty($tickets)) {
                    foreach ($tickets as $ticket) {
                        echo $ticket->displayTable();
                    }
                } else {
                    echo "<tr><td colspan='7'>No tickets found</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </article>
    </section>
</main>
<hr>
<footer> 
    <address>
        <p>123 Main St, Ramallah, Palestine</p>
        <p>Contact Us: Phone: 05677354287 | Email: <a href="Email.html">naturalSolutions@gmail.com</a></p>
    </address>
    <p>&copy; 2024 Natural Solutions. All rights reserved | <a href="/index.html">Sadeel Jabareen Home Page</a>
</p>
</footer>
