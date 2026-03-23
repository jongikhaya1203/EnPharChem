<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | EnPharChem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f1117 0%, #1a1d23 40%, #141720 100%);
            color: #dee2e6;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; margin: 0;
        }
        .error-container { text-align: center; max-width: 480px; padding: 2rem; }
        .error-code {
            font-size: 8rem; font-weight: 800; line-height: 1;
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .error-title { font-size: 1.5rem; font-weight: 700; color: #fff; margin: 1rem 0 .5rem; }
        .error-desc { color: #6c757d; margin-bottom: 2rem; line-height: 1.6; }
        .btn-home {
            display: inline-flex; align-items: center; gap: .5rem;
            padding: .7rem 1.5rem;
            background: #0d6efd; color: #fff;
            border: none; border-radius: 8px;
            text-decoration: none; font-weight: 600;
            transition: .2s;
        }
        .btn-home:hover { background: #0b5ed7; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(13,110,253,.3); }
        .brand-link {
            display: flex; align-items: center; justify-content: center; gap: .5rem;
            margin-bottom: 2rem; text-decoration: none;
        }
        .brand-link .icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: .9rem;
        }
        .brand-link span { font-weight: 700; font-size: 1.1rem; color: #fff; }
        .brand-link .accent { color: #0dcaf0; }
    </style>
</head>
<body>
    <div class="error-container">
        <a href="/enpharchem/" class="brand-link">
            <span class="icon"><i class="fas fa-atom"></i></span>
            <span>En<span class="accent">Phar</span>Chem</span>
        </a>
        <div class="error-code">404</div>
        <div class="error-title">Page Not Found</div>
        <p class="error-desc">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        <a href="/enpharchem/dashboard" class="btn-home">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</body>
</html>
