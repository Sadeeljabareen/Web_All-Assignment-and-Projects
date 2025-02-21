<?php
    require_once 'dbconfig.in.php';
    require_once 'database.class.php';
    $db = new Database($pdo);  // Use 'Database' with a capital D
    $rows = $db->getData();
?>
