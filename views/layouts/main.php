<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : '' ?>EnPharChem Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --epc-primary: #0d6efd;
            --epc-accent: #0dcaf0;
            --epc-dark-bg: #1a1d23;
            --epc-card-bg: #212529;
            --epc-sidebar-w: 280px;
            --epc-navbar-h: 60px;
        }

        * { box-sizing: border-box; }

        body {
            background: #141720;
            color: #dee2e6;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
        }

        /* ── Top Navbar ── */
        .top-navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--epc-navbar-h);
            background: var(--epc-dark-bg);
            border-bottom: 1px solid rgba(255,255,255,.06);
            z-index: 1040;
            display: flex;
            align-items: center;
            padding: 0 1rem;
        }
        .top-navbar .brand {
            width: calc(var(--epc-sidebar-w) - 1rem);
            display: flex;
            align-items: center;
            gap: .6rem;
            font-weight: 700;
            font-size: 1.15rem;
            color: #fff;
            text-decoration: none;
            flex-shrink: 0;
        }
        .top-navbar .brand .brand-icon {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--epc-primary), var(--epc-accent));
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem; color: #fff;
        }
        .top-navbar .brand span.text-accent { color: var(--epc-accent); }
        .navbar-search {
            flex: 1;
            max-width: 420px;
            margin: 0 1.5rem;
        }
        .navbar-search .form-control {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.08);
            color: #dee2e6;
            border-radius: 8px;
            padding-left: 2.4rem;
        }
        .navbar-search .form-control:focus {
            background: rgba(255,255,255,.1);
            border-color: var(--epc-primary);
            box-shadow: 0 0 0 .2rem rgba(13,110,253,.15);
        }
        .navbar-search .search-icon {
            position: absolute; left: .8rem; top: 50%; transform: translateY(-50%);
            color: #6c757d;
        }
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-left: auto;
        }
        .navbar-actions .btn-icon {
            width: 38px; height: 38px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            background: transparent;
            border: none;
            color: #adb5bd;
            position: relative;
            transition: .2s;
        }
        .navbar-actions .btn-icon:hover { background: rgba(255,255,255,.08); color: #fff; }
        .notif-badge {
            position: absolute; top: 4px; right: 4px;
            width: 8px; height: 8px;
            background: #dc3545;
            border-radius: 50%;
        }
        .user-dropdown .dropdown-toggle {
            display: flex; align-items: center; gap: .5rem;
            background: transparent; border: none; color: #dee2e6;
            padding: .35rem .6rem;
            border-radius: 8px;
            transition: .2s;
        }
        .user-dropdown .dropdown-toggle:hover { background: rgba(255,255,255,.08); }
        .user-dropdown .dropdown-toggle::after { display: none; }
        .user-avatar {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--epc-primary), var(--epc-accent));
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: .8rem; color: #fff;
        }
        .dropdown-menu {
            background: var(--epc-card-bg);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 10px;
            box-shadow: 0 8px 32px rgba(0,0,0,.4);
        }
        .dropdown-item { color: #dee2e6; padding: .5rem 1rem; border-radius: 6px; margin: 2px 4px; }
        .dropdown-item:hover { background: rgba(255,255,255,.08); color: #fff; }
        .dropdown-item i { width: 20px; text-align: center; margin-right: .5rem; }
        .dropdown-divider { border-color: rgba(255,255,255,.08); }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: var(--epc-navbar-h);
            left: 0;
            bottom: 0;
            width: var(--epc-sidebar-w);
            background: var(--epc-dark-bg);
            border-right: 1px solid rgba(255,255,255,.06);
            overflow-y: auto;
            z-index: 1030;
            padding: .75rem 0;
            transition: transform .3s ease;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 4px; }

        .sidebar-section {
            padding: .5rem .9rem;
            font-size: .65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #6c757d;
            margin-top: .5rem;
        }
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .5rem 1rem;
            margin: 1px .5rem;
            color: #adb5bd;
            border-radius: 8px;
            font-size: .85rem;
            transition: .15s;
            text-decoration: none;
        }
        .sidebar .nav-link:hover { background: rgba(255,255,255,.06); color: #fff; }
        .sidebar .nav-link.active {
            background: rgba(13,110,253,.15);
            color: var(--epc-primary);
        }
        .sidebar .nav-link i { width: 20px; text-align: center; font-size: .9rem; flex-shrink: 0; }
        .sidebar .nav-link .badge {
            margin-left: auto;
            font-size: .65rem;
            padding: .2em .55em;
        }

        .sidebar-collapse-btn {
            display: flex; align-items: center; justify-content: space-between;
            width: calc(100% - 1rem);
            margin: 1px .5rem;
            padding: .5rem 1rem;
            background: none; border: none;
            color: #adb5bd;
            border-radius: 8px;
            font-size: .85rem;
            cursor: pointer;
            transition: .15s;
        }
        .sidebar-collapse-btn:hover { background: rgba(255,255,255,.06); color: #fff; }
        .sidebar-collapse-btn i.chevron { font-size: .7rem; transition: transform .2s; }
        .sidebar-collapse-btn.collapsed i.chevron { transform: rotate(-90deg); }
        .sidebar .collapse-modules { padding-left: .25rem; }

        /* ── Main content ── */
        .main-content {
            margin-left: var(--epc-sidebar-w);
            margin-top: var(--epc-navbar-h);
            min-height: calc(100vh - var(--epc-navbar-h));
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }
        .main-content > .content-body { flex: 1; }

        /* ── Footer ── */
        .main-footer {
            padding: 1rem 0;
            margin-top: 2rem;
            border-top: 1px solid rgba(255,255,255,.06);
            font-size: .8rem;
            color: #6c757d;
            text-align: center;
        }

        /* ── Cards / Utility ── */
        .card {
            background: var(--epc-card-bg);
            border: 1px solid rgba(255,255,255,.06);
            border-radius: 12px;
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(255,255,255,.06);
            font-weight: 600;
        }
        .stat-card {
            border: none;
            border-radius: 12px;
            background: var(--epc-card-bg);
            border: 1px solid rgba(255,255,255,.06);
            transition: transform .15s, box-shadow .15s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,.3);
        }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .table { color: #dee2e6; }
        .table thead th {
            border-bottom-color: rgba(255,255,255,.08);
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6c757d;
            font-weight: 600;
        }
        .table td { border-bottom-color: rgba(255,255,255,.04); vertical-align: middle; }
        .badge-status-draft { background: #6c757d; }
        .badge-status-running { background: #0d6efd; }
        .badge-status-completed { background: #198754; }
        .badge-status-failed { background: #dc3545; }
        .badge-status-active { background: #198754; }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 1rem;
            font-size: .85rem;
        }
        .breadcrumb-item a { color: var(--epc-accent); text-decoration: none; }
        .breadcrumb-item.active { color: #6c757d; }
        .btn-primary {
            background: var(--epc-primary);
            border-color: var(--epc-primary);
        }
        .btn-outline-primary {
            border-color: var(--epc-primary);
            color: var(--epc-primary);
        }
        .btn-outline-primary:hover {
            background: var(--epc-primary);
            color: #fff;
        }
        .form-control, .form-select {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            color: #dee2e6;
            border-radius: 8px;
        }
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,.1);
            border-color: var(--epc-primary);
            color: #dee2e6;
            box-shadow: 0 0 0 .2rem rgba(13,110,253,.15);
        }
        .form-label { font-weight: 500; font-size: .9rem; color: #adb5bd; }
        .nav-tabs { border-bottom-color: rgba(255,255,255,.08); }
        .nav-tabs .nav-link {
            color: #adb5bd;
            border: none;
            border-bottom: 2px solid transparent;
            border-radius: 0;
            padding: .75rem 1.25rem;
        }
        .nav-tabs .nav-link:hover { color: #fff; border-bottom-color: rgba(255,255,255,.2); }
        .nav-tabs .nav-link.active {
            color: var(--epc-primary);
            background: transparent;
            border-bottom-color: var(--epc-primary);
        }
        .module-card {
            transition: transform .15s, box-shadow .15s;
            cursor: pointer;
        }
        .module-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 24px rgba(0,0,0,.35);
        }
        .module-card .module-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }

        /* ── Sidebar toggle for mobile ── */
        .sidebar-toggle {
            display: none;
            background: transparent; border: none;
            color: #dee2e6; font-size: 1.2rem;
            margin-right: .5rem;
        }
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 1025;
        }

        @media (max-width: 991.98px) {
            .sidebar-toggle { display: block; }
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show { transform: translateX(0); }
            .sidebar-overlay.show { display: block; }
            .main-content { margin-left: 0; }
            .top-navbar .brand { width: auto; }
        }
        @media (max-width: 575.98px) {
            .navbar-search { display: none; }
        }
    </style>
</head>
<body>

<!-- ── Top Navbar ── -->
<nav class="top-navbar">
    <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
    <a href="/enpharchem/" class="brand">
        <span class="brand-icon"><i class="fas fa-atom"></i></span>
        En<span class="text-accent">Phar</span>Chem
    </a>

    <div class="navbar-search position-relative d-none d-sm-block">
        <i class="fas fa-search search-icon"></i>
        <input type="text" class="form-control" placeholder="Search modules, projects, simulations...">
    </div>

    <div class="navbar-actions">
        <button class="btn-icon" title="Notifications">
            <i class="fas fa-bell"></i>
            <span class="notif-badge"></span>
        </button>
        <div class="dropdown user-dropdown">
            <button class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="user-avatar">
                    <?= isset($_SESSION['user']) ? strtoupper(substr($_SESSION['user']['username'] ?? 'U', 0, 1)) : 'U' ?>
                </span>
                <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['user']['username'] ?? 'User') ?></span>
                <i class="fas fa-chevron-down" style="font-size:.6rem;margin-left:.2rem;"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="/enpharchem/profile"><i class="fas fa-user"></i>Profile</a></li>
                <li><a class="dropdown-item" href="/enpharchem/settings"><i class="fas fa-cog"></i>Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/enpharchem/logout"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- ── Sidebar overlay (mobile) ── -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ── Sidebar ── -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-section">Main</div>
    <a href="/enpharchem/dashboard" class="nav-link"><i class="fas fa-th-large"></i>Dashboard</a>
    <a href="/enpharchem/projects" class="nav-link"><i class="fas fa-project-diagram"></i>Projects</a>
    <a href="/enpharchem/simulations" class="nav-link"><i class="fas fa-play-circle"></i>Simulations</a>
    <a href="/enpharchem/benchmark" class="nav-link"><i class="fas fa-chart-bar"></i>Gartner Benchmark</a>
    <a href="/enpharchem/control-panel" class="nav-link"><i class="fas fa-cogs"></i>Control Panel</a>

    <div class="sidebar-section">Module Categories</div>
    <button class="sidebar-collapse-btn" data-bs-toggle="collapse" data-bs-target="#modEnergy" aria-expanded="true">
        <span><i class="fas fa-fire me-2"></i>Energy &amp; Process</span>
        <i class="fas fa-chevron-down chevron"></i>
    </button>
    <div class="collapse show" id="modEnergy">
        <div class="collapse-modules">
            <a href="/enpharchem/process-sim-energy" class="nav-link"><i class="fas fa-bolt"></i>Process Sim &ndash; Energy</a>
            <a href="/enpharchem/process-sim-chemicals" class="nav-link"><i class="fas fa-flask"></i>Process Sim &ndash; Chemicals</a>
            <a href="/enpharchem/exchanger-design" class="nav-link"><i class="fas fa-exchange-alt"></i>Exchanger Design &amp; Rating</a>
            <a href="/enpharchem/concurrent-feed" class="nav-link"><i class="fas fa-drafting-compass"></i>Concurrent FEED</a>
            <a href="/enpharchem/subsurface-science" class="nav-link"><i class="fas fa-mountain"></i>Subsurface Science &amp; Eng.</a>
        </div>
    </div>

    <button class="sidebar-collapse-btn" data-bs-toggle="collapse" data-bs-target="#modOptimize" aria-expanded="true">
        <span><i class="fas fa-cogs me-2"></i>Optimization &amp; Control</span>
        <i class="fas fa-chevron-down chevron"></i>
    </button>
    <div class="collapse show" id="modOptimize">
        <div class="collapse-modules">
            <a href="/enpharchem/energy-optimization" class="nav-link"><i class="fas fa-leaf"></i>Energy &amp; Utilities Opt.</a>
            <a href="/enpharchem/operations-support" class="nav-link"><i class="fas fa-desktop"></i>Operations Support</a>
            <a href="/enpharchem/advanced-process-control" class="nav-link"><i class="fas fa-sliders-h"></i>Advanced Process Control</a>
            <a href="/enpharchem/dynamic-optimization" class="nav-link"><i class="fas fa-chart-line"></i>Dynamic Optimization</a>
        </div>
    </div>

    <button class="sidebar-collapse-btn" data-bs-toggle="collapse" data-bs-target="#modSupply" aria-expanded="true">
        <span><i class="fas fa-boxes me-2"></i>Supply Chain &amp; MES</span>
        <i class="fas fa-chevron-down chevron"></i>
    </button>
    <div class="collapse show" id="modSupply">
        <div class="collapse-modules">
            <a href="/enpharchem/mes" class="nav-link"><i class="fas fa-industry"></i>Manufacturing Execution</a>
            <a href="/enpharchem/petroleum-supply-chain" class="nav-link"><i class="fas fa-oil-can"></i>Petroleum Supply Chain</a>
            <a href="/enpharchem/supply-chain-mgmt" class="nav-link"><i class="fas fa-truck"></i>Supply Chain Management</a>
        </div>
    </div>

    <button class="sidebar-collapse-btn" data-bs-toggle="collapse" data-bs-target="#modAsset" aria-expanded="true">
        <span><i class="fas fa-server me-2"></i>Asset &amp; Data</span>
        <i class="fas fa-chevron-down chevron"></i>
    </button>
    <div class="collapse show" id="modAsset">
        <div class="collapse-modules">
            <a href="/enpharchem/apm" class="nav-link"><i class="fas fa-heartbeat"></i>Asset Performance Mgmt.</a>
            <a href="/enpharchem/industrial-data-fabric" class="nav-link"><i class="fas fa-database"></i>Industrial Data Fabric</a>
            <a href="/enpharchem/digital-grid-mgmt" class="nav-link"><i class="fas fa-plug"></i>Digital Grid Management</a>
        </div>
    </div>

    <?php
    $userRole = $_SESSION['user_role'] ?? ($user['role'] ?? '');
    $isSuperOrAdmin = in_array($userRole, ['superuser', 'admin']);
    ?>
    <?php if ($isSuperOrAdmin): ?>
    <div class="sidebar-section">Control Panel</div>
    <a href="/enpharchem/control-panel" class="nav-link"><i class="fas fa-cogs"></i>Control Panel</a>
    <a href="/enpharchem/control-panel/active-directory" class="nav-link"><i class="fas fa-sitemap"></i>Active Directory</a>
    <a href="/enpharchem/control-panel/cms" class="nav-link"><i class="fas fa-file-alt"></i>CMS Pages</a>
    <a href="/enpharchem/control-panel/marketing" class="nav-link"><i class="fas fa-bullhorn"></i>Marketing Material</a>
    <a href="/enpharchem/control-panel/training" class="nav-link"><i class="fas fa-graduation-cap"></i>Training Material</a>
    <a href="/enpharchem/control-panel/data-management" class="nav-link"><i class="fas fa-database"></i>Data Management</a>

    <div class="sidebar-section">Administration</div>
    <a href="/enpharchem/admin" class="nav-link"><i class="fas fa-shield-alt"></i>Admin Panel</a>
    <a href="/enpharchem/admin/users" class="nav-link"><i class="fas fa-users-cog"></i>User Management</a>
    <a href="/enpharchem/admin/modules" class="nav-link"><i class="fas fa-cubes"></i>Module Management</a>
    <a href="/enpharchem/admin/settings" class="nav-link"><i class="fas fa-wrench"></i>System Settings</a>
    <?php endif; ?>
</aside>

<!-- ── Main content ── -->
<main class="main-content">
    <div class="content-body">
        <?= $content ?>
    </div>
    <footer class="main-footer">
        &copy; 2026 EnPharChem Technologies &mdash; Energy, Pharmaceutical &amp; Chemical Engineering Platform
    </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
    // Sidebar toggle (mobile)
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function closeSidebar(){ sidebar.classList.remove('show'); overlay.classList.remove('show'); }

    if(toggle){
        toggle.addEventListener('click', function(){
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
    }
    if(overlay){ overlay.addEventListener('click', closeSidebar); }

    // Active state highlighting
    const currentPath = window.location.pathname.replace(/\/+$/, '') || '/enpharchem/dashboard';
    document.querySelectorAll('.sidebar .nav-link').forEach(function(link){
        const href = link.getAttribute('href');
        if(href && currentPath === href.replace(/\/+$/, '')){
            link.classList.add('active');
        }
    });

    // Collapse buttons – track state
    document.querySelectorAll('.sidebar-collapse-btn').forEach(function(btn){
        const target = document.querySelector(btn.getAttribute('data-bs-target'));
        if(target){
            target.addEventListener('hide.bs.collapse', function(){ btn.classList.add('collapsed'); });
            target.addEventListener('show.bs.collapse', function(){ btn.classList.remove('collapsed'); });
        }
    });
})();
</script>
</body>
</html>
