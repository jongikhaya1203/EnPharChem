<?php
/**
 * EnPharChem - Business Process Map (print-ready A4 HTML edition)
 *
 * Screen-readable companion to the generated PDF. Maps each business process to
 * its use cases, the job titles/platform roles that run it, and a worked example.
 *
 * Expects: $processes, $roleLegend
 */
$processes  = $processes ?? [];
$roleLegend = $roleLegend ?? [];

$roleColour = [
    'superuser' => ['#f3ebfd', '#5b2ea8'],
    'admin'     => ['#e8f0fe', '#14509c'],
    'engineer'  => ['#e7f5ef', '#0f7a52'],
    'operator'  => ['#fff4e0', '#9a5c08'],
    'viewer'    => ['#eef3f9', '#43536b'],
];
$generated = date('j F Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EnPharChem Business Process Map</title>
<style>
@page { size: A4; margin: 12mm; }
@media print {
    .print-bar { display: none !important; }
    .page-break { page-break-before: always; }
    .process, .legend-row, .cover { page-break-inside: avoid; }
    body { background: #fff; }
}
* { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --primary:#0d6efd; --accent:#0dcaf0; --ink:#16202e; --body:#33425c;
    --muted:#64748b; --line:#dbe3ec; --deep:#0a1628; --soft:#f6f8fb;
}
body { font-family:'Segoe UI',system-ui,-apple-system,sans-serif; color:var(--ink);
    background:#eef2f7; font-size:11px; line-height:1.55;
    -webkit-print-color-adjust:exact; print-color-adjust:exact; }
.print-bar { position:sticky; top:0; background:linear-gradient(135deg,#0f1117,#1a1d23);
    padding:12px 26px; display:flex; align-items:center; justify-content:space-between; z-index:999; }
.print-bar .brand { color:#fff; font-weight:700; font-size:15px; }
.print-bar .brand span { color:var(--accent); }
.print-bar .actions { display:flex; gap:10px; }
.btn { border:0; border-radius:6px; padding:8px 16px; font-size:12px; font-weight:600;
    cursor:pointer; text-decoration:none; display:inline-block; }
.btn-print { background:var(--primary); color:#fff; }
.btn-back { background:#2b3038; color:#cfd6de; }
.sheet { width:210mm; min-height:297mm; margin:0 auto; padding:14mm 13mm; background:#fff; }
@media print { .sheet { width:auto; min-height:0; margin:0; padding:0; } }

.cover { background:linear-gradient(150deg,var(--deep) 0%,#173a6b 55%,#0b6ea8 100%);
    color:#fff; padding:26mm 16mm; margin:-14mm -13mm 10mm; }
.cover .kicker { font-size:11px; letter-spacing:3px; text-transform:uppercase; color:#8fc7f0; margin-bottom:16px; }
.cover h1 { font-size:32px; line-height:1.12; margin-bottom:10px; }
.cover h1 span { color:#6fe3ff; }
.cover .lede { font-size:13px; color:#d6e8f8; max-width:130mm; }
.cover .stats { display:flex; gap:30px; margin-top:26px; }
.cover .stat .n { font-size:30px; font-weight:700; color:#6fe3ff; }
.cover .stat .l { font-size:10px; text-transform:uppercase; letter-spacing:1.4px; color:#a9cbe8; }
.cover .meta { margin-top:22px; font-size:10px; color:#9fc4e4; }

.section-title { font-size:16px; color:var(--deep); font-weight:700;
    padding-bottom:6px; border-bottom:2px solid var(--primary); margin:0 0 12px; }
.role-badge { display:inline-block; font-size:9px; font-weight:700; padding:2px 9px;
    border-radius:11px; white-space:nowrap; }

.legend-row { display:flex; gap:12px; align-items:baseline; padding:5px 0; border-bottom:1px dotted var(--line); }
.legend-row .lb { min-width:78px; }
.legend-row .ls { font-size:10px; color:var(--body); }

table.summary { width:100%; border-collapse:collapse; margin-top:4px; }
table.summary th { background:var(--deep); color:#fff; font-size:9.5px; text-align:left; padding:7px 8px; }
table.summary td { font-size:9.5px; padding:6px 8px; border-bottom:1px solid var(--line); color:var(--body); vertical-align:top; }
table.summary tr:nth-child(even) td { background:var(--soft); }

.proc-header { display:flex; align-items:center; gap:12px; background:linear-gradient(100deg,var(--deep),#17457e);
    color:#fff; padding:11px 16px; border-radius:8px; margin:0 0 10px; }
.proc-header .num { font-size:19px; font-weight:700; color:#6fe3ff; min-width:32px; }
.proc-header h2 { font-size:15px; margin:0; }
.proc-summary { font-size:10.5px; color:var(--body); margin-bottom:8px; }
.apparea { font-size:9px; color:var(--muted); margin-bottom:10px; }
.apparea .lbl { font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--muted); }

.cols { display:flex; gap:18px; margin-bottom:10px; }
.col { flex:1; }
.col h3 { font-size:11px; color:var(--primary); margin-bottom:6px; }
.col ul { list-style:none; }
.col li { font-size:9.5px; color:var(--body); padding-left:12px; position:relative; margin-bottom:4px; }
.col li::before { content:''; position:absolute; left:0; top:5px; width:5px; height:5px; border-radius:50%; background:var(--primary); }
.actor { display:flex; justify-content:space-between; align-items:baseline; gap:8px; margin-bottom:6px; }
.actor .t { font-size:9.5px; color:var(--body); }

.example { background:var(--soft); border-left:4px solid var(--accent); border-radius:0 6px 6px 0; padding:10px 13px; }
.example .lbl { font-size:8.5px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--muted); }
.example h4 { font-size:11px; color:var(--deep); margin:3px 0 8px; }
.example ol { counter-reset:s; list-style:none; }
.example li { counter-increment:s; position:relative; padding-left:22px; font-size:9.5px; color:var(--body); margin-bottom:5px; }
.example li::before { content:counter(s); position:absolute; left:0; top:0; width:15px; height:15px; border-radius:50%;
    background:#e8f0fe; color:#14509c; font-size:8.5px; font-weight:700; text-align:center; line-height:15px; }
.example .outcome { margin-top:8px; font-size:9.5px; font-weight:600; color:#0f7a52; }
.page-foot { margin-top:14px; padding-top:6px; border-top:1px solid var(--line);
    font-size:8.5px; color:var(--muted); display:flex; justify-content:space-between; }
</style>
</head>
<body>

<div class="print-bar">
    <div class="brand">EnPharChem<span> · Business Process Map</span></div>
    <div class="actions">
        <a href="/enpharchem/control-panel/marketing" class="btn btn-back">← Control Panel</a>
        <button class="btn btn-print" onclick="window.print()">Save as PDF / Print</button>
    </div>
</div>

<div class="sheet">

    <div class="cover">
        <div class="kicker">Business Process Map</div>
        <h1>Processes, use cases<br><span>and the people who run them.</span></h1>
        <p class="lede">
            How the EnPharChem platform supports the business end to end: each process mapped to the
            use cases it serves, the job titles and platform roles that carry it out, and a worked example.
        </p>
        <div class="stats">
            <div class="stat"><div class="n"><?= count($processes) ?></div><div class="l">Business processes</div></div>
            <div class="stat"><div class="n"><?= count($roleLegend) ?></div><div class="l">Platform roles</div></div>
        </div>
        <div class="meta"><?= htmlspecialchars(COMPANY_NAME) ?> &middot; Platform v<?= htmlspecialchars(APP_VERSION) ?> &middot; Generated <?= htmlspecialchars($generated) ?></div>
    </div>

    <h2 class="section-title">How to read this map</h2>
    <p class="proc-summary">
        Each business process lists the use cases it serves, the actors that carry it out &mdash; by job
        title and by the platform role that grants their access &mdash; and a worked example. Job titles are
        the industry roles that map onto the platform's five access roles:
    </p>
    <?php foreach ($roleLegend as $l): $c = $roleColour[$l['role']] ?? ['#eef3f9', '#43536b']; ?>
    <div class="legend-row">
        <span class="lb"><span class="role-badge" style="background:<?= $c[0] ?>;color:<?= $c[1] ?>;"><?= htmlspecialchars($l['role']) ?></span></span>
        <span class="ls"><?= htmlspecialchars($l['scope']) ?></span>
    </div>
    <?php endforeach; ?>

    <div class="page-break"></div>
    <h2 class="section-title">Process summary</h2>
    <table class="summary">
        <thead><tr><th style="width:6%;">#</th><th style="width:28%;">Business process</th><th style="width:34%;">Primary job titles</th><th style="width:32%;">Headline use case</th></tr></thead>
        <tbody>
        <?php foreach ($processes as $i => $p):
            $titles = implode(', ', array_slice(array_map(fn($a) => $a['title'], $p['actors']), 0, 3)); ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                <td><?= htmlspecialchars($titles) ?></td>
                <td><?= htmlspecialchars($p['useCases'][0]) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="page-foot"><span><?= htmlspecialchars(COMPANY_NAME) ?> — Business Process Map</span><span><?= htmlspecialchars($generated) ?></span></div>

    <?php foreach ($processes as $i => $p): ?>
    <div class="page-break"></div>
    <div class="process">
        <div class="proc-header">
            <div class="num"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></div>
            <h2><?= htmlspecialchars($p['name']) ?></h2>
        </div>
        <p class="proc-summary"><?= htmlspecialchars($p['summary']) ?></p>
        <div class="apparea"><span class="lbl">Where in the platform:</span> <?= htmlspecialchars($p['appArea']) ?></div>

        <div class="cols">
            <div class="col">
                <h3>Use cases</h3>
                <ul>
                    <?php foreach ($p['useCases'] as $uc): ?><li><?= htmlspecialchars($uc) ?></li><?php endforeach; ?>
                </ul>
            </div>
            <div class="col">
                <h3>Actors &amp; roles</h3>
                <?php foreach ($p['actors'] as $a): $c = $roleColour[$a['role']] ?? ['#eef3f9', '#43536b']; ?>
                <div class="actor">
                    <span class="t"><?= htmlspecialchars($a['title']) ?></span>
                    <span class="role-badge" style="background:<?= $c[0] ?>;color:<?= $c[1] ?>;"><?= htmlspecialchars($a['role']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="example">
            <span class="lbl">Worked example</span>
            <h4><?= htmlspecialchars($p['example']['title']) ?></h4>
            <ol>
                <?php foreach ($p['example']['steps'] as $s): ?><li><?= htmlspecialchars($s) ?></li><?php endforeach; ?>
            </ol>
            <div class="outcome">Outcome: <?= htmlspecialchars($p['example']['outcome']) ?></div>
        </div>

        <div class="page-foot"><span><?= htmlspecialchars($p['name']) ?></span><span><?= htmlspecialchars(COMPANY_NAME) ?> · <?= htmlspecialchars($generated) ?></span></div>
    </div>
    <?php endforeach; ?>

</div>
</body>
</html>
