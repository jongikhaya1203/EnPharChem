<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/enpharchem/dashboard" class="text-decoration-none" style="color:var(--epc-accent);">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/enpharchem/control-panel" class="text-decoration-none" style="color:var(--epc-accent);">Control Panel</a></li>
        <li class="breadcrumb-item active text-light">Formulae Control Sheets</li>
    </ol>
</nav>

<div class="page-header">
    <h1><i class="fas fa-calculator me-2" style="color:var(--epc-accent);"></i>Formulae Control Sheets</h1>
    <p class="text-muted mb-0">Interactive calculation sheets with engineering formulas for real-time mix calculations</p>
</div>

<div class="row g-4 mt-2">
    <!-- Energy Chemical Mix -->
    <div class="col-md-4">
        <a href="/enpharchem/control-panel/formulae/energy-mix" class="text-decoration-none">
            <div class="card h-100" style="transition:all .3s;cursor:pointer;border:1px solid rgba(255,255,255,.08);" onmouseover="this.style.borderColor='#fd7e14';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)';this.style.transform='none'">
                <div class="card-body p-4">
                    <div style="width:64px;height:64px;border-radius:14px;background:linear-gradient(135deg,#fd7e14,#dc3545);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.8rem;margin-bottom:20px;box-shadow:0 4px 16px rgba(253,126,20,.3);">
                        <i class="fas fa-fire"></i>
                    </div>
                    <h4 class="text-light mb-2">Energy Chemical Mix</h4>
                    <p class="text-secondary mb-3" style="font-size:.9rem;line-height:1.6;">
                        Fuel blend calculations, combustion stoichiometry, heating values, flue gas composition, and energy balance formulas.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-dark border border-secondary">LHV / HHV</span>
                        <span class="badge bg-dark border border-secondary">Stoichiometry</span>
                        <span class="badge bg-dark border border-secondary">Combustion</span>
                        <span class="badge bg-dark border border-secondary">Flue Gas</span>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center" style="background:transparent;border-color:rgba(255,255,255,.06);">
                    <span style="font-size:.85rem;color:#fd7e14;font-weight:600;">Open Control Sheet</span>
                    <i class="fas fa-arrow-right" style="color:#fd7e14;"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Pharmaceutical Chemical Mix -->
    <div class="col-md-4">
        <a href="/enpharchem/control-panel/formulae/pharma-mix" class="text-decoration-none">
            <div class="card h-100" style="transition:all .3s;cursor:pointer;border:1px solid rgba(255,255,255,.08);" onmouseover="this.style.borderColor='#0dcaf0';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)';this.style.transform='none'">
                <div class="card-body p-4">
                    <div style="width:64px;height:64px;border-radius:14px;background:linear-gradient(135deg,#0dcaf0,#6f42c1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.8rem;margin-bottom:20px;box-shadow:0 4px 16px rgba(13,202,240,.3);">
                        <i class="fas fa-pills"></i>
                    </div>
                    <h4 class="text-light mb-2">Pharmaceutical Mix</h4>
                    <p class="text-secondary mb-3" style="font-size:.9rem;line-height:1.6;">
                        API dosage, dilution calculations, batch scaling, excipient ratios, solution concentration, and potency adjustments.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-dark border border-secondary">API Dose</span>
                        <span class="badge bg-dark border border-secondary">Dilution C1V1=C2V2</span>
                        <span class="badge bg-dark border border-secondary">Batch Size</span>
                        <span class="badge bg-dark border border-secondary">Molarity</span>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center" style="background:transparent;border-color:rgba(255,255,255,.06);">
                    <span style="font-size:.85rem;color:#0dcaf0;font-weight:600;">Open Control Sheet</span>
                    <i class="fas fa-arrow-right" style="color:#0dcaf0;"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Chemical Engineering Mix -->
    <div class="col-md-4">
        <a href="/enpharchem/control-panel/formulae/chemical-mix" class="text-decoration-none">
            <div class="card h-100" style="transition:all .3s;cursor:pointer;border:1px solid rgba(255,255,255,.08);" onmouseover="this.style.borderColor='#198754';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)';this.style.transform='none'">
                <div class="card-body p-4">
                    <div style="width:64px;height:64px;border-radius:14px;background:linear-gradient(135deg,#198754,#0d6efd);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.8rem;margin-bottom:20px;box-shadow:0 4px 16px rgba(25,135,84,.3);">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h4 class="text-light mb-2">Chemical Engineering Mix</h4>
                    <p class="text-secondary mb-3" style="font-size:.9rem;line-height:1.6;">
                        Mass balance, reactor design, heat transfer, pressure drop, equilibrium constants, and multi-component mixing rules.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-dark border border-secondary">Mass Balance</span>
                        <span class="badge bg-dark border border-secondary">Raoult's Law</span>
                        <span class="badge bg-dark border border-secondary">Arrhenius</span>
                        <span class="badge bg-dark border border-secondary">Bernoulli</span>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center" style="background:transparent;border-color:rgba(255,255,255,.06);">
                    <span style="font-size:.85rem;color:#198754;font-weight:600;">Open Control Sheet</span>
                    <i class="fas fa-arrow-right" style="color:#198754;"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="card border-0 mt-4" style="background:rgba(13,202,240,.05);border:1px solid rgba(13,202,240,.2) !important;">
    <div class="card-body">
        <h6 style="color:#0dcaf0;"><i class="fas fa-info-circle me-2"></i>About Formulae Control Sheets</h6>
        <p style="font-size:.85rem;color:#9ca3af;margin:0;line-height:1.6;">
            These interactive control sheets provide real-time engineering calculations for mixing, blending, and formulation design across Energy, Pharmaceutical, and Chemical Engineering domains.
            All formulas follow industry-standard equations (ASTM, API, USP, IUPAC). Input parameters update results instantly - no server round-trip needed.
            Results can be exported as CSV or printed as PDF for documentation.
        </p>
    </div>
</div>
