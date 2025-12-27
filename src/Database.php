<?php

class Database {
    private $pdo;

    public function __construct(array $config) {
        try {
            $dsn = sprintf("pgsql:host=%s;port=%s;dbname=%s;", 
                $config['host'], 
                $config['port'], 
                $config['dbname']
            );
            
            $this->pdo = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    public function getPdo() {
        return $this->pdo;
    }
}
