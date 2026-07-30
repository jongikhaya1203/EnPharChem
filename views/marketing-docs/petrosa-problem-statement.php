<?php
/**
 * EnPharChem - PetroSA GTL Problem Statement (print-ready A4 HTML edition)
 *
 * Screen-readable companion to the generated PDF. One problem statement per
 * module area, each with worked use cases plus how to perform and analyse them.
 *
 * Expects: $context, $corePstatements, $modulesContent, $disclaimer
 */
$context    = $context ?? [];
$core       = $corePstatements ?? [];
$modules    = $modulesContent ?? [];
$disclaimer = $disclaimer ?? '';
$chain      = $context['chain'] ?? [];

$useCaseCount = 0;
foreach ($modules as $m) { $useCaseCount += count($m['useCases']); }
$generated = date('j F Y');
$e = fn($s) => htmlspecialchars((string) $s);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PetroSA GTL Refinery Problem Statement &amp; Use Case Handbook</title>
<style>
@page { size: A4; margin: 12mm; }
@media print {
    .print-bar { display: none !important; }
    .page-break { page-break-before: always; }
    .modblock, .usecase, .cover, .chain-row, .disclaimer { page-break-inside: avoid; }
    body { background: #fff; }
}
* { box-sizing: border-box; margin: 0; padding: 0; }
:root {
    --primary:#0d6efd; --accent:#0dcaf0; --ink:#16202e; --body:#33425c;
    --muted:#64748b; --line:#dbe3ec; --deep:#0a1628; --soft:#f6f8fb;
    --good:#0f7a52; --warn:#9a5c08; --warnbg:#fff8e8;
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
.btn-pdf { background:#20c997; color:#06281d; }
.btn-back { background:#2b3038; color:#cfd6de; }
.sheet { width:210mm; min-height:297mm; margin:0 auto; padding:14mm 13mm; background:#fff; }
@media print { .sheet { width:auto; min-height:0; margin:0; padding:0; } }

.cover { background:linear-gradient(150deg,var(--deep) 0%,#173a6b 55%,#0b6ea8 100%);
    color:#fff; padding:24mm 16mm; margin:-14mm -13mm 10mm; }
.cover .kicker { font-size:10px; letter-spacing:2.4px; text-transform:uppercase;
    color:#8fc7f0; margin-bottom:16px; }
.cover h1 { font-size:31px; line-height:1.14; margin-bottom:10px; }
.cover h1 span { color:#6fe3ff; }
.cover .lede { font-size:13px; color:#d6e8f8; max-width:132mm; }
.cover .stats { display:flex; gap:28px; margin-top:26px; flex-wrap:wrap; }
.cover .stat .n { font-size:29px; font-weight:700; color:#6fe3ff; }
.cover .stat .l { font-size:9px; text-transform:uppercase; letter-spacing:1.3px; color:#a9cbe8; }
.cover .meta { margin-top:22px; font-size:10px; color:#9fc4e4; }

.section-title { font-size:16px; color:var(--deep); font-weight:700;
    padding-bottom:6px; border-bottom:2px solid var(--primary); margin:22px 0 12px; }
p.para { color:var(--body); margin-bottom:9px; }

.disclaimer { background:var(--warnbg); border-left:3px solid var(--warn);
    border-radius:5px; padding:12px 15px; margin:10px 0 4px; }
.disclaimer .lbl { font-size:9px; font-weight:700; letter-spacing:1.2px;
    text-transform:uppercase; color:var(--warn); margin-bottom:5px; }
.disclaimer p { color:#5a3e0c; font-size:10.5px; }

table { width:100%; border-collapse:collapse; margin:8px 0 14px; }
th { background:var(--deep); color:#fff; font-size:9.5px; text-align:left;
    padding:7px 8px; font-weight:700; }
td { padding:6px 8px; border-bottom:1px solid var(--line); color:var(--body);
    font-size:10px; vertical-align:top; }
tbody tr:nth-child(even) { background:#f8fafc; }

.chain-row { display:flex; gap:12px; align-items:baseline; padding:5px 0;
    border-bottom:1px dotted var(--line); }
.chain-row .cs { min-width:52mm; font-size:10px; font-weight:700; color:#14509c;
    background:#e8f0fe; border-radius:4px; padding:3px 8px; }
.chain-row .cd { font-size:10px; color:var(--body); }

.modblock { margin-top:16px; }
.modhead { background:var(--deep); color:#fff; border-radius:5px; padding:10px 14px;
    display:flex; align-items:baseline; gap:14px; }
.modhead .n { font-size:17px; font-weight:700; color:#6fe3ff; }
.modhead .t { font-size:13px; font-weight:700; }
.modhead .c { font-size:9.5px; color:#a9cbe8; margin-top:2px; }
.lbl { font-size:9px; font-weight:700; letter-spacing:1.2px; text-transform:uppercase;
    color:var(--muted); margin:12px 0 5px; }
.modlist { font-size:9.5px; color:var(--ink); }

.usecase { margin-top:14px; }
.ucstrip { background:var(--soft); border-left:3px solid var(--accent);
    border-radius:4px; padding:8px 13px; }
.ucstrip .uck { font-size:8.5px; font-weight:700; letter-spacing:1.1px;
    text-transform:uppercase; color:var(--muted); }
.ucstrip .ucn { font-size:12px; font-weight:700; color:var(--deep); margin-top:2px; }
.ucobj { font-style:italic; color:var(--muted); font-size:10.5px; margin:7px 0 9px; }
.subhead { font-size:10.5px; font-weight:700; margin:10px 0 6px; }
.subhead.perform { color:var(--primary); }
.subhead.analyse { color:var(--good); }
ol.steps { margin:0 0 0 16px; padding:0; }
ol.steps li { font-size:10px; color:var(--body); margin-bottom:5px; padding-left:3px; }
ul.checks { margin:0 0 0 16px; padding:0; list-style:square; }
ul.checks li { font-size:10px; color:var(--body); margin-bottom:5px; padding-left:3px; }
ul.checks li::marker { color:var(--good); }
.kpi { background:#ebf6f0; border-radius:4px; padding:8px 12px; margin-top:10px;
    font-size:10px; font-weight:700; color:var(--good); }
.footnote { margin-top:26px; padding-top:10px; border-top:1px solid var(--line);
    font-size:9px; color:var(--muted); }
</style>
</head>
<body>

<div class="print-bar">
    <div class="brand">En<span>Phar</span>Chem &mdash; PetroSA GTL Problem Statement</div>
    <div class="actions">
        <a class="btn btn-pdf" href="/enpharchem/marketing/petrosa-problem-statement">Generated PDF</a>
        <button class="btn btn-print" onclick="window.print()">Print / Save as PDF</button>
        <a class="btn btn-back" href="/enpharchem/control-panel/marketing">Back</a>
    </div>
</div>

<div class="sheet">

    <div class="cover">
        <div class="kicker">Prepared for <?= $e(PetroSAProblemStatement::CLIENT) ?>
            &mdash; <?= $e(PetroSAProblemStatement::ASSET) ?></div>
        <h1>GTL Refinery Problem Statement &amp;<br><span>Use Case Handbook</span></h1>
        <p class="lede"><?= $e($context['headline'] ?? '') ?>. One problem statement per module area,
            each with worked use cases and the criteria to analyse them against.</p>
        <div class="stats">
            <div class="stat"><div class="n"><?= count($modules) ?></div><div class="l">Module areas</div></div>
            <div class="stat"><div class="n"><?= $useCaseCount ?></div><div class="l">Use cases</div></div>
            <div class="stat"><div class="n"><?= count($core) ?></div><div class="l">Core problems</div></div>
            <div class="stat"><div class="n"><?= count($chain) ?></div><div class="l">Plant areas</div></div>
        </div>
        <div class="meta"><?= $e(COMPANY_NAME) ?> &mdash; Platform v<?= $e(APP_VERSION) ?>
            &mdash; Generated <?= $e($generated) ?></div>
    </div>

    <h2 class="section-title">The situation</h2>
    <?php foreach (($context['paragraphs'] ?? []) as $p): ?>
        <p class="para"><?= $e($p) ?></p>
    <?php endforeach; ?>

    <h2 class="section-title">Basis and limitations of this document</h2>
    <div class="disclaimer">
        <div class="lbl">Read first</div>
        <p><?= $e($disclaimer) ?></p>
    </div>

    <h2 class="section-title">The GTL value chain</h2>
    <p class="para">Each module area below is anchored to one or more of these plant areas, so a reader
        can go straight to the section covering the part of the refinery they own.</p>
    <?php foreach ($chain as $i => $c): ?>
        <div class="chain-row">
            <div class="cs"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?>
                &nbsp; <?= $e($c['stage']) ?></div>
            <div class="cd"><?= $e($c['detail']) ?></div>
        </div>
    <?php endforeach; ?>

    <h2 class="section-title">The six core problems</h2>
    <table>
        <thead><tr><th style="width:24%">Core problem</th><th style="width:44%">Why it matters here</th>
            <th style="width:32%">Answered in</th></tr></thead>
        <tbody>
        <?php foreach ($core as $c): ?>
            <tr>
                <td><strong><?= $e($c['title']) ?></strong></td>
                <td><?= $e($c['detail']) ?></td>
                <td><?= $e($c['answered']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2 class="section-title">Module areas at a glance</h2>
    <table>
        <thead><tr><th style="width:6%">#</th><th style="width:28%">Module area</th>
            <th style="width:52%">Headline use case</th><th style="width:14%">Cases</th></tr></thead>
        <tbody>
        <?php foreach ($modules as $i => $m): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><strong><?= $e($m['category']) ?></strong></td>
                <td><?= $e($m['useCases'][0]['name'] ?? '') ?></td>
                <td><?= count($m['useCases']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php foreach ($modules as $i => $m): ?>
        <div class="modblock page-break">
            <div class="modhead">
                <div class="n"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></div>
                <div>
                    <div class="t"><?= $e($m['category']) ?></div>
                    <div class="c"><?= $e($m['chain']) ?></div>
                </div>
            </div>

            <div class="lbl">Problem statement</div>
            <p class="para"><?= $e($m['problem']) ?></p>

            <div class="lbl">Platform modules applied</div>
            <div class="modlist"><?= $e(implode('  ·  ', $m['platformModules'])) ?></div>

            <?php foreach ($m['useCases'] as $ui => $uc): ?>
                <div class="usecase">
                    <div class="ucstrip">
                        <div class="uck">Use case <?= ($i + 1) . '.' . ($ui + 1) ?></div>
                        <div class="ucn"><?= $e($uc['name']) ?></div>
                    </div>
                    <div class="ucobj">Objective: <?= $e($uc['objective']) ?></div>

                    <div class="subhead perform">How to perform it</div>
                    <ol class="steps">
                        <?php foreach ($uc['perform'] as $s): ?>
                            <li><?= $e($s) ?></li>
                        <?php endforeach; ?>
                    </ol>

                    <div class="subhead analyse">How to analyse the result</div>
                    <ul class="checks">
                        <?php foreach ($uc['analyse'] as $a): ?>
                            <li><?= $e($a) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="kpi">Success criterion: <?= $e($uc['kpi']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <div class="modblock page-break">
        <h2 class="section-title">How to use this handbook</h2>
        <ul class="checks">
            <li>Start from the problem statement, not the software. If the problem as written does not
                match the refinery's actual situation, correct it before any tool is opened &mdash; a
                well-executed study of the wrong problem is still waste.</li>
            <li>Replace every illustrative acceptance figure with a verified plant number, a design basis
                value or the applicable specification. The figures in this document exist to show the
                method and must not be quoted as targets.</li>
            <li>Do the analysis steps as written, including the ones that check whether the result is
                trustworthy. Most of the analysis guidance here is about establishing whether a result
                can be relied on, which is the step most often skipped.</li>
            <li>Feed each result into the next module area rather than filing it. The feed forecast bounds
                the plan; the calibrated models bound the optimiser; the reconciled data bounds
                everything. These use cases are not independent.</li>
            <li>Record what did not work. A use case that failed for a stated reason is more valuable to
                the next engineer than one that quietly produced a number nobody trusts.</li>
        </ul>

        <h2 class="section-title">Document control</h2>
        <table>
            <thead><tr><th style="width:28%">Field</th><th>Value</th></tr></thead>
            <tbody>
                <tr><td>Document</td><td><?= $e(PetroSAProblemStatement::DOCTITLE) ?></td></tr>
                <tr><td>Prepared for</td><td><?= $e(PetroSAProblemStatement::CLIENT) ?>
                    &mdash; <?= $e(PetroSAProblemStatement::ASSET) ?></td></tr>
                <tr><td>Prepared by</td><td><?= $e(COMPANY_NAME) ?></td></tr>
                <tr><td>Platform</td><td><?= $e(APP_NAME) ?> v<?= $e(APP_VERSION) ?></td></tr>
                <tr><td>Module areas</td><td><?= count($modules) ?> categories covering the full module set</td></tr>
                <tr><td>Use cases</td><td><?= $useCaseCount ?> worked cases with perform and analyse guidance</td></tr>
                <tr><td>Generated</td><td><?= $e($generated) ?></td></tr>
                <tr><td>Status</td><td>Vendor solution-fit assessment &mdash; figures illustrative,
                    not endorsed by the named company</td></tr>
            </tbody>
        </table>

        <div class="footnote"><?= $e($disclaimer) ?></div>
    </div>

</div>
</body>
</html>
