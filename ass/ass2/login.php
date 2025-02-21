<?php
session_start();
require 'dbconfig.in.php'; // data base connect

$email = $password = $user = null; // initilize variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // check user data
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch();

    if ($user && $password === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];
        header("Location: ticketsys.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
    
}

 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <header>
        <figure>
            <img src="Natural_Solutions.png" alt="Natural Solutions Logo" width="100" height="100">
        </figure>
        <h1>Maintenance Request System</h1>
    </header>
    <hr>

    <main>
        <form method="POST">
            <table>
                <tr>
                    <td><label for="email">Email:</label></td>
                    <td><input type="email" name="email" required></td>
                </tr>
                <tr>
                    <td><label for="password">Password:</label></td>
                    <td><input type="password" name="password" required></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;"><button type="submit">Login</button></td>
                </tr>
            </table>
        </form>

        <?php if (isset($error)) { echo "<p>$error</p>"; } ?>
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
</body>
</html>


