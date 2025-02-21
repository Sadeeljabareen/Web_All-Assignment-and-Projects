<?php
$host = 'localhost';
$db = 'web1220465_tap_db'; // Your database name
$user = 'web1220465_dbuser'; // Your database username
$pass = 'Sadeel@123456'; // Your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>