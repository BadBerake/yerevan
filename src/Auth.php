<?php

class Auth {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function register($username, $email, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $this->db->query(
                "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)",
                [$username, $email, $hash]
            );
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function login($email, $password) {
        $stmt = $this->db->query("SELECT * FROM users WHERE email = ?", [$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Check status
            if (isset($user['status']) && $user['status'] !== 'active') {
                return false;
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            return true;
        }
        return false;
    }

    public function logout() {
        session_destroy();
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public function getUser() {
        if (!$this->isLoggedIn()) return null;
        
        $stmt = $this->db->query("SELECT id, username, email, role, points, level, avatar_url FROM users WHERE id = ?", [$_SESSION['user_id']]);
        return $stmt->fetch();
    }
}
