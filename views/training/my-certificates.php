<!-- My Certificates -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color: var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/training" class="text-decoration-none" style="color: var(--epc-accent);">Training</a></li>
        <li class="breadcrumb-item active text-light" aria-current="page">My Certificates</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-light mb-0"><i class="fas fa-certificate me-2 text-warning"></i>My Certificates</h2>
    <a href="/enpharchem/training" class="btn btn-outline-info">
        <i class="fas fa-graduation-cap me-1"></i>Browse Courses
    </a>
</div>

<?php if (empty($certificates)): ?>
    <div class="card border-0" style="background: var(--epc-card-bg);">
        <div class="card-body text-center py-5">
            <i class="fas fa-certificate text-secondary" style="font-size: 4rem;"></i>
            <h4 class="text-light mt-3">No Certificates Yet</h4>
            <p class="text-secondary mb-4">Complete course assessments with a score of 70% or higher to earn certificates.</p>
            <a href="/enpharchem/training" class="btn btn-primary">
                <i class="fas fa-graduation-cap me-2"></i>Start Learning
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php
        $levelColors = ['beginner' => 'success', 'intermediate' => 'primary', 'advanced' => 'warning', 'expert' => 'danger'];
        $statusColors = ['active' => 'success', 'expired' => 'secondary', 'revoked' => 'danger'];
        foreach ($certificates as $cert):
            $lvl = $cert['course_level'] ?? 'beginner';
            $status = $cert['status'] ?? 'active';
        ?>
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 h-100" style="background: var(--epc-card-bg); transition: transform 0.2s;" onmouseenter="this.style.transform='translateY(-4px)'" onmouseleave="this.style.transform='none'">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="badge bg-<?= $levelColors[$lvl] ?? 'secondary' ?>"><?= ucfirst($lvl) ?></span>
                        <span class="badge bg-<?= $statusColors[$status] ?? 'secondary' ?>"><?= ucfirst($status) ?></span>
                    </div>

                    <div class="text-center mb-3">
                        <div style="width:60px;height:60px;background:linear-gradient(135deg,#d4af37,#f0d060);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;">
                            <i class="fas fa-award text-dark fs-4"></i>
                        </div>
                    </div>

                    <h6 class="text-light fw-bold text-center mb-2"><?= htmlspecialchars($cert['course_title']) ?></h6>

                    <div class="text-center mb-3">
                        <span class="text-<?= ($cert['score'] ?? 0) >= 90 ? 'success' : 'primary' ?> fw-bold fs-5"><?= number_format($cert['score'] ?? 0, 1) ?>%</span>
                    </div>

                    <div class="text-secondary small mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Certificate #</span>
                            <span class="text-light"><?= htmlspecialchars($cert['certificate_number']) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Issued</span>
                            <span class="text-light"><?= date('M d, Y', strtotime($cert['issue_date'])) ?></span>
                        </div>
                        <?php if (!empty($cert['expiry_date'])): ?>
                        <div class="d-flex justify-content-between">
                            <span>Expires</span>
                            <span class="text-light"><?= date('M d, Y', strtotime($cert['expiry_date'])) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-auto">
                        <a href="/enpharchem/training/certificate?id=<?= $cert['id'] ?>" class="btn btn-warning btn-sm w-100" target="_blank">
                            <i class="fas fa-external-link-alt me-1"></i>View Certificate
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
