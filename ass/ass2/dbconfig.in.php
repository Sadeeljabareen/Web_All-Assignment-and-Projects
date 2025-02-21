<?php
$host = 'localhost';
$db_name = 'web1220465_db';
$db_username = 'web1220465_dbuser';
$db_password = 'Sadeel@123456'; 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $db_username, $db_password);
} catch (PDOException $e) {
    exit('Error Connecting To DataBase: ' . $e->getMessage());
}
?>
