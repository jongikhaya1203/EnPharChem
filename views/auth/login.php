<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EnPharChem Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --epc-primary: #0d6efd; --epc-accent: #0dcaf0; }
        body {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #0f1117 0%, #1a1d23 40%, #141720 100%);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #dee2e6;
        }
        .login-wrapper { width: 100%; max-width: 440px; padding: 1rem; }
        .brand-header {
            text-align: center; margin-bottom: 2rem;
        }
        .brand-header .brand-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--epc-primary), var(--epc-accent));
            border-radius: 16px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #fff;
            margin-bottom: .75rem;
        }
        .brand-header h1 { font-size: 1.6rem; font-weight: 700; margin: 0; color: #fff; }
        .brand-header h1 span { color: var(--epc-accent); }
        .brand-header p { color: #6c757d; font-size: .9rem; margin-top: .3rem; }
        .login-card {
            background: #212529;
            border: 1px solid rgba(255,255,255,.06);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 40px rgba(0,0,0,.4);
        }
        .login-card h2 { font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem; }
        .form-control {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            color: #dee2e6;
            border-radius: 8px;
            padding: .6rem .9rem;
        }
        .form-control:focus {
            background: rgba(255,255,255,.1);
            border-color: var(--epc-primary);
            color: #dee2e6;
            box-shadow: 0 0 0 .2rem rgba(13,110,253,.15);
        }
        .form-label { font-weight: 500; font-size: .9rem; color: #adb5bd; }
        .btn-login {
            background: linear-gradient(135deg, var(--epc-primary), #0b5ed7);
            border: none; color: #fff;
            padding: .65rem; border-radius: 8px;
            font-weight: 600; width: 100%;
            transition: .2s;
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(13,110,253,.3); color: #fff; }
        .form-check-input { background-color: rgba(255,255,255,.1); border-color: rgba(255,255,255,.2); }
        .form-check-input:checked { background-color: var(--epc-primary); border-color: var(--epc-primary); }
        .form-check-label { font-size: .85rem; color: #adb5bd; }
        .login-footer { text-align: center; margin-top: 1.5rem; font-size: .85rem; color: #6c757d; }
        .login-footer a { color: var(--epc-accent); text-decoration: none; }
        .login-footer a:hover { text-decoration: underline; }
        .alert { border-radius: 8px; font-size: .9rem; }
        .input-group-text {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.1);
            border-right: none;
            color: #6c757d;
        }
        .input-group .form-control { border-left: none; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="brand-header">
            <div class="brand-icon"><i class="fas fa-atom"></i></div>
            <h1>En<span>Phar</span>Chem</h1>
            <p>Energy, Pharmaceutical &amp; Chemical Engineering</p>
        </div>

        <div class="login-card">
            <h2>Sign in to your account</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/enpharchem/login">
                <div class="mb-3">
                    <label for="username" class="form-label">Username or Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username or email" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <a href="/enpharchem/forgot-password" style="font-size:.85rem;color:#0dcaf0;text-decoration:none;">Forgot password?</a>
                </div>

                <button type="submit" class="btn btn-login"><i class="fas fa-sign-in-alt me-2"></i>Sign In</button>
            </form>
        </div>

        <div class="login-footer">
            Don't have an account? <a href="/enpharchem/register">Create one now</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
