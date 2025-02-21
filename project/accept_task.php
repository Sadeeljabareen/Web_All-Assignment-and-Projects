<?php
session_start();
require 'db.inc.php';

if (!isset($_SESSION['user_id'])) {
    echo "User is not logged in.";
    exit;
}

if ($_SESSION['role'] !== 'Team Member') {
    header('Location: dashboard.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch assigned tasks
$sql = "SELECT t.task_id, t.name AS task_name, p.title AS project_name, t.start_date
        FROM tasks t
        JOIN team_members tm ON t.id = tm.task_id
        JOIN projects p ON t.project_id = p.id
        WHERE tm.user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$assigned_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle task acceptance or rejection
if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];
    
    // Fetch task details
    $sql = "SELECT t.task_id, t.name AS task_name, t.description, t.priority, t.status, 
                   t.start_date, t.end_date, tm.role, t.effort 
            FROM tasks t
            JOIN team_members tm ON t.id = tm.task_id 
            WHERE t.task_id = :task_id AND tm.user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':task_id' => $task_id, ':user_id' => $user_id]);
    $task_details = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($task_details) {
        if (isset($_POST['accept'])) {
    // Update task status to a valid ENUM value
    $statusValue = 'In Progress'; // Change this to 'Pending' or 'Completed' as necessary

    $sql = "UPDATE tasks SET status = :status WHERE task_id = :task_id";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([':status' => $statusValue, ':task_id' => $task_id]);
        $success_message = "Task successfully accepted and activated.";
    } catch (PDOException $e) {
        $error_message = "Error updating status: " . $e->getMessage();
    }
}

        if (isset($_POST['reject'])) {
            header('Location: confirm_reject.php?task_id=' . $task_id);
            exit;
        }
    } else {
        $error_message = "Task not found.";
    }
}
?>

<?php include 'header.php'; ?>
<?php include 'navigation.php'; ?>
<link rel="stylesheet" href="styles.css">
<title>Accept Task</title>
<main>
    <h2>Your Assigned Tasks</h2>
    <table>
        <thead>
            <tr>
                <th>Task ID</th>
                <th>Task Name</th>
                <th>Project Name</th>
                <th>Start Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assigned_tasks as $task): ?>
            <tr>
                <td><?php echo htmlspecialchars($task['task_id']); ?></td>
                <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                <td><?php echo htmlspecialchars($task['project_name']); ?></td>
                <td><?php echo htmlspecialchars($task['start_date']); ?></td>
                <td>
                    <a href="accept_task.php?task_id=<?php echo $task['task_id']; ?>">Confirm or Reject</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (isset($task_details)): ?>
        <h3>Task Details</h3>
        <form method="post" class="task-details-form">
            <fieldset>
                <legend>Task Information</legend>
                <label>Task ID:</label>
                <input type="text" value="<?php echo htmlspecialchars($task_details['task_id']); ?>" readonly>
                
                <label>Task Title:</label>
                <input type="text" value="<?php echo htmlspecialchars($task_details['task_name']); ?>" readonly>
                
                <label>Description:</label>
                <textarea readonly><?php echo htmlspecialchars($task_details['description']); ?></textarea>
                
                <label>Priority:</label>
                <input type="text" value="<?php echo htmlspecialchars($task_details['priority']); ?>" readonly>
                
                <label>Status:</label>
                <input type="text" value="<?php echo htmlspecialchars($task_details['status']); ?>" readonly>
                
                <label>Total Effort (Man-Months):</label>
                <input type="text" value="<?php echo htmlspecialchars($task_details['effort']); ?>" readonly>
                
                <label>Role:</label>
                <input type="text" value="<?php echo htmlspecialchars($task_details['role']); ?>" readonly>
                
                <label>Start Date:</label>
                <input type="text" value="<?php echo htmlspecialchars($task_details['start_date']); ?>" readonly>
                
                <label>End Date:</label>
                <input type="text" value="<?php echo htmlspecialchars($task_details['end_date']); ?>" readonly>
            </fieldset>
            <button type="submit" name="accept" class="accept-button">Accept Task</button>
            <button type="submit" name="reject" class="reject-button">Reject Task</button>
        </form>
        
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>