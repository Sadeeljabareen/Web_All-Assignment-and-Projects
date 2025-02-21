<?php
session_start();
require 'db.inc.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if task_id is provided
if (!isset($_GET['task_id'])) {
    header('Location: task_search.php');
    exit;
}

$task_id = $_GET['task_id'];

// Fetch task details
$sql = "SELECT t.id AS task_id, t.name AS task_name, t.description, p.title AS project_name, t.start_date, t.end_date, t.completion_percentage, t.status, t.priority
        FROM tasks t
        JOIN projects p ON t.project_id = p.id
        WHERE t.id = :task_id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':task_id' => $task_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    $_SESSION['error'] = "Task not found.";
    header('Location: task_search.php');
    exit;
}

// Fetch team members assigned to the task
$sql = "SELECT u.id, u.name, tm.start_date, tm.end_date, tm.contribution_percentage
        FROM team_members tm
        JOIN users u ON tm.user_id = u.id
        WHERE tm.task_id = :task_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':task_id' => $task_id]);
$team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php';
?>

<main>
    <title>Task Details</title>
    <h2>Task Details</h2>
    <div class="task-details-container">
        <!-- Part A: Task Details -->
        <article class="task-details">
            <h3>Task Information</h3>
            <p><strong>Task ID:</strong> <?php echo htmlspecialchars($task['task_id'] ?? ''); ?></p>
            <p><strong>Task Name:</strong> <?php echo htmlspecialchars($task['task_name'] ?? ''); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($task['description'] ?? ''); ?></p>
            <p><strong>Project:</strong> <?php echo htmlspecialchars($task['project_name'] ?? ''); ?></p>
            <p><strong>Start Date:</strong> <?php echo htmlspecialchars($task['start_date'] ?? ''); ?></p>
            <p><strong>End Date:</strong> <?php echo htmlspecialchars($task['end_date'] ?? ''); ?></p>
            <p><strong>Completion Percentage:</strong> <?php echo htmlspecialchars($task['completion_percentage'] ?? ''); ?>%</p>
            <p><strong>Status:</strong> <span class="status-<?php echo strtolower(str_replace(' ', '-', $task['status'] ?? '')); ?>"><?php echo htmlspecialchars($task['status'] ?? ''); ?></span></p>
            <p><strong>Priority:</strong> <span class="priority-<?php echo strtolower($task['priority'] ?? ''); ?>"><?php echo htmlspecialchars($task['priority'] ?? ''); ?></span></p>
        </article>

        <!-- Part B: Team Members Table -->
        <article class="team-members">
            <h3>Team Members</h3>
            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Member ID</th>
                        <th>Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Effort Allocated (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($team_members as $member): ?>
                        <tr>
                            <td><img src="profile.png" alt="Profile" class="profile-image"></td>
                            <td><?php echo htmlspecialchars($member['id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($member['name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($member['start_date'] ?? ''); ?></td>
                            <td><?php echo $member['end_date'] ? htmlspecialchars($member['end_date']) : 'In Progress'; ?></td>
                            <td><?php echo htmlspecialchars($member['contribution_percentage'] ?? ''); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </article>
    </div>
</main>

<?php include 'footer.php'; ?>