<?php
    class Database
    {
        private $pdo;

        function __construct($pdo)
        {
            $this->pdo = $pdo;
        }

        function getData()
{
    try {
        $query = $this->pdo->prepare('SELECT * FROM `tickets`');
        $query->execute();
        return $query->fetchAll();
    } catch (PDOException $e) {
        echo 'SQL Error: ' . $e->getMessage();
    }
}

    }
?>
