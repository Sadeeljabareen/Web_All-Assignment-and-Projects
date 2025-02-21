<?php
session_start();
require 'db.inc.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id'])) {
    echo "Not logged in. Please log in.";
    exit;
}

if ($_SESSION['role'] !== 'Manager') {
    header('Location: dashboard.php');
    exit;
}

// Fetch unassigned projects
$sql = "SELECT * FROM projects WHERE leader_id IS NULL ORDER BY start_date ASC";
$projects = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$success = $error = '';

// Handle project selection and team leader allocation
if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];

    // Fetch project details
    $sql = "SELECT p.*, u.name AS leader_name FROM projects p LEFT JOIN users u ON p.leader_id = u.id WHERE project_id = :project_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':project_id' => $project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$project) {
        $error = "Project not found.";
    } else {
        // Fetch available team leaders
        $sql = "SELECT id, name FROM users WHERE role = 'Project Leader'";
        $team_leaders = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        // Fetch documents related to the project
        $sql_documents = "SELECT * FROM project_documents WHERE project_id = :project_id";
        $stmt_documents = $pdo->prepare($sql_documents);
        $stmt_documents->execute([':project_id' => $project_id]);
        $documents = $stmt_documents->fetchAll(PDO::FETCH_ASSOC);

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $team_leader_id = $_POST['team_leader_id'] ?? null;

            if (empty($team_leader_id)) {
                $error = "Please select a team leader.";
            } else {
                try {
                    // Update project with the selected team leader
                    $sql = "UPDATE projects SET leader_id = :leader_id WHERE project_id = :project_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':leader_id' => $team_leader_id,
                        ':project_id' => $project_id
                    ]);

                    $success = "Team Leader successfully allocated to Project {$project['project_id']}.";
                } catch (Exception $e) {
                    $error = "Error allocating team leader: " . htmlspecialchars($e->getMessage());
                }
            }
        }
    }
} else {
    $error = "No project selected.";
}

include 'header.php';
include 'navigation.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allocate Team Leader</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<main>
    <section class="container">
        <h2>Allocate Team Leader</h2>
        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <h3>Unassigned Projects</h3>
        <ul>
            <?php foreach ($projects as $proj): ?>
                <li>
                    <a href="?project_id=<?php echo htmlspecialchars($proj['project_id']); ?>">
                        <?php echo htmlspecialchars($proj['title']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if (isset($project)): ?>
            <form class="form-no-margin" method="POST" action="">
                <fieldset>
                    <legend>Project Details</legend>
                    <label for="project_id">Project ID</label>
                    <input type="text" id="project_id" name="project_id" value="<?php echo htmlspecialchars($project['project_id']); ?>" readonly>
                    <label for="project_title">Project Title</label>
                    <input type="text" id="project_title" name="project_title" value="<?php echo htmlspecialchars($project['title']); ?>" readonly>
                    <label for="project_description">Project Description</label>
                    <textarea id="project_description" name="project_description" readonly><?php echo htmlspecialchars($project['description']); ?></textarea>
                    <label for="customer_name">Customer Name</label>
                    <input type="text" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($project['customer_name']); ?>" readonly>
                    <label for="budget">Total Budget</label>
                    <input type="number" id="budget" name="budget" value="<?php echo htmlspecialchars($project['budget']); ?>" readonly>
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($project['start_date']); ?>" readonly>
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($project['end_date']); ?>" readonly>
                    <?php if ($project['leader_name']): ?>
                        <label>Current Team Leader</label>
                        <p><?php echo htmlspecialchars($project['leader_name']); ?></p>
                    <?php endif; ?>
                </fieldset>

                <fieldset>
                    <legend>Select Team Leader</legend>
                    <label for="team_leader_id">Team Leader</label>
                    <select id="team_leader_id" name="team_leader_id" required>
                        <option value="">Select a team leader</option>
                        <?php foreach ($team_leaders as $leader): ?>
                            <option value="<?php echo $leader['id']; ?>"><?php echo htmlspecialchars($leader['name']) . ' - ' . htmlspecialchars($leader['id']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </fieldset>

                <!-- Supporting Documents Section -->
                <fieldset>
                    <legend>Supporting Documents</legend>
                    <ul>
                        <?php if (!empty($documents)): ?>
                            <?php foreach ($documents as $document): ?>
                                <?php
                                $file_extension = pathinfo($document['path'], PATHINFO_EXTENSION);
                                $class = 'document-link ' . $file_extension; // Add document type as a class
                                ?>
                                <li>
                                    <a href="<?php echo htmlspecialchars($document['path']); ?>" class="<?php echo $class; ?>" target="_blank">
                                        <?php echo htmlspecialchars($document['title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No documents available.</li>
                        <?php endif; ?>
                    </ul>
                </fieldset>

                <button type="submit">Confirm Allocation</button>
            </form>
        <?php else: ?>
            <p>No project available for allocation.</p>
        <?php endif; ?>
    </section>
</main>

<?php include 'footer.php'; ?>
</body>
</html>