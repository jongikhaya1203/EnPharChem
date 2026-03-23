/**
 * EnPharChem Platform - Main JavaScript
 * Energy, Pharmaceutical & Chemical Engineering Software
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initTabs();
    initAlerts();
    initTooltips();
    highlightActiveNav();
});

/* ============================================================
   SIDEBAR
   ============================================================ */
function initSidebar() {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.ep-sidebar');
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    overlay.style.cssText = 'display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1015;';
    document.body.appendChild(overlay);

    if (toggle) {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
        });
    }

    overlay.addEventListener('click', function() {
        sidebar.classList.remove('show');
        overlay.style.display = 'none';
    });
}

function highlightActiveNav() {
    const currentPath = window.location.pathname.replace('/enpharchem/', '').replace(/\/$/, '');
    const navLinks = document.querySelectorAll('.ep-sidebar-nav .nav-link');

    navLinks.forEach(function(link) {
        const href = link.getAttribute('href');
        if (!href) return;
        const linkPath = href.replace('/enpharchem/', '').replace(/\/$/, '');

        if (currentPath === linkPath || (linkPath && currentPath.startsWith(linkPath) && linkPath !== '')) {
            link.classList.add('active');
        }
    });
}

/* ============================================================
   TABS
   ============================================================ */
function initTabs() {
    document.querySelectorAll('[data-tab]').forEach(function(tab) {
        tab.addEventListener('click', function() {
            const targetId = this.getAttribute('data-tab');
            const tabGroup = this.closest('.ep-tabs') || this.parentElement;

            // Deactivate all tabs in group
            tabGroup.querySelectorAll('[data-tab]').forEach(function(t) {
                t.classList.remove('active');
            });

            // Hide all tab contents
            const container = tabGroup.nextElementSibling ? tabGroup.parentElement : document;
            container.querySelectorAll('.ep-tab-content').forEach(function(content) {
                content.classList.remove('active');
            });

            // Activate clicked tab and its content
            this.classList.add('active');
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
}

/* ============================================================
   ALERTS
   ============================================================ */
function initAlerts() {
    document.querySelectorAll('.ep-alert .close-alert').forEach(function(btn) {
        btn.addEventListener('click', function() {
            this.closest('.ep-alert').style.display = 'none';
        });
    });
}

/* ============================================================
   TOOLTIPS
   ============================================================ */
function initTooltips() {
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(el) {
            return new bootstrap.Tooltip(el);
        });
    }
}

/* ============================================================
   SIMULATION HELPERS
   ============================================================ */
function runSimulation(simulationId) {
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner spin"></i> Running...';

    fetch('/enpharchem/api/simulations', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'run', simulation_id: simulationId })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            showNotification('Simulation completed successfully!', 'success');
            setTimeout(function() { location.reload(); }, 1500);
        } else {
            showNotification('Simulation failed: ' + (data.error || 'Unknown error'), 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-play"></i> Run Simulation';
        }
    })
    .catch(function(error) {
        showNotification('Error: ' + error.message, 'danger');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-play"></i> Run Simulation';
    });
}

/* ============================================================
   FLOWSHEET CANVAS
   ============================================================ */
function initFlowsheet(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    canvas.width = canvas.parentElement.offsetWidth;
    canvas.height = 500;

    // Draw grid
    ctx.strokeStyle = 'rgba(255,255,255,0.05)';
    ctx.lineWidth = 1;
    for (let x = 0; x < canvas.width; x += 20) {
        ctx.beginPath();
        ctx.moveTo(x, 0);
        ctx.lineTo(x, canvas.height);
        ctx.stroke();
    }
    for (let y = 0; y < canvas.height; y += 20) {
        ctx.beginPath();
        ctx.moveTo(0, y);
        ctx.lineTo(canvas.width, y);
        ctx.stroke();
    }

    // Draw sample process flow
    drawUnitOperation(ctx, 100, 200, 'Feed', '#0d6efd');
    drawUnitOperation(ctx, 300, 150, 'Reactor', '#0dcaf0');
    drawUnitOperation(ctx, 300, 280, 'Heat Exchanger', '#198754');
    drawUnitOperation(ctx, 500, 200, 'Separator', '#ffc107');
    drawUnitOperation(ctx, 700, 150, 'Product', '#0d6efd');
    drawUnitOperation(ctx, 700, 280, 'Recycle', '#dc3545');

    // Draw connections
    drawStream(ctx, 160, 220, 280, 170);
    drawStream(ctx, 160, 220, 280, 300);
    drawStream(ctx, 360, 170, 480, 210);
    drawStream(ctx, 360, 300, 480, 230);
    drawStream(ctx, 560, 210, 680, 170);
    drawStream(ctx, 560, 230, 680, 300);
}

function drawUnitOperation(ctx, x, y, label, color) {
    ctx.fillStyle = color;
    ctx.globalAlpha = 0.2;
    ctx.fillRect(x - 40, y - 25, 80, 50);
    ctx.globalAlpha = 1;
    ctx.strokeStyle = color;
    ctx.lineWidth = 2;
    ctx.strokeRect(x - 40, y - 25, 80, 50);
    ctx.fillStyle = '#e9ecef';
    ctx.font = '11px Inter, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText(label, x, y + 4);
}

function drawStream(ctx, x1, y1, x2, y2) {
    ctx.strokeStyle = '#4a9eff';
    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(x1, y1);
    const midX = (x1 + x2) / 2;
    ctx.lineTo(midX, y1);
    ctx.lineTo(midX, y2);
    ctx.lineTo(x2, y2);
    ctx.stroke();

    // Arrow
    ctx.fillStyle = '#4a9eff';
    ctx.beginPath();
    ctx.moveTo(x2, y2 - 5);
    ctx.lineTo(x2 + 8, y2);
    ctx.lineTo(x2, y2 + 5);
    ctx.fill();
}

/* ============================================================
   NOTIFICATIONS
   ============================================================ */
function showNotification(message, type) {
    type = type || 'info';
    const container = document.getElementById('notificationContainer') || createNotificationContainer();

    const alert = document.createElement('div');
    alert.className = 'ep-alert ep-alert-' + type + ' fade-in';
    alert.style.cssText = 'margin-bottom:8px;cursor:pointer;';
    alert.innerHTML = '<i class="fas fa-' + getAlertIcon(type) + '"></i> ' + message;
    alert.addEventListener('click', function() { alert.remove(); });

    container.appendChild(alert);
    setTimeout(function() { alert.remove(); }, 5000);
}

function createNotificationContainer() {
    const container = document.createElement('div');
    container.id = 'notificationContainer';
    container.style.cssText = 'position:fixed;top:70px;right:20px;width:350px;z-index:9999;';
    document.body.appendChild(container);
    return container;
}

function getAlertIcon(type) {
    var icons = { success: 'check-circle', danger: 'exclamation-circle', warning: 'exclamation-triangle', info: 'info-circle' };
    return icons[type] || 'info-circle';
}

/* ============================================================
   CHARTS HELPER
   ============================================================ */
function createChart(canvasId, config) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || typeof Chart === 'undefined') return null;

    // Apply dark theme defaults
    Chart.defaults.color = '#9ca3af';
    Chart.defaults.borderColor = '#2d3238';
    Chart.defaults.backgroundColor = 'rgba(13, 110, 253, 0.5)';

    return new Chart(canvas.getContext('2d'), config);
}

/* ============================================================
   UTILITY FUNCTIONS
   ============================================================ */
function formatNumber(num) {
    return new Intl.NumberFormat().format(num);
}

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric', month: 'short', day: 'numeric'
    });
}

function confirmAction(message) {
    return confirm(message || 'Are you sure you want to proceed?');
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showNotification('Copied to clipboard', 'success');
    });
}

// Delete confirmation
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-delete')) {
        if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
            e.preventDefault();
        }
    }
});

// Form validation
document.querySelectorAll('form[data-validate]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        var required = form.querySelectorAll('[required]');
        var valid = true;
        required.forEach(function(field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                valid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        if (!valid) {
            e.preventDefault();
            showNotification('Please fill in all required fields.', 'warning');
        }
    });
});

// Auto-resize textareas
document.querySelectorAll('textarea[data-autoresize]').forEach(function(textarea) {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });
});

/* ============================================================
   DASHBOARD STATS (AJAX)
   ============================================================ */
function refreshDashboardStats() {
    fetch('/enpharchem/api/dashboard-stats')
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.total_projects !== undefined) {
                var el = document.getElementById('stat-projects');
                if (el) el.textContent = formatNumber(data.total_projects);
            }
            if (data.active_simulations !== undefined) {
                var el = document.getElementById('stat-active-sims');
                if (el) el.textContent = formatNumber(data.active_simulations);
            }
            if (data.completed_simulations !== undefined) {
                var el = document.getElementById('stat-completed-sims');
                if (el) el.textContent = formatNumber(data.completed_simulations);
            }
        })
        .catch(function(error) {
            console.error('Failed to refresh stats:', error);
        });
}
