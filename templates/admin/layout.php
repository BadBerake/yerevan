<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?> | Yerevango Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #D90012;
            --primary-dark: #b91c1c;
            --sidebar-width: 260px;
            --header-height: 64px;
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f3f4f6; color: #1f2937; display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: #111827;
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 50;
        }
        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            border-bottom: 1px solid #1f2937;
            font-weight: 700;
            font-size: 1.2rem;
            color: white;
            text-decoration: none;
        }
        .sidebar-nav {
            flex: 1;
            padding: 1.5rem 1rem;
            overflow-y: auto;
        }
        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #9ca3af;
            text-decoration: none;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
            font-weight: 500;
        }
        .nav-item:hover, .nav-item.active {
            background: #374151;
            color: white;
        }
        .nav-item i { width: 24px; margin-right: 12px; text-align: center; }
        
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid #1f2937;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
        }
        
        .header {
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 40;
        }
        
        .content-area {
            padding: 2rem;
            flex: 1;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }
        .btn-outline { border-color: #d1d5db; background: white; color: #374151; }
        .btn-outline:hover { background: #f3f4f6; }
        
        .card { background: white; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; margin-bottom: 1.5rem; border: 1px solid #e5e7eb; }
        .card-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; font-weight: 600; }
        .card-body { padding: 1.5rem; }
        
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 0.75rem 1.5rem; background: #f9fafb; color: #6b7280; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e5e7eb; }
        td { padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; color: #374151; font-size: 0.875rem; }
        tr:last-child td { border-bottom: none; }
        
        .status-badge { display: inline-flex; padding: 2px 8px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; }
        .success { background: #d1fae5; color: #065f46; }
        .warning { background: #fef3c7; color: #92400e; }
        .danger { background: #fee2e2; color: #991b1b; }
        
        /* Utility */
        .text-right { text-align: right; }
        .mb-4 { margin-bottom: 1rem; }
    </style>
</head>
<body>

<aside class="sidebar">
    <a href="/admin" class="sidebar-header">
        <span style="color: var(--primary);">Admin</span>Portal
    </a>
    <nav class="sidebar-nav">
        <a href="/admin" class="nav-item <?= $active === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="/admin/users" class="nav-item <?= $active === 'users' ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Users
        </a>
        <a href="/admin/analytics" class="nav-item <?= $active === 'analytics' ? 'active' : '' ?>">
            <i class="fas fa-chart-pie"></i> Analytics
        </a>
        <a href="/admin/content" class="nav-item <?= $active === 'content' ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i> Content
        </a>
        <a href="/admin/settings" class="nav-item <?= $active === 'settings' ? 'active' : '' ?>">
            <i class="fas fa-cog"></i> Settings
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="/" class="nav-item" style="color: #9ca3af;">
            <i class="fas fa-external-link-alt"></i> View Site
        </a>
        <a href="/logout" class="nav-item" style="color: #f87171;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</aside>

<div class="main-content">
    <header class="header">
        <h2 style="font-size: 1.25rem; font-weight: 600;"><?= $title ?? 'Dashboard' ?></h2>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 35px; height: 35px; background: #e5e7eb; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-shield" style="color: #4b5563;"></i>
            </div>
        </div>
    </header>
    
    <div class="content-area">
        <?= $content ?>
    </div>
</div>

</body>
</html>
