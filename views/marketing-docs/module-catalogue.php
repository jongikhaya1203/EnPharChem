<?php
/**
 * EnPharChem - Module Catalogue Brochure (print-ready A4)
 *
 * Every active module with an interface illustration, capability bullets,
 * version and licence tier, grouped by category.
 *
 * Expects: $categories, $modules, $byCategory, $moduleFeatures
 */
$categories = $categories ?? [];
$modules    = $modules ?? [];
$byCategory = $byCategory ?? [];
$moduleFeatures = $moduleFeatures ?? [];

// Group by category slug so the mockup archetype can be selected per module.
$grouped = [];
foreach ($modules as $m) {
    $grouped[$m['category_slug']][] = $m;
}
$catMeta = [];
foreach ($categories as $c) {
    $catMeta[$c['slug']] = $c;
}

$tierLabel = ['standard' => 'Standard', 'professional' => 'Professional', 'enterprise' => 'Enterprise'];
$generated = date('j F Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EnPharChem Module Catalogue</title>
<style>
@page { size: A4; margin: 12mm; }
@media print {
    .print-bar { display: none !important; }
    .page-break { page-break-before: always; }
    .module-card, .cat-header { page-break-inside: avoid; }
    body { background: #fff; }
}
* { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --primary: #0d6efd;
    --accent: #0dcaf0;
    --ink: #16202e;
    --muted: #64748b;
    --line: #dbe3ec;
    --deep: #0a1628;
}
body {
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    color: var(--ink);
    background: #eef2f7;
    font-size: 11px;
    line-height: 1.55;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
.print-bar {
    position: sticky; top: 0;
    background: linear-gradient(135deg, #0f1117, #1a1d23);
    padding: 12px 26px;
    display: flex; align-items: center; justify-content: space-between;
    z-index: 999;
}
.print-bar .brand { color: #fff; font-weight: 700; font-size: 15px; }
.print-bar .brand span { color: var(--accent); }
.print-bar .actions { display: flex; gap: 10px; }
.btn {
    border: 0; border-radius: 6px; padding: 8px 16px;
    font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none;
    display: inline-block;
}
.btn-print { background: var(--primary); color: #fff; }
.btn-back { background: #2b3038; color: #cfd6de; }

.sheet {
    width: 210mm; min-height: 297mm;
    margin: 0 auto; padding: 14mm 13mm;
    background: #fff;
}
@media print { .sheet { width: auto; min-height: 0; margin: 0; padding: 0; } }

/* ---------- cover ---------- */
.cover {
    background: linear-gradient(150deg, var(--deep) 0%, #123a6b 55%, #0b6ea8 100%);
    color: #fff; padding: 26mm 16mm; margin: -14mm -13mm 10mm;
}
.cover h1 { font-size: 34px; line-height: 1.1; letter-spacing: -.5px; margin-bottom: 10px; }
.cover h1 span { color: #6fe3ff; }
.cover .kicker { font-size: 11px; letter-spacing: 3px; text-transform: uppercase; color: #8fc7f0; margin-bottom: 16px; }
.cover .lede { font-size: 13px; color: #d6e8f8; max-width: 128mm; }
.cover .stats { display: flex; gap: 26px; margin-top: 26px; }
.cover .stat .n { font-size: 30px; font-weight: 700; color: #6fe3ff; }
.cover .stat .l { font-size: 10px; text-transform: uppercase; letter-spacing: 1.4px; color: #a9cbe8; }
.cover .meta { margin-top: 24px; font-size: 10px; color: #9fc4e4; }

/* ---------- index ---------- */
.section-title {
    font-size: 17px; color: var(--deep); font-weight: 700;
    padding-bottom: 6px; border-bottom: 2px solid var(--primary);
    margin: 0 0 12px;
}
.index-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 18px; }
.index-row {
    display: flex; justify-content: space-between; align-items: baseline;
    border-bottom: 1px dotted var(--line); padding: 3px 0; font-size: 10.5px;
}
.index-row .nm { color: var(--ink); }
.index-row .ct { color: var(--muted); font-variant-numeric: tabular-nums; }

/* ---------- category header ---------- */
.cat-header {
    display: flex; align-items: center; gap: 12px;
    background: linear-gradient(100deg, var(--deep), #17457e);
    color: #fff; padding: 12px 16px; border-radius: 8px; margin: 0 0 12px;
}
.cat-header .num {
    font-size: 20px; font-weight: 700; color: #6fe3ff;
    min-width: 34px;
}
.cat-header h2 { font-size: 16px; margin: 0; }
.cat-header p { font-size: 10px; color: #b9d6ee; margin: 2px 0 0; }
.cat-header .count {
    margin-left: auto; background: rgba(255,255,255,.14);
    padding: 5px 12px; border-radius: 20px; font-size: 10px; white-space: nowrap;
}

/* ---------- module card ---------- */
.module-card {
    border: 1px solid var(--line); border-radius: 8px;
    padding: 12px; margin-bottom: 12px; background: #fff;
    display: grid; grid-template-columns: 1fr 1fr; gap: 14px;
}
.module-card .shot { }
.module-card .shot .frame {
    border: 1px solid #c9d6e4; border-radius: 6px; overflow: hidden; background: #0f1b2d;
}
.module-card .shot .cap {
    font-size: 8.5px; color: var(--muted); margin-top: 4px; font-style: italic;
}
.module-card h3 { font-size: 13px; color: var(--deep); margin-bottom: 3px; }
.module-card .sub { font-size: 9.5px; color: var(--muted); margin-bottom: 8px; }
.module-card ul { list-style: none; }
.module-card li {
    font-size: 9.5px; padding-left: 13px; position: relative;
    margin-bottom: 4px; color: #34435a;
}
.module-card li::before {
    content: ''; position: absolute; left: 0; top: 5px;
    width: 5px; height: 5px; border-radius: 50%; background: var(--primary);
}
.badges { display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px; }
.badge {
    font-size: 8.5px; padding: 3px 8px; border-radius: 12px; font-weight: 600;
}
.b-ver { background: #eef3f9; color: #43536b; }
.b-standard { background: #e7f5ef; color: #0f7a52; }
.b-professional { background: #e8f0fe; color: #14509c; }
.b-enterprise { background: #f3ebfd; color: #5b2ea8; }

.footnote {
    margin-top: 10px; padding: 8px 10px; background: #f6f8fb;
    border-left: 3px solid var(--accent); font-size: 9px; color: var(--muted);
}
.page-foot {
    margin-top: 14px; padding-top: 6px; border-top: 1px solid var(--line);
    font-size: 8.5px; color: var(--muted); display: flex; justify-content: space-between;
}
</style>
</head>
<body>

<div class="print-bar">
    <div class="brand">EnPharChem<span> · Module Catalogue</span></div>
    <div class="actions">
        <a href="/enpharchem/control-panel/marketing" class="btn btn-back">← Control Panel</a>
        <button class="btn btn-print" onclick="window.print()">Save as PDF / Print</button>
    </div>
</div>

<div class="sheet">

    <!-- ============ COVER ============ -->
    <div class="cover">
        <div class="kicker">Product Catalogue</div>
        <h1>Every module,<br><span>in one catalogue.</span></h1>
        <p class="lede">
            The complete EnPharChem engineering suite — process simulation, exchanger design,
            subsurface science, advanced process control, manufacturing execution, supply chain,
            asset performance and digital grid management — documented module by module.
        </p>
        <div class="stats">
            <div class="stat">
                <div class="n"><?= count($modules) ?></div>
                <div class="l">Modules</div>
            </div>
            <div class="stat">
                <div class="n"><?= count($grouped) ?></div>
                <div class="l">Categories</div>
            </div>
            <div class="stat">
                <div class="n">3</div>
                <div class="l">Licence tiers</div>
            </div>
        </div>
        <div class="meta">
            <?= htmlspecialchars(COMPANY_NAME) ?> &middot; Platform v<?= htmlspecialchars(APP_VERSION) ?>
            &middot; Generated <?= htmlspecialchars($generated) ?>
        </div>
    </div>

    <!-- ============ INDEX ============ -->
    <h2 class="section-title">Catalogue index</h2>
    <div class="index-grid">
        <?php $n = 0; foreach ($grouped as $slug => $mods): $n++; ?>
        <div class="index-row">
            <span class="nm"><?= str_pad($n, 2, '0', STR_PAD_LEFT) ?> &nbsp; <?= htmlspecialchars($catMeta[$slug]['name'] ?? $mods[0]['category_name']) ?></span>
            <span class="ct"><?= count($mods) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="footnote">
        <strong>About the interface views.</strong> Each module is shown with a representative
        illustration of its working view, drawn to scale as vector artwork so it stays legible in
        print. The figures depict the layout and readouts a user works with; the values shown are
        illustrative sample data, not output from a specific production run.
    </div>

    <div class="page-foot">
        <span><?= htmlspecialchars(COMPANY_NAME) ?> — Module Catalogue</span>
        <span><?= htmlspecialchars($generated) ?></span>
    </div>

    <!-- ============ CATEGORY SECTIONS ============ -->
    <?php $n = 0; foreach ($grouped as $slug => $mods): $n++; ?>
    <div class="page-break"></div>

    <div class="cat-header">
        <div class="num"><?= str_pad($n, 2, '0', STR_PAD_LEFT) ?></div>
        <div>
            <h2><?= htmlspecialchars($catMeta[$slug]['name'] ?? $mods[0]['category_name']) ?></h2>
            <?php if (!empty($catMeta[$slug]['description'])): ?>
            <p><?= htmlspecialchars($catMeta[$slug]['description']) ?></p>
            <?php endif; ?>
        </div>
        <div class="count"><?= count($mods) ?> module<?= count($mods) === 1 ? '' : 's' ?></div>
    </div>

    <?php foreach ($mods as $mod):
        $feats = $moduleFeatures[$mod['id']] ?? [];
        $tier  = $mod['license_required'] ?? 'standard';
    ?>
    <div class="module-card">
        <div class="shot">
            <div class="frame">
                <?= ModuleMockup::render($mod, $slug, $catMeta[$slug]['name'] ?? $mod['category_name']) ?>
            </div>
            <div class="cap">Representative working view — <?= htmlspecialchars($mod['name']) ?></div>
        </div>
        <div class="info">
            <h3><?= htmlspecialchars($mod['name']) ?></h3>
            <div class="sub"><?= htmlspecialchars($catMeta[$slug]['name'] ?? $mod['category_name']) ?></div>
            <ul>
                <?php foreach (array_slice($feats, 0, 6) as $f): ?>
                <li><?= htmlspecialchars($f) ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="badges">
                <span class="badge b-ver">v<?= htmlspecialchars($mod['version'] ?? '1.0') ?></span>
                <span class="badge b-<?= htmlspecialchars($tier) ?>"><?= htmlspecialchars($tierLabel[$tier] ?? ucfirst($tier)) ?> licence</span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="page-foot">
        <span><?= htmlspecialchars($catMeta[$slug]['name'] ?? '') ?></span>
        <span><?= htmlspecialchars(COMPANY_NAME) ?> · <?= htmlspecialchars($generated) ?></span>
    </div>
    <?php endforeach; ?>

</div>
</body>
</html>
