<?php
session_start();
require 'db.inc.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['step1'])) {
    $_SESSION['name'] = htmlspecialchars($_POST['name']);
    $_SESSION['flat_house'] = htmlspecialchars($_POST['flat_house']);
    $_SESSION['street'] = htmlspecialchars($_POST['street']);
    $_SESSION['city'] = htmlspecialchars($_POST['city']);
    $_SESSION['country'] = htmlspecialchars($_POST['country']);
    $_SESSION['dob'] = htmlspecialchars($_POST['dob']);
    $_SESSION['idNumber'] = htmlspecialchars($_POST['idNumber']);
    $_SESSION['email'] = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $_SESSION['phone'] = htmlspecialchars($_POST['phone']);
    $_SESSION['role'] = htmlspecialchars($_POST['role']);
    $_SESSION['qualification'] = htmlspecialchars($_POST['qualification']);
    $_SESSION['skills'] = htmlspecialchars($_POST['skills']);
    

    if ($_SESSION['email']) {
        header('Location: registration_step2.php');
        exit;
    } else {
        $error = "Invalid email address.";
    }
}

include 'header.php';
?>
<link rel="stylesheet" href="styles.css">
<title>registration Step one</title>
<main>
    <section class="container">
        <h2>User Registration - Step 1</h2>
        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
        <form method="POST" action="">
            <input type="hidden" name="step1" value="1">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="flat_house">Flat/House No:</label>
            <input type="text" id="flat_house" name="flat_house" required>
            <label for="street">Street:</label>
            <input type="text" id="street" name="street" required>
            <label for="city">City:</label>
            <input type="text" id="city" name="city" required>
            <label for="country">Country:</label>
            <input type="text" id="country" name="country" required>
            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>
            <label for="idNumber">ID Number:</label>
            <input type="text" id="idNumber" name="idNumber" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required>
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="Manager">Manager</option>
                <option value="Project Leader">Project Leader</option>
                <option value="Team Member">Team Member</option>
            </select>
            <label for="qualification">Qualification:</label>
            <input type="text" id="qualification" name="qualification" required>
            <label for="skills">Skills:</label>
            <textarea id="skills" name="skills" required></textarea>
            <button type="submit">Proceed</button>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>