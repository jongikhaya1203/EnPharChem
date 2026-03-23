<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EnPharChem Platform</title>
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
            padding: 2rem 0;
        }
        .register-wrapper { width: 100%; max-width: 520px; padding: 1rem; }
        .brand-header { text-align: center; margin-bottom: 2rem; }
        .brand-header .brand-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--epc-primary), var(--epc-accent));
            border-radius: 16px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 1.5rem; color: #fff; margin-bottom: .75rem;
        }
        .brand-header h1 { font-size: 1.6rem; font-weight: 700; margin: 0; color: #fff; }
        .brand-header h1 span { color: var(--epc-accent); }
        .brand-header p { color: #6c757d; font-size: .9rem; margin-top: .3rem; }
        .register-card {
            background: #212529;
            border: 1px solid rgba(255,255,255,.06);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 8px 40px rgba(0,0,0,.4);
        }
        .register-card h2 { font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem; }
        .form-control {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            color: #dee2e6; border-radius: 8px;
            padding: .6rem .9rem;
        }
        .form-control:focus {
            background: rgba(255,255,255,.1);
            border-color: var(--epc-primary); color: #dee2e6;
            box-shadow: 0 0 0 .2rem rgba(13,110,253,.15);
        }
        .form-label { font-weight: 500; font-size: .9rem; color: #adb5bd; }
        .btn-register {
            background: linear-gradient(135deg, var(--epc-primary), #0b5ed7);
            border: none; color: #fff;
            padding: .65rem; border-radius: 8px;
            font-weight: 600; width: 100%;
            transition: .2s;
        }
        .btn-register:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(13,110,253,.3); color: #fff; }
        .register-footer { text-align: center; margin-top: 1.5rem; font-size: .85rem; color: #6c757d; }
        .register-footer a { color: var(--epc-accent); text-decoration: none; }
        .register-footer a:hover { text-decoration: underline; }
        .alert { border-radius: 8px; font-size: .9rem; }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <div class="brand-header">
            <div class="brand-icon"><i class="fas fa-atom"></i></div>
            <h1>En<span>Phar</span>Chem</h1>
            <p>Create your account</p>
        </div>

        <div class="register-card">
            <h2>Register a new account</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="/enpharchem/register">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name"
                               value="<?= htmlspecialchars($first_name ?? '') ?>" placeholder="First name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name"
                               value="<?= htmlspecialchars($last_name ?? '') ?>" placeholder="Last name" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?= htmlspecialchars($email ?? '') ?>" placeholder="you@company.com" required>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                           value="<?= htmlspecialchars($username ?? '') ?>" placeholder="Choose a username" required>
                </div>

                <div class="mb-3">
                    <label for="company" class="form-label">Company / Organization</label>
                    <input type="text" class="form-control" id="company" name="company"
                           value="<?= htmlspecialchars($company ?? '') ?>" placeholder="Your company name">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Min 8 characters" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                               placeholder="Repeat password" required>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required
                           style="background-color:rgba(255,255,255,.1);border-color:rgba(255,255,255,.2);">
                    <label class="form-check-label" for="terms" style="font-size:.85rem;color:#adb5bd;">
                        I agree to the <a href="#" style="color:#0dcaf0;">Terms of Service</a> and <a href="#" style="color:#0dcaf0;">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-register"><i class="fas fa-user-plus me-2"></i>Create Account</button>
            </form>
        </div>

        <div class="register-footer">
            Already have an account? <a href="/enpharchem/login">Sign in</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
