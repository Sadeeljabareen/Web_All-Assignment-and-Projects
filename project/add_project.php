<?php
session_start();
require 'db.inc.php';

if ($_SESSION['role'] !== 'Manager') {
    header('Location: dashboard.php');
    exit;
}

$success = $error = '';
$allowed_file_types = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/png', 'image/jpeg'];
$max_file_size = 2 * 1024 * 1024;

$sql = "SELECT * FROM projects WHERE leader_id IS NULL ORDER BY start_date ASC";
$projects = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

function generateProjectID($pdo) {
    // استخراج آخر معرف مشروع
    $sql = "SELECT project_id FROM projects ORDER BY project_id DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $last_id = $stmt->fetchColumn();

    // إذا لم يكن هناك أي معرفات، نبدأ بـ "PROJ-00001"
    if (!$last_id) {
        return 'PROJ-00001';
    }

    // فصل الجزء الأبجدي والجزء الرقمي
    $parts = explode('-', $last_id);
    $alpha_part = $parts[0]; // الجزء الأبجدي (الأحرف)
    $numeric_part = intval($parts[1]); // الجزء الرقمي

    // زيادة الجزء الرقمي بمقدار 1
    $new_numeric_part = $numeric_part + 1;

    // إذا وصل الجزء الرقمي إلى 99999، نعيده إلى 00001
    if ($new_numeric_part > 99999) {
        $new_numeric_part = 1; // إعادة الجزء الرقمي إلى 00001
    }

    // التأكد من أن الجزء الرقمي مكون من 5 أرقام مع تعبئة الأصفار
    $new_numeric_part = str_pad($new_numeric_part, 5, '0', STR_PAD_LEFT);

    // إنشاء معرف المشروع الجديد
    return 'PROJ-' . $new_numeric_part;
}


$new_project_id = generateProjectID($pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $customer_name = $_POST['customer_name'];
    $budget = $_POST['budget'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (strtotime($end_date) <= strtotime($start_date)) {
        $error = "End Date must be later than Start Date.";
    } elseif ($budget <= 0) {
        $error = "Budget must be a positive numeric value.";
    } else {
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }

        $documents = [];
        for ($i = 1; $i <= 3; $i++) {
            if (isset($_FILES["document_$i"]) && $_FILES["document_$i"]['error'] == UPLOAD_ERR_OK) {
                $doc_title = $_POST["document_title_$i"];
                $file = $_FILES["document_$i"];
                if (!in_array($file['type'], $allowed_file_types)) {
                    $error = "Invalid file type for document $i.";
                    break;
                }
                if ($file['size'] > $max_file_size) {
                    $error = "File size for document $i exceeds the maximum limit of 2MB.";
                    break;
                }
                $file_path = 'uploads/' . basename($file['name']);
                if (move_uploaded_file($file['tmp_name'], $file_path)) {
                    $documents[] = ['title' => $doc_title, 'path' => $file_path];
                } else {
                    $error = "Failed to upload document $i.";
                    break;
                }
            }
        }

        if (!$error) {
            try {
                $pdo->beginTransaction();

                $sql = "INSERT INTO projects (project_id, title, description, customer_name, budget, start_date, end_date)
                        VALUES (:project_id, :title, :description, :customer_name, :budget, :start_date, :end_date)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':project_id' => $new_project_id,
                    ':title' => $title,
                    ':description' => $description,
                    ':customer_name' => $customer_name,
                    ':budget' => $budget,
                    ':start_date' => $start_date,
                    ':end_date' => $end_date
                ]);

                foreach ($documents as $doc) {
                    $sql = "INSERT INTO project_documents (project_id, title, path) VALUES (:project_id, :title, :path)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':project_id' => $new_project_id,
                        ':title' => $doc['title'],
                        ':path' => $doc['path']
                    ]);
                }

                $pdo->commit();
                $success = "Project added successfully!";
            } catch (Exception $e) {
                $pdo->rollBack();
                $error = "Error adding project: " . htmlspecialchars($e->getMessage());
            }
        }
    }
}

include 'header.php';
include 'navigation.php';
?>
<link rel="stylesheet" href="styles.css">
<title>Add Project</title>
<main class="main">
    <section class="container">
        <h2>Add Project</h2>
        <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
        <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <label for="project_id">Project ID</label>
            <input type="text" id="project_id_display" value="<?php echo $new_project_id; ?>" readonly>
            <input type="hidden" id="project_id" name="project_id" value="<?php echo $new_project_id; ?>">
            <label for="title">Project Title</label>
            <input type="text" id="title" name="title" placeholder="Project Title" required>
            <label for="description">Project Description</label>
            <textarea id="description" name="description" placeholder="Description" required></textarea>
            <label for="customer_name">Customer Name</label>
            <input type="text" id="customer_name" name="customer_name" placeholder="Customer Name" required>
            <label for="budget">Total Budget</label>
            <input type="number" id="budget" name="budget" step="0.01" placeholder="Budget" required>
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" required>
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" required>
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <label for="document_title_<?php echo $i; ?>">Document Title <?php echo $i; ?></label>
                <input type="text" id="document_title_<?php echo $i; ?>" name="document_title_<?php echo $i; ?>" placeholder="Document Title <?php echo $i; ?>">
                <input type="file" id="document_<?php echo $i; ?>" name="document_<?php echo $i; ?>" accept=".pdf,.docx,.png,.jpg">
            <?php endfor; ?>
            <button type="submit">Add Project</button>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>