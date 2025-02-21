<?php
session_start();
require 'db.inc.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['step3'])) {
    $sqlCheck = "SELECT COUNT(*) FROM users WHERE email = :email OR username = :username";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([
        ':email' => $_SESSION['email'],
        ':username' => $_SESSION['username']
    ]);
    $count = $stmtCheck->fetchColumn();

    if ($count == 0) {
        $sql = "INSERT INTO users (username, password, name, role, email, phone, address, qualification, skills, dob, idNumber) VALUES 
                (:username, :password, :name, :role, :email, :phone, :address, :qualification, :skills, :dob, :idNumber)";
        $stmt = $pdo->prepare($sql);

        $address = $_SESSION['flat_house'] . ', ' . $_SESSION['street'] . ', ' . $_SESSION['city'] . ', ' . $_SESSION['country'];

        if ($stmt->execute([
            ':username' => $_SESSION['username'],
            ':password' => $_SESSION['password'],
            ':name' => $_SESSION['name'],
            ':role' => $_SESSION['role'],
            ':email' => $_SESSION['email'],
            ':phone' => $_SESSION['phone'],
            ':address' => $address,
            ':qualification' => $_SESSION['qualification'],
            ':skills' => $_SESSION['skills'],
            ':dob' => $_SESSION['dob'],
            ':idNumber' => $_SESSION['idNumber'] 
        ])) {
            $userId = $pdo->lastInsertId();
            $_SESSION['id'] = $userId;
            $_SESSION['success'] = "Registration successful! Your User ID is $userId. <a href='login.php'>Click here to log in.</a>";
            header('Location: registration_success.php');
            exit;
        } else {
            $error = "Error: Unable to register user.";
        }
    } else {
        $error = "Error: Username or email already exists.";
    }
}

include 'header.php';
?>
<link rel="stylesheet" href="styles.css">
<title>registration Step Three</title>
<main>
    <section class="container">
        <h2>User Registration - Step 3</h2>
        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="hidden" name="step3" value="1">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $_SESSION['name']; ?>" readonly>
            <label for="address">Address:</label>
            <textarea id="address" name="address" readonly><?php echo $_SESSION['flat_house'] . ', ' . $_SESSION['street'] . ', ' . $_SESSION['city'] . ', ' . $_SESSION['country']; ?></textarea>
            <label for="dob">Date of Birth:</label>
            <input type="text" id="dob" name="dob" value="<?php echo $_SESSION['dob']; ?>" readonly>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $_SESSION['email']; ?>" readonly>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo $_SESSION['phone']; ?>" readonly>
            <label for="role">Role:</label>
            <input type="text" id="role" name="role" value="<?php echo $_SESSION['role']; ?>" readonly>
            <label for="qualification">Qualification:</label>
            <input type="text" id="qualification" name="qualification" value="<?php echo $_SESSION['qualification']; ?>" readonly>
            <label for="skills">Skills:</label>
            <textarea id="skills" name="skills" readonly><?php echo $_SESSION['skills']; ?></textarea>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo $_SESSION['username']; ?>" readonly>
            <button type="submit">Confirm and Register</button>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>