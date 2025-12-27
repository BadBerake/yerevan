<!DOCTYPE html>
<html lang="<?= Lang::current() ?>" dir="<?= Lang::getDir() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Yerevango</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            background-color: #2c3e50;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        .admin-login-card {
            background: white;
            padding: 2.5rem;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            text-align: center;
        }
        .admin-login-header {
            margin-bottom: 2rem;
        }
        .admin-login-header h1 {
            color: #2c3e50;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #bdc3c7;
            border-radius: 6px;
            font-size: 1rem;
        }
        .btn-admin {
            width: 100%;
            padding: 12px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-admin:hover {
            background-color: #c0392b;
        }
        .back-link {
            display: block;
            margin-top: 1.5rem;
            color: #95a5a6;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .back-link:hover { color: white; }
    </style>
</head>
<body>
    <div class="admin-login-card">
        <div class="admin-login-header">
            <h2>YerevanGo Admin</h2>
            <p style="color: #95a5a6; font-size: 0.9rem;">Secure Access Only</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div style="background: #fde8e8; color: #c0392b; padding: 10px; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem;">
                Invalid credentials or insufficient permissions.
            </div>
        <?php endif; ?>

        <form action="/admin/login" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-admin">Login to Dashboard</button>
        </form>
    </div>
    
    <div style="position: absolute; bottom: 20px;">
        <a href="/" class="back-link">← Back to Yerevango</a>
    </div>
</body>
</html>
