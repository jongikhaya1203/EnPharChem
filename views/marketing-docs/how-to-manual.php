<?php
/**
 * EnPharChem - How-To Manual (print-ready A4)
 *
 * Documents the tasks performed in every module, each with a numbered
 * walkthrough, required inputs, expected outputs and a How Helper hint.
 *
 * Expects: $categories, $modules, $byCategory, $moduleTasks
 */
$categories  = $categories ?? [];
$modules     = $modules ?? [];
$moduleTasks = $moduleTasks ?? [];

$grouped = [];
foreach ($modules as $m) {
    $grouped[$m['category_slug']][] = $m;
}
$catMeta = [];
foreach ($categories as $c) {
    $catMeta[$c['slug']] = $c;
}

$totalTasks = 0;
foreach ($moduleTasks as $ts) { $totalTasks += count($ts); }
$generated = date('j F Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EnPharChem How-To Manual</title>
<style>
@page { size: A4; margin: 12mm; }
@media print {
    .print-bar { display: none !important; }
    .page-break { page-break-before: always; }
    .task, .mod-header, .cat-header { page-break-inside: avoid; }
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
    --helper: #7a5b00;
}
body {
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    color: var(--ink); background: #eef2f7;
    font-size: 11px; line-height: 1.55;
    -webkit-print-color-adjust: exact; print-color-adjust: exact;
}
.print-bar {
    position: sticky; top: 0;
    background: linear-gradient(135deg, #0f1117, #1a1d23);
    padding: 12px 26px; display: flex; align-items: center; justify-content: space-between;
    z-index: 999;
}
.print-bar .brand { color: #fff; font-weight: 700; font-size: 15px; }
.print-bar .brand span { color: var(--accent); }
.print-bar .actions { display: flex; gap: 10px; }
.btn { border: 0; border-radius: 6px; padding: 8px 16px; font-size: 12px; font-weight: 600;
       cursor: pointer; text-decoration: none; display: inline-block; }
.btn-print { background: var(--primary); color: #fff; }
.btn-back { background: #2b3038; color: #cfd6de; }

.sheet { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 14mm 13mm; background: #fff; }
@media print { .sheet { width: auto; min-height: 0; margin: 0; padding: 0; } }

.cover {
    background: linear-gradient(150deg, var(--deep) 0%, #14324f 55%, #0e6b6b 100%);
    color: #fff; padding: 26mm 16mm; margin: -14mm -13mm 10mm;
}
.cover .kicker { font-size: 11px; letter-spacing: 3px; text-transform: uppercase; color: #8fd7d0; margin-bottom: 16px; }
.cover h1 { font-size: 34px; line-height: 1.1; margin-bottom: 10px; }
.cover h1 span { color: #6fe3d5; }
.cover .lede { font-size: 13px; color: #d3e9e6; max-width: 128mm; }
.cover .stats { display: flex; gap: 26px; margin-top: 26px; }
.cover .stat .n { font-size: 30px; font-weight: 700; color: #6fe3d5; }
.cover .stat .l { font-size: 10px; text-transform: uppercase; letter-spacing: 1.4px; color: #a5cfca; }
.cover .meta { margin-top: 24px; font-size: 10px; color: #9dc6c1; }

.section-title {
    font-size: 17px; color: var(--deep); font-weight: 700;
    padding-bottom: 6px; border-bottom: 2px solid var(--primary); margin: 0 0 12px;
}
.intro-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.intro-card { border: 1px solid var(--line); border-radius: 8px; padding: 11px 13px; }
.intro-card h4 { font-size: 11.5px; color: var(--deep); margin-bottom: 5px; }
.intro-card p, .intro-card li { font-size: 9.5px; color: #40506a; }
.intro-card ol { padding-left: 15px; }

.helper-box {
    background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b;
    border-radius: 6px; padding: 9px 12px; margin: 10px 0;
}
.helper-box .lbl {
    font-size: 8.5px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase;
    color: var(--helper); display: block; margin-bottom: 3px;
}
.helper-box p { font-size: 9.5px; color: #5d4708; }

.cat-header {
    display: flex; align-items: center; gap: 12px;
    background: linear-gradient(100deg, var(--deep), #14506b);
    color: #fff; padding: 12px 16px; border-radius: 8px; margin: 0 0 12px;
}
.cat-header .num { font-size: 20px; font-weight: 700; color: #6fe3d5; min-width: 34px; }
.cat-header h2 { font-size: 16px; }
.cat-header .count { margin-left: auto; background: rgba(255,255,255,.14);
    padding: 5px 12px; border-radius: 20px; font-size: 10px; white-space: nowrap; }

.mod-header {
    border-left: 4px solid var(--primary); background: #f5f8fc;
    padding: 8px 12px; border-radius: 0 6px 6px 0; margin: 14px 0 10px;
}
.mod-header h3 { font-size: 13.5px; color: var(--deep); }
.mod-header .desc { font-size: 9.5px; color: var(--muted); margin-top: 2px; }
.mod-header .tier {
    font-size: 8.5px; font-weight: 600; color: #14509c;
    background: #e8f0fe; border-radius: 10px; padding: 2px 8px; margin-left: 6px;
}

.task { border: 1px solid var(--line); border-radius: 8px; padding: 11px 13px; margin-bottom: 10px; }
.task .hd { display: flex; align-items: baseline; gap: 8px; margin-bottom: 5px; }
.task .tnum {
    background: var(--primary); color: #fff; font-size: 9px; font-weight: 700;
    border-radius: 4px; padding: 2px 7px; white-space: nowrap;
}
.task h4 { font-size: 11.5px; color: var(--deep); }
.task .obj { font-size: 9.5px; color: #40506a; margin-bottom: 7px; }
.io { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 8px; }
.io div { background: #f6f8fb; border-radius: 5px; padding: 6px 9px; }
.io .k { font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); }
.io .v { font-size: 9.5px; color: #2b3a52; }
.steps { counter-reset: s; list-style: none; }
.steps li {
    counter-increment: s; position: relative; padding-left: 22px;
    font-size: 9.5px; color: #33425c; margin-bottom: 5px;
}
.steps li::before {
    content: counter(s); position: absolute; left: 0; top: 0;
    width: 15px; height: 15px; border-radius: 50%;
    background: #e8f0fe; color: #14509c;
    font-size: 8.5px; font-weight: 700; text-align: center; line-height: 15px;
}
.steps code { background: #eef2f7; border-radius: 3px; padding: 1px 4px; font-family: Consolas, monospace; font-size: 9px; }
.page-foot {
    margin-top: 14px; padding-top: 6px; border-top: 1px solid var(--line);
    font-size: 8.5px; color: var(--muted); display: flex; justify-content: space-between;
}
</style>
</head>
<body>

<div class="print-bar">
    <div class="brand">EnPharChem<span> · How-To Manual</span></div>
    <div class="actions">
        <a href="/enpharchem/control-panel/marketing" class="btn btn-back">← Control Panel</a>
        <button class="btn btn-print" onclick="window.print()">Save as PDF / Print</button>
    </div>
</div>

<div class="sheet">

    <!-- ============ COVER ============ -->
    <div class="cover">
        <div class="kicker">User Documentation</div>
        <h1>How to run<br><span>every module.</span></h1>
        <p class="lede">
            A task-by-task operating manual for the EnPharChem platform. For each module it sets out
            what you can do, the inputs each task needs, the steps to follow in the module workspace,
            and what the result should tell you.
        </p>
        <div class="stats">
            <div class="stat"><div class="n"><?= count($modules) ?></div><div class="l">Modules</div></div>
            <div class="stat"><div class="n"><?= $totalTasks ?></div><div class="l">Documented tasks</div></div>
            <div class="stat"><div class="n"><?= count($grouped) ?></div><div class="l">Categories</div></div>
        </div>
        <div class="meta">
            <?= htmlspecialchars(COMPANY_NAME) ?> &middot; Platform v<?= htmlspecialchars(APP_VERSION) ?>
            &middot; Generated <?= htmlspecialchars($generated) ?>
        </div>
    </div>

    <!-- ============ HOW TO USE ============ -->
    <h2 class="section-title">How to use this manual</h2>
    <div class="intro-grid">
        <div class="intro-card">
            <h4>Every task follows the same shape</h4>
            <ol>
                <li><strong>Objective</strong> — what the task achieves.</li>
                <li><strong>Inputs</strong> — the data to have ready first.</li>
                <li><strong>Outputs</strong> — what you get back.</li>
                <li><strong>Steps</strong> — the numbered sequence to follow on screen.</li>
                <li><strong>How Helper</strong> — the practical hint that saves the rework.</li>
            </ol>
        </div>
        <div class="intro-card">
            <h4>The module workspace</h4>
            <p>
                Every module opens on the same five-tab workspace, so once you have run one module you
                can run any of them: <strong>Overview</strong> for scope and version,
                <strong>Simulation</strong> to create and run a case, <strong>Configuration</strong> for
                units, tolerance and iteration limits, <strong>Results</strong> for the output charts,
                and <strong>Documentation</strong> for the method notes.
            </p>
        </div>
    </div>

    <div class="helper-box">
        <span class="lbl">How Helper</span>
        <p>
            Before starting any task, create the <strong>Project</strong> that will own the work
            (Projects → New Project). Every simulation is attached to a project, and runs created
            without one are difficult to find and impossible to compare later.
        </p>
    </div>

    <div class="helper-box">
        <span class="lbl">How Helper · Licensing</span>
        <p>
            Modules are governed per licence tier. If a module reports that a licence is required,
            request one through the Licensing Portal — an administrator grants it against your
            account, after which the module runs without further action.
        </p>
    </div>

    <div class="page-foot">
        <span><?= htmlspecialchars(COMPANY_NAME) ?> — How-To Manual</span>
        <span><?= htmlspecialchars($generated) ?></span>
    </div>

    <!-- ============ CATEGORY / MODULE SECTIONS ============ -->
    <?php $n = 0; foreach ($grouped as $slug => $mods): $n++; ?>
    <div class="page-break"></div>

    <div class="cat-header">
        <div class="num"><?= str_pad($n, 2, '0', STR_PAD_LEFT) ?></div>
        <div><h2><?= htmlspecialchars($catMeta[$slug]['name'] ?? $mods[0]['category_name']) ?></h2></div>
        <div class="count"><?= count($mods) ?> module<?= count($mods) === 1 ? '' : 's' ?></div>
    </div>

    <?php foreach ($mods as $mod):
        $tasks = $moduleTasks[$mod['id']] ?? [];
        $tier  = ucfirst($mod['license_required'] ?? 'standard');
    ?>
    <div class="mod-header">
        <h3><?= htmlspecialchars($mod['name']) ?><span class="tier"><?= htmlspecialchars($tier) ?></span></h3>
        <?php if (!empty($mod['description'])): ?>
        <div class="desc"><?= htmlspecialchars($mod['description']) ?></div>
        <?php endif; ?>
    </div>

    <?php foreach ($tasks as $i => $t): ?>
    <div class="task">
        <div class="hd">
            <span class="tnum">TASK <?= $i + 1 ?></span>
            <h4><?= htmlspecialchars($t['title']) ?></h4>
        </div>
        <p class="obj"><?= htmlspecialchars($t['objective']) ?></p>
        <div class="io">
            <div><div class="k">Inputs required</div><div class="v"><?= htmlspecialchars($t['inputs']) ?></div></div>
            <div><div class="k">Expected output</div><div class="v"><?= htmlspecialchars($t['outputs']) ?></div></div>
        </div>
        <ol class="steps">
            <?php foreach ($t['steps'] as $step): ?>
            <li><?= $step /* trusted: authored in ModuleDocContent, contains <strong>/<code> markup */ ?></li>
            <?php endforeach; ?>
        </ol>
        <?php if (!empty($t['tip'])): ?>
        <div class="helper-box">
            <span class="lbl">How Helper</span>
            <p><?= htmlspecialchars($t['tip']) ?></p>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endforeach; ?>

    <div class="page-foot">
        <span><?= htmlspecialchars($catMeta[$slug]['name'] ?? '') ?></span>
        <span><?= htmlspecialchars(COMPANY_NAME) ?> · <?= htmlspecialchars($generated) ?></span>
    </div>
    <?php endforeach; ?>

</div>
</body>
</html>
