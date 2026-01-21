<?php

namespace App\Middleware;

class AdminMiddleware
{
    private $auth;

    public function __construct($auth)
    {
        $this->auth = $auth;
    }

    public function handle()
    {
        if (!$this->auth->isLoggedIn()) {
            header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }

        if (!$this->auth->isAdmin()) {
            http_response_code(403);
            echo "403 Forbidden - Access Denied";
            exit;
        }
    }
}
