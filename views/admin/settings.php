<?php $settings = $settings ?? []; ?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/admin">Admin</a></li>
        <li class="breadcrumb-item active">Settings</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-wrench me-2" style="color:var(--epc-accent);"></i>System Settings</h1>
</div>

<?php if (!empty($success)): ?>
<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- General Settings -->
        <div class="card mb-4">
            <div class="card-header py-3"><i class="fas fa-cog me-2"></i>General Settings</div>
            <div class="card-body">
                <form method="POST" action="/enpharchem/admin/settings">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Platform Name</label>
                            <input type="text" class="form-control" name="app_name"
                                   value="<?= htmlspecialchars($settings['app_name'] ?? 'EnPharChem') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" name="company_name"
                                   value="<?= htmlspecialchars($settings['company_name'] ?? 'EnPharChem Technologies') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email"
                                   value="<?= htmlspecialchars($settings['contact_email'] ?? 'info@enpharchem.com') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Support Email</label>
                            <input type="email" class="form-control" name="support_email"
                                   value="<?= htmlspecialchars($settings['support_email'] ?? 'support@enpharchem.com') ?>">
                        </div>
                    </div>

                    <hr style="border-color:rgba(255,255,255,.08);margin:1.5rem 0;">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Default License Type</label>
                            <select class="form-select" name="default_license">
                                <?php
                                $licenses = ['trial' => 'Trial', 'standard' => 'Standard', 'professional' => 'Professional', 'enterprise' => 'Enterprise'];
                                $currentLicense = $settings['default_license'] ?? 'trial';
                                foreach ($licenses as $val => $label):
                                ?>
                                <option value="<?= $val ?>" <?= $currentLicense === $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Session Timeout (hours)</label>
                            <input type="number" class="form-control" name="session_timeout"
                                   value="<?= (int)($settings['session_timeout'] ?? 8) ?>" min="1" max="72">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Upload Size (MB)</label>
                            <input type="number" class="form-control" name="max_upload_size"
                                   value="<?= (int)($settings['max_upload_size'] ?? 50) ?>" min="1" max="500">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Default Simulation Timeout (min)</label>
                            <input type="number" class="form-control" name="sim_timeout"
                                   value="<?= (int)($settings['sim_timeout'] ?? 30) ?>" min="1" max="1440">
                        </div>
                    </div>

                    <hr style="border-color:rgba(255,255,255,.08);margin:1.5rem 0;">

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="allow_registration" name="allow_registration"
                               <?= ($settings['allow_registration'] ?? true) ? 'checked' : '' ?>
                               style="background-color:rgba(255,255,255,.1);border-color:rgba(255,255,255,.2);">
                        <label class="form-check-label" for="allow_registration">Allow public registration</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode"
                               <?= ($settings['maintenance_mode'] ?? false) ? 'checked' : '' ?>
                               style="background-color:rgba(255,255,255,.1);border-color:rgba(255,255,255,.2);">
                        <label class="form-check-label" for="maintenance_mode">Maintenance mode</label>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="debug_mode" name="debug_mode"
                               <?= ($settings['debug_mode'] ?? false) ? 'checked' : '' ?>
                               style="background-color:rgba(255,255,255,.1);border-color:rgba(255,255,255,.2);">
                        <label class="form-check-label" for="debug_mode">Debug mode (show errors)</label>
                    </div>

                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Settings</button>
                </form>
            </div>
        </div>

        <!-- Database Info -->
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-database me-2"></i>System Information</div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <tbody>
                        <tr><td style="color:#6c757d;">Platform Version</td><td>1.0.0</td></tr>
                        <tr><td style="color:#6c757d;">PHP Version</td><td><?= phpversion() ?></td></tr>
                        <tr><td style="color:#6c757d;">MySQL Version</td><td><?= htmlspecialchars($mysqlVersion ?? 'N/A') ?></td></tr>
                        <tr><td style="color:#6c757d;">Database Host</td><td>localhost</td></tr>
                        <tr><td style="color:#6c757d;">Database Name</td><td>enpharchem</td></tr>
                        <tr><td style="color:#6c757d;">Server Software</td><td><?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- About -->
        <div class="card mb-4">
            <div class="card-header py-3"><i class="fas fa-info-circle me-2"></i>About EnPharChem</div>
            <div class="card-body text-center">
                <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#0d6efd,#0dcaf0);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.5rem;margin:0 auto 1rem;">
                    <i class="fas fa-atom"></i>
                </div>
                <h5 style="color:#fff;">EnPharChem</h5>
                <p style="color:#6c757d;font-size:.85rem;">Energy, Pharmaceutical &amp; Chemical Engineering Platform</p>
                <p style="color:#6c757d;font-size:.8rem;">Version 1.0.0</p>
                <hr style="border-color:rgba(255,255,255,.08);">
                <p style="font-size:.8rem;color:#adb5bd;line-height:1.6;">
                    EnPharChem delivers enterprise solutions for process simulation, advanced process control, manufacturing execution, supply chain optimization, asset performance management, and digital grid management.
                </p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header py-3"><i class="fas fa-bolt me-2"></i>Maintenance</div>
            <div class="card-body d-grid gap-2">
                <button class="btn btn-outline-primary text-start" onclick="alert('Cache cleared.');"><i class="fas fa-broom me-2"></i>Clear Cache</button>
                <button class="btn btn-outline-primary text-start" onclick="alert('Backup initiated.');"><i class="fas fa-download me-2"></i>Database Backup</button>
                <button class="btn btn-outline-warning text-start" onclick="alert('Logs will be archived.');"><i class="fas fa-archive me-2"></i>Archive Logs</button>
            </div>
        </div>
    </div>
</div>
