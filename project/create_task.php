<?php
session_start();
require 'db.inc.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Project Leader') {
    header('Location: dashboard.php');
    exit;
}

$success = $error = '';
$errors = [];

function generateTaskID($pdo) {
    $sql = "SELECT task_id FROM tasks ORDER BY task_id DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $last_id = $stmt->fetchColumn();

    if (!$last_id) {
        return 'TASK-00001';
    }

    $numeric_part = intval(substr($last_id, 5));
    $new_numeric_part = str_pad($numeric_part + 1, 5, '0', STR_PAD_LEFT);
    return 'TASK-' . $new_numeric_part;
}

$task_id = generateTaskID($pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $project_id = $_POST['project_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $effort = $_POST['effort'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $leader_id = $_SESSION['user_id'];

    if (empty($name) || empty($description) || empty($project_id) || empty($start_date) || empty($end_date) || empty($effort) || empty($priority) || empty($status)) {
        $errors[] = "All fields must be completed.";
    }

    $projectSql = "SELECT start_date, end_date FROM projects WHERE id = :project_id";
    $stmt = $pdo->prepare($projectSql);
    $stmt->execute([':project_id' => $project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($project) {
        if ($start_date < $project['start_date']) {
            $errors[] = "Start Date cannot be earlier than the project's start date.";
        }
        if ($end_date > $project['end_date']) {
            $errors[] = "End Date cannot exceed the project's end date.";
        }
    } else {
        $errors[] = "Selected project does not exist.";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO tasks (task_id, name, description, project_id, leader_id, start_date, end_date, effort, priority, status)
                VALUES (:task_id, :name, :description, :project_id, :leader_id, :start_date, :end_date, :effort, :priority, :status)";
        $stmt = $pdo->prepare($sql);

        try {
            if ($stmt->execute([
                ':task_id' => $task_id,
                ':name' => $name,
                ':description' => $description,
                ':project_id' => $project_id,
                ':leader_id' => $leader_id,
                ':start_date' => $start_date,
                ':end_date' => $end_date,
                ':effort' => $effort,
                ':priority' => $priority,
                ':status' => $status
            ])) {
                $success = "Task '$name' successfully created with ID '$task_id'.";
            } else {
                $error = "Error creating task.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

$sql = "SELECT id, title FROM projects WHERE leader_id = :leader_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':leader_id' => $_SESSION['user_id']]);
$projects = $stmt->fetchAll();

if (empty($projects)) {
    $errors[] = "No projects available for this leader.";
}

include 'header.php';
include 'navigation.php';
?>
<link rel="stylesheet" href="styles.css">
<main class="main">
    <title>Craet task</title>
    <section class="container">
        <h2>Create Task</h2>
        <?php if ($success) echo "<p class='success'>$success</p>"; ?>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <?php if (!empty($errors)): ?>
            <section class="error-message">
                <?php foreach ($errors as $err): ?>
                    <p><?php echo htmlspecialchars($err); ?></p>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="task_id">Task ID</label>
            <input type="text" id="task_id" name="task_id" value="<?php echo $task_id; ?>" readonly>
            <label for="name">Task Name</label>
            <input type="text" id="name" name="name" placeholder="Task Name" required>
            <label for="description">Task Description</label>
            <textarea id="description" name="description" placeholder="Task Description" required></textarea>
            <label for="project_id">Select Project</label>
            <select id="project_id" name="project_id" required>
                <option value="">Select Project</option>
                <?php foreach ($projects as $project): ?>
                    <option value="<?php echo $project['id']; ?>">
                        <?php echo htmlspecialchars($project['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" required>
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" required>
            <label for="effort">Effort (man-months)</label>
            <input type="number" id="effort" name="effort" placeholder="Effort (man-months)" required>
            <label for="priority">Priority</label>
            <select id="priority" name="priority" required>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
            </select>
            <label for="status">Status</label>
            <select id="status" name="status" required>
                <option value="Pending" selected>Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select>
            <button type="submit">Create Task</button>
        </form>
    </section>
</main>
<?php include 'footer.php'; ?>