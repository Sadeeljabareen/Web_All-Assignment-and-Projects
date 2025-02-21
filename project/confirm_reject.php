<?php
session_start();
require 'db.inc.php';

if (!isset($_SESSION['user_id'])) {
    echo "User is not logged in.";
    exit;
}

if ($_SESSION['role'] !== 'Team Member') {
    echo "User is not a Team Member.";
    header('Location: dashboard.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['task_id'])) {
    die("Task ID is missing.");
}

$task_id = $_GET['task_id'];

if (isset($_POST['confirm_reject'])) {
    $sql = "DELETE FROM team_members WHERE task_id = (SELECT id FROM tasks WHERE task_id = :task_id) AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':task_id' => $task_id, ':user_id' => $user_id]);
    header('Location: assignments.php');
    exit;
}

include 'header.php';
include 'navigation.php';
?>
<link rel="stylesheet" href="styles.css">
<title>Confrim or Rejeret</title>
<main>
    <section class="container">
        <h2>Confirm Rejection</h2>
        <h3>Are you sure you want to reject this task?</h3>
        <form method="post">
            <button type="submit" name="confirm_reject" class="reject-button">Yes, Reject Task</button>
            <a href="accept_task.php?task_id=<?php echo $task_id; ?>" class="cancel-button">Cancel</a>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>