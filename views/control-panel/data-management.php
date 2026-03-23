<!-- Data Management -->
<?php $msg = $_GET['msg'] ?? null; ?>

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color: var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel" class="text-decoration-none" style="color: var(--epc-accent);">Control Panel</a></li>
        <li class="breadcrumb-item active text-light" aria-current="page">Data Management</li>
    </ol>
</nav>

<h2 class="text-light mb-4"><i class="bi bi-database-fill-gear me-2" style="color: var(--epc-accent);"></i>Data Management</h2>

<!-- Status Messages -->
<?php if ($msg === 'loaded'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>Sample data loaded successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif ($msg === 'reset'): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i>Sample data has been reset successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif ($msg === 'error'): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>An error occurred. Please try again.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Database Overview -->
<div class="card border-0 mb-4" style="background: var(--epc-card-bg);">
    <div class="card-header border-bottom border-secondary" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="bi bi-database me-2 text-info"></i>Database Overview</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-dark table-hover mb-0" style="background: transparent;">
                <thead>
                    <tr class="text-secondary small text-uppercase">
                        <th>Table Name</th>
                        <th>Row Count</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tableStats)): ?>
                        <?php foreach ($tableStats as $ts): ?>
                            <tr>
                                <td class="text-light fw-semibold">
                                    <i class="bi bi-table me-2 text-secondary"></i><?= htmlspecialchars($ts['table'] ?? '') ?>
                                </td>
                                <td class="text-light"><?= number_format($ts['rows'] ?? 0) ?></td>
                                <td>
                                    <?php if (($ts['rows'] ?? 0) > 0): ?>
                                        <span class="badge bg-success">Has Data</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Empty</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center text-secondary py-4">No tables found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Load / Reset Cards -->
<div class="row g-4 mb-4">
    <!-- Load Sample Data -->
    <div class="col-md-6">
        <div class="card h-100" style="background: var(--epc-card-bg); border: 1px solid #198754;">
            <div class="card-header border-bottom" style="background: var(--epc-card-bg); border-color: #198754 !important;">
                <h5 class="text-success mb-0"><i class="bi bi-cloud-arrow-down-fill me-2"></i>Load Sample Data</h5>
            </div>
            <div class="card-body d-flex flex-column">
                <p class="text-secondary mb-3">Populate the database with sample data for testing and demonstration purposes:</p>
                <dl class="row text-light small mb-3">
                    <dt class="col-sm-4 text-secondary">Users</dt>
                    <dd class="col-sm-8">5 platform users</dd>
                    <dt class="col-sm-4 text-secondary">Projects</dt>
                    <dd class="col-sm-8">8 sample projects</dd>
                    <dt class="col-sm-4 text-secondary">Simulations</dt>
                    <dd class="col-sm-8">15 simulations</dd>
                    <dt class="col-sm-4 text-secondary">AD Groups</dt>
                    <dd class="col-sm-8">10 directory groups</dd>
                    <dt class="col-sm-4 text-secondary">AD Users</dt>
                    <dd class="col-sm-8">20 directory users</dd>
                    <dt class="col-sm-4 text-secondary">CMS Pages</dt>
                    <dd class="col-sm-8">10 content pages</dd>
                    <dt class="col-sm-4 text-secondary">Marketing</dt>
                    <dd class="col-sm-8">15 marketing materials</dd>
                    <dt class="col-sm-4 text-secondary">Training</dt>
                    <dd class="col-sm-8">8 courses, 25 lessons</dd>
                    <dt class="col-sm-4 text-secondary">Components</dt>
                    <dd class="col-sm-8">Chemical components</dd>
                    <dt class="col-sm-4 text-secondary">Assets</dt>
                    <dd class="col-sm-8">Industrial assets</dd>
                </dl>
                <div class="mt-auto">
                    <form method="POST" action="/enpharchem/control-panel/load-sample-data">
                        <button type="submit" class="btn btn-success btn-lg w-100 mb-2">
                            <i class="bi bi-cloud-arrow-down-fill me-2"></i>Load Sample Data
                        </button>
                    </form>
                    <p class="text-warning small mb-0"><i class="bi bi-exclamation-triangle me-1"></i>This will add sample records to existing data. Existing records will not be affected.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Sample Data -->
    <div class="col-md-6">
        <div class="card h-100" style="background: var(--epc-card-bg); border: 1px solid #dc3545;">
            <div class="card-header border-bottom" style="background: var(--epc-card-bg); border-color: #dc3545 !important;">
                <h5 class="text-danger mb-0"><i class="bi bi-trash3-fill me-2"></i>Reset Sample Data</h5>
            </div>
            <div class="card-body d-flex flex-column">
                <p class="text-secondary mb-3">Remove all sample data from the database. This will delete:</p>
                <ul class="text-light small mb-3">
                    <li>All sample users and their associated data</li>
                    <li>All sample projects and simulations</li>
                    <li>All Active Directory groups and users</li>
                    <li>All CMS pages and content</li>
                    <li>All marketing materials</li>
                    <li>All training courses and lessons</li>
                    <li>All chemical components and assets</li>
                    <li>All enrollment and progress records</li>
                </ul>
                <div class="mt-auto">
                    <form method="POST" action="/enpharchem/control-panel/reset-sample-data">
                        <button type="submit" class="btn btn-danger btn-lg w-100 mb-2" onclick="return confirm('Are you sure? This cannot be undone!')">
                            <i class="bi bi-trash3-fill me-2"></i>Reset Sample Data
                        </button>
                    </form>
                    <p class="text-danger small mb-0"><i class="bi bi-exclamation-octagon me-1"></i>Warning: This action is irreversible. All sample data will be permanently deleted.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import / Export Section -->
<div class="card border-0" style="background: var(--epc-card-bg);">
    <div class="card-header border-bottom border-secondary" style="background: var(--epc-card-bg);">
        <h5 class="text-light mb-0"><i class="bi bi-arrow-left-right me-2"></i>Import / Export</h5>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-secondary h-100" style="background: rgba(255,255,255,0.03);">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-box-arrow-in-down text-secondary" style="font-size: 2.5rem;"></i>
                        <h6 class="text-light mt-3">Import Data</h6>
                        <p class="text-secondary small">Import data from CSV, JSON, or XML files.</p>
                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Coming Soon</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-secondary h-100" style="background: rgba(255,255,255,0.03);">
                    <div class="card-body text-center py-4">
                        <i class="bi bi-box-arrow-up text-secondary" style="font-size: 2.5rem;"></i>
                        <h6 class="text-light mt-3">Export Data</h6>
                        <p class="text-secondary small">Export data to CSV, JSON, or PDF formats.</p>
                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Coming Soon</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
