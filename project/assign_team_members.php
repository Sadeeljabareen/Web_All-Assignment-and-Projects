<?php
session_start();
require 'db.inc.php';

// Check if the user is a Project Leader
if ($_SESSION['role'] !== 'Project Leader') {
    header('Location: dashboard.php');
    exit;
}

$success = $error = '';

// Handle form submission for assigning team members
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['finish_allocation'])) {
        // Handle Finish Allocation button click
        $task_id = $_POST['task_id'];
        $sql = "DELETE FROM tasks WHERE task_id = :task_id";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([':task_id' => $task_id]);
            $success = "Task $task_id has been successfully finished.";
        } catch (Exception $e) {
            $error = "Error removing task: " . htmlspecialchars($e->getMessage());
        }
    } elseif (isset($_POST['assign_member'])) {
        // Handle Assign Member button click
        $task_id = $_POST['task_id'];
        $user_id = $_POST['user_id'] ?? null;
        $role = $_POST['role'] ?? null;
        $contribution = $_POST['contribution'] ?? null;

        // Validate required fields
        if (empty($user_id) || empty($role) || empty($contribution)) {
            $error = "All fields are required.";
        } else {
            // Fetch the task's start date for validation
            $sql = "SELECT start_date FROM tasks WHERE task_id = :task_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':task_id' => $task_id]);
            $task = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$task) {
                $error = "Task not found.";
            } else {
                // Validate contribution percentage
                $sql = "SELECT SUM(contribution_percentage) AS total_contribution FROM team_members WHERE task_id = (SELECT id FROM tasks WHERE task_id = :task_id)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':task_id' => $task_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $totalContribution = $result['total_contribution'] ?? 0;

                if (($totalContribution + $contribution) > 100) {
                    $error = "Total contribution cannot exceed 100%. Current total: $totalContribution%, attempted to add: $contribution%";
                } else {
                    // Insert the team member into the database
                    $sql = "INSERT INTO team_members (task_id, user_id, role, contribution_percentage)
                            VALUES ((SELECT id FROM tasks WHERE task_id = :task_id), :user_id, :role, :contribution)";
                    $stmt = $pdo->prepare($sql);

                    try {
                        $stmt->execute([
                            ':task_id' => $task_id,
                            ':user_id' => $user_id,
                            ':role' => $role,
                            ':contribution' => $contribution
                        ]);
                        $success = "Team member successfully assigned to Task $task_id as $role.";
                    } catch (Exception $e) {
                        $error = "Error assigning team member: " . htmlspecialchars($e->getMessage());
                    }
                }
            }
        }
    }
}

// Fetch available team members
$sql = "SELECT id, name FROM users WHERE role = 'Team Member'";
$team_members = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Fetch tasks, sorted by allocation status
$sql = "SELECT id, task_id, name, start_date, status, priority 
        FROM tasks ORDER BY 
        CASE WHEN id IN (SELECT task_id FROM team_members) THEN 1 ELSE 0 END, id";
$tasks = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
include 'navigation.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Team Members to Tasks</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<main>
    <h2>Assign Team Members to Tasks</h2>
    <?php if ($success): ?>
        <h3 class="success-message"><?php echo $success; ?></h3>
        <h3>
    <form method="POST" action="">
        <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($_POST['task_id']); ?>">
        <button type="submit" class="add-another" name="add_another">Add Another Team Member</button>
        <button type="submit" class="finish-allocation" name="finish_allocation">Finish Allocation</button>
    </form>
</h3>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="error-message"><?php echo $error; ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Task ID</th>
                <th>Task Name</th>
                <th>Start Date</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?php echo htmlspecialchars($task['task_id']); ?></td>
                <td><?php echo htmlspecialchars($task['name']); ?></td>
                <td><?php echo htmlspecialchars($task['start_date']); ?></td>
                <td><?php echo htmlspecialchars($task['status']); ?></td>
                <td><?php echo htmlspecialchars($task['priority']); ?></td>
                <td>
                    <form method="POST" action="" class="action-form">
                        <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['task_id']); ?>">
                        <select name="user_id" required>
                            <option value="">Select Team Member</option>
                            <?php foreach ($team_members as $member): ?>
                                <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="role" required>
                            <option value="">Select Role</option>
                            <option value="Developer">Developer</option>
                            <option value="Designer">Designer</option>
                            <option value="Tester">Tester</option>
                            <option value="Analyst">Analyst</option>
                            <option value="Support">Support</option>
                        </select>
                        <input type="number" name="contribution" placeholder="Contribution (%)" required min="0" max="100">
                        <button type="submit" name="assign_member">Assign Member</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php include 'footer.php'; ?>