<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnPharChem vs AspenTech - Gartner Benchmark Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        @page {
            size: A4;
            margin: 15mm 12mm 15mm 12mm;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #1a1a2e;
            background: #ffffff;
            font-size: 11px;
            line-height: 1.5;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Print Button Bar */
        .print-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: linear-gradient(135deg, #0f1117, #1a1d23);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 9999;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .print-bar .brand { color: #fff; font-weight: 700; font-size: 16px; }
        .print-bar .brand span { color: #0dcaf0; }
        .print-bar .actions { display: flex; gap: 10px; }
        .print-bar .btn-pdf {
            padding: 8px 24px; border-radius: 8px; font-weight: 600;
            font-size: 13px; border: none; cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-download { background: #0d6efd; color: #fff; }
        .btn-download:hover { background: #0a58ca; }
        .btn-back { background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2) !important; }
        .btn-back:hover { background: rgba(255,255,255,0.2); }

        .report-body { padding-top: 70px; }

        /* Cover Header */
        .cover-header {
            background: linear-gradient(135deg, #0a1628 0%, #0d2847 50%, #0f3460 100%);
            color: #fff;
            padding: 40px;
            margin: -15mm -12mm 0 -12mm;
            margin-bottom: 30px;
            text-align: center;
        }
        .cover-logo {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            border-radius: 16px; display: inline-flex;
            align-items: center; justify-content: center;
            font-size: 24px; font-weight: 800; color: #fff;
            margin-bottom: 16px;
        }
        .cover-title { font-size: 28px; font-weight: 800; margin-bottom: 6px; letter-spacing: -0.5px; }
        .cover-subtitle { font-size: 14px; color: #a8c8e8; margin-bottom: 20px; }
        .cover-meta {
            display: inline-flex; gap: 24px;
            font-size: 11px; color: #7fb3d8;
            border-top: 1px solid rgba(255,255,255,0.15);
            padding-top: 16px; margin-top: 8px;
        }
        .cover-meta strong { color: #fff; }

        /* Section Styles */
        .section { margin-bottom: 28px; page-break-inside: avoid; }
        .section-title {
            font-size: 16px; font-weight: 700; color: #0d2847;
            margin-bottom: 14px; padding-bottom: 8px;
            border-bottom: 3px solid #0d6efd;
            display: flex; align-items: center; gap: 8px;
        }
        .section-title i { color: #0d6efd; font-size: 14px; }

        /* Summary Cards */
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 28px; }
        .summary-card {
            border: 1px solid #e2e8f0; border-radius: 10px;
            padding: 16px; text-align: center;
        }
        .summary-card .value { font-size: 28px; font-weight: 800; }
        .summary-card .label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .color-cyan { color: #0891b2; border-top: 3px solid #0891b2; }
        .color-green { color: #059669; border-top: 3px solid #059669; }
        .color-blue { color: #2563eb; border-top: 3px solid #2563eb; }
        .color-red { color: #dc2626; border-top: 3px solid #dc2626; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
        thead th {
            background: #f1f5f9; color: #475569; font-weight: 700;
            text-transform: uppercase; font-size: 9px; letter-spacing: 0.5px;
            padding: 8px 10px; border-bottom: 2px solid #cbd5e1;
            text-align: left;
        }
        tbody td {
            padding: 7px 10px; border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        tbody tr:last-child td { border-bottom: none; }
        .total-row { background: #f1f5f9; font-weight: 700; }

        /* Progress Bars */
        .bar-bg { height: 7px; background: #e2e8f0; border-radius: 4px; overflow: hidden; flex: 1; }
        .bar-fill { height: 100%; border-radius: 4px; }
        .bar-aspen { background: #dc2626; }
        .bar-ep { background: #0891b2; }
        .bar-green { background: #059669; }
        .bar-yellow { background: #d97706; }

        /* Scores */
        .score { font-weight: 700; min-width: 30px; display: inline-block; text-align: right; }
        .score-ep { color: #0891b2; }
        .score-at { color: #dc2626; }
        .delta-pos { color: #059669; font-weight: 700; }
        .delta-neg { color: #dc2626; font-weight: 700; }
        .delta-zero { color: #94a3b8; }

        /* Badge */
        .badge-sm {
            display: inline-block; padding: 2px 8px; border-radius: 4px;
            font-size: 9px; font-weight: 700; text-transform: uppercase;
        }
        .badge-high { background: #dbeafe; color: #2563eb; }
        .badge-medium { background: #f1f5f9; color: #64748b; }
        .badge-coverage { background: #d1fae5; color: #059669; }

        /* Star Rating */
        .stars { color: #f59e0b; font-size: 10px; }
        .stars-dim { color: #e2e8f0; }

        /* Two Column */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .col-box {
            border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px;
        }
        .col-box h5 { font-size: 13px; font-weight: 700; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 2px solid; }
        .col-box ul { padding-left: 16px; font-size: 10.5px; color: #475569; }
        .col-box li { margin-bottom: 4px; }

        /* Chart containers */
        .chart-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 28px; }
        .chart-box {
            border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px;
        }
        .chart-box h5 { font-size: 12px; font-weight: 700; color: #0d2847; margin-bottom: 12px; }

        /* Scorecard heatmap */
        .heatmap-cell {
            display: inline-block; width: 38px; height: 24px; line-height: 24px;
            text-align: center; border-radius: 4px; font-weight: 700; font-size: 10px;
        }
        .heat-high { background: #d1fae5; color: #059669; }
        .heat-mid { background: #dbeafe; color: #2563eb; }
        .heat-low { background: #fef3c7; color: #d97706; }
        .heat-poor { background: #fee2e2; color: #dc2626; }

        /* Footer */
        .report-footer {
            border-top: 2px solid #0d6efd; padding-top: 16px;
            margin-top: 30px; font-size: 9px; color: #94a3b8;
            text-align: center;
        }

        /* Print overrides */
        @media print {
            .print-bar { display: none !important; }
            .report-body { padding-top: 0; }
            .cover-header { margin: 0 0 30px 0; }
            .section { page-break-inside: avoid; }
            body { font-size: 10px; }
        }
    </style>
</head>
<body>

<!-- Print Action Bar (hidden in print) -->
<div class="print-bar">
    <div class="brand"><i class="fas fa-atom" style="margin-right:8px;color:#0dcaf0;"></i>En<span>Phar</span>Chem Benchmark Report</div>
    <div class="actions">
        <a href="/enpharchem/benchmark" class="btn-pdf btn-back"><i class="fas fa-arrow-left"></i> Back</a>
        <button onclick="window.print()" class="btn-pdf btn-download"><i class="fas fa-file-pdf"></i> Download PDF</button>
    </div>
</div>

<div class="report-body">

<!-- Cover Header -->
<div class="cover-header">
    <div class="cover-logo">EP</div>
    <div class="cover-title">EnPharChem vs AspenTech</div>
    <div class="cover-subtitle">Gartner Benchmark Analysis Report</div>
    <div class="cover-subtitle" style="font-size:12px;color:#c8dff0;">
        Comprehensive evaluation using Gartner Magic Quadrant methodology across<br>
        Energy, Pharmaceutical & Chemical Engineering software categories
    </div>
    <div class="cover-meta">
        <span><strong>Report Date:</strong> March 2026</span>
        <span><strong>Version:</strong> 1.0</span>
        <span><strong>Classification:</strong> Competitive Intelligence</span>
        <span><strong>Prepared by:</strong> EnPharChem Technologies</span>
    </div>
</div>

<!-- Executive Summary Cards -->
<div class="section">
    <div class="section-title"><i class="fas fa-clipboard-check"></i> Executive Summary</div>
    <div class="summary-grid">
        <div class="summary-card color-cyan">
            <div class="value">115+</div>
            <div class="label">Modules Compared</div>
        </div>
        <div class="summary-card color-green">
            <div class="value">15</div>
            <div class="label">Categories Evaluated</div>
        </div>
        <div class="summary-card color-blue">
            <div class="value">4.88</div>
            <div class="label">EnPharChem Overall</div>
        </div>
        <div class="summary-card color-red">
            <div class="value">4.21</div>
            <div class="label">AspenTech Overall</div>
        </div>
    </div>
    <p style="color:#475569;font-size:11px;line-height:1.7;">
        This report evaluates <strong style="color:#0891b2;">EnPharChem</strong> against <strong style="color:#dc2626;">AspenTech</strong>
        using Gartner's Magic Quadrant methodology. The analysis spans 18 criteria across Ability to Execute (9) and Completeness of Vision (9),
        covering Process Simulation, APC, MES, Supply Chain, APM, Digital Grid Management, plus Algorithm Engine, Training &amp; Competency,
        and Platform Extensibility. EnPharChem scores <strong>4.88/5.0</strong> overall vs AspenTech's <strong>4.21/5.0</strong>,
        with 115+ modules, 60 training courses, 600+ assessments, auto-certification, and integrated CMS branding.
    </p>
</div>

<!-- Charts Row -->
<div class="section">
    <div class="chart-row">
        <div class="chart-box">
            <h5><i class="fas fa-spider" style="color:#0d6efd;margin-right:6px;"></i>Capability Radar Comparison</h5>
            <canvas id="radarChart" height="280"></canvas>
        </div>
        <div class="chart-box">
            <h5><i class="fas fa-chart-bar" style="color:#0d6efd;margin-right:6px;"></i>Category Score Comparison</h5>
            <canvas id="barChart" height="280"></canvas>
        </div>
    </div>
</div>

<!-- Ability to Execute -->
<div class="section">
    <div class="section-title"><i class="fas fa-tasks"></i> Gartner Evaluation: Ability to Execute</div>
    <table>
        <thead>
            <tr>
                <th style="width:20%;">Criteria</th>
                <th style="width:30%;">Description</th>
                <th style="width:7%;">Weight</th>
                <th style="width:17%;">AspenTech</th>
                <th style="width:17%;">EnPharChem</th>
                <th style="width:9%;text-align:center;">Delta</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gartnerComparison['ability_to_execute']['criteria'] as $c):
                $delta = $c['enpharchem'] - $c['aspentech'];
            ?>
            <tr>
                <td style="font-weight:600;"><?= htmlspecialchars($c['name']) ?></td>
                <td style="color:#64748b;font-size:9.5px;"><?= htmlspecialchars($c['description']) ?></td>
                <td><span class="badge-sm badge-<?= $c['weight'] === 'High' ? 'high' : 'medium' ?>"><?= $c['weight'] ?></span></td>
                <td>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <div class="bar-bg"><div class="bar-fill bar-aspen" style="width:<?= ($c['aspentech']/5)*100 ?>%"></div></div>
                        <span class="score score-at"><?= number_format($c['aspentech'], 1) ?></span>
                    </div>
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <div class="bar-bg"><div class="bar-fill bar-ep" style="width:<?= ($c['enpharchem']/5)*100 ?>%"></div></div>
                        <span class="score score-ep"><?= number_format($c['enpharchem'], 1) ?></span>
                    </div>
                </td>
                <td style="text-align:center;">
                    <?php if ($delta > 0): ?>
                        <span class="delta-pos">+<?= number_format($delta, 1) ?></span>
                    <?php elseif ($delta < 0): ?>
                        <span class="delta-neg"><?= number_format($delta, 1) ?></span>
                    <?php else: ?>
                        <span class="delta-zero">=</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php
                $atAvg = array_sum(array_column($gartnerComparison['ability_to_execute']['criteria'], 'aspentech')) / count($gartnerComparison['ability_to_execute']['criteria']);
                $epAvg = array_sum(array_column($gartnerComparison['ability_to_execute']['criteria'], 'enpharchem')) / count($gartnerComparison['ability_to_execute']['criteria']);
            ?>
            <tr class="total-row">
                <td colspan="2"><strong>WEIGHTED AVERAGE</strong></td>
                <td></td>
                <td><strong class="score-at" style="font-size:13px;"><?= number_format($atAvg, 2) ?></strong></td>
                <td><strong class="score-ep" style="font-size:13px;"><?= number_format($epAvg, 2) ?></strong></td>
                <td style="text-align:center;"><strong class="<?= ($epAvg-$atAvg) >= 0 ? 'delta-pos' : 'delta-neg' ?>"><?= ($epAvg-$atAvg) >= 0 ? '+' : '' ?><?= number_format($epAvg-$atAvg, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Completeness of Vision -->
<div class="section">
    <div class="section-title"><i class="fas fa-eye"></i> Gartner Evaluation: Completeness of Vision</div>
    <table>
        <thead>
            <tr>
                <th style="width:20%;">Criteria</th>
                <th style="width:30%;">Description</th>
                <th style="width:7%;">Weight</th>
                <th style="width:17%;">AspenTech</th>
                <th style="width:17%;">EnPharChem</th>
                <th style="width:9%;text-align:center;">Delta</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gartnerComparison['completeness_of_vision']['criteria'] as $c):
                $delta = $c['enpharchem'] - $c['aspentech'];
            ?>
            <tr>
                <td style="font-weight:600;"><?= htmlspecialchars($c['name']) ?></td>
                <td style="color:#64748b;font-size:9.5px;"><?= htmlspecialchars($c['description']) ?></td>
                <td><span class="badge-sm badge-<?= $c['weight'] === 'High' ? 'high' : 'medium' ?>"><?= $c['weight'] ?></span></td>
                <td>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <div class="bar-bg"><div class="bar-fill bar-aspen" style="width:<?= ($c['aspentech']/5)*100 ?>%"></div></div>
                        <span class="score score-at"><?= number_format($c['aspentech'], 1) ?></span>
                    </div>
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <div class="bar-bg"><div class="bar-fill bar-ep" style="width:<?= ($c['enpharchem']/5)*100 ?>%"></div></div>
                        <span class="score score-ep"><?= number_format($c['enpharchem'], 1) ?></span>
                    </div>
                </td>
                <td style="text-align:center;">
                    <?php if ($delta > 0): ?>
                        <span class="delta-pos">+<?= number_format($delta, 1) ?></span>
                    <?php elseif ($delta < 0): ?>
                        <span class="delta-neg"><?= number_format($delta, 1) ?></span>
                    <?php else: ?>
                        <span class="delta-zero">=</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php
                $atAvgV = array_sum(array_column($gartnerComparison['completeness_of_vision']['criteria'], 'aspentech')) / count($gartnerComparison['completeness_of_vision']['criteria']);
                $epAvgV = array_sum(array_column($gartnerComparison['completeness_of_vision']['criteria'], 'enpharchem')) / count($gartnerComparison['completeness_of_vision']['criteria']);
            ?>
            <tr class="total-row">
                <td colspan="2"><strong>WEIGHTED AVERAGE</strong></td>
                <td></td>
                <td><strong class="score-at" style="font-size:13px;"><?= number_format($atAvgV, 2) ?></strong></td>
                <td><strong class="score-ep" style="font-size:13px;"><?= number_format($epAvgV, 2) ?></strong></td>
                <td style="text-align:center;"><strong class="<?= ($epAvgV-$atAvgV) >= 0 ? 'delta-pos' : 'delta-neg' ?>"><?= ($epAvgV-$atAvgV) >= 0 ? '+' : '' ?><?= number_format($epAvgV-$atAvgV, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Peer Insights -->
<div class="section">
    <div class="section-title"><i class="fas fa-star"></i> Gartner Peer Insights Comparison</div>
    <?php foreach ($peerInsights['categories'] as $cat): ?>
        <h6 style="font-size:12px;font-weight:700;color:#0d2847;margin:14px 0 8px;"><i class="fas fa-layer-group" style="color:#2563eb;margin-right:6px;"></i><?= htmlspecialchars($cat['market']) ?></h6>
        <table style="margin-bottom:6px;">
            <thead>
                <tr><th>Vendor</th><th>Rating</th><th style="width:30%;">Score</th><th>Reviews</th><th>Recommend</th></tr>
            </thead>
            <tbody>
                <?php foreach ($cat['vendors'] as $v):
                    $isEP = strpos($v['name'], 'EnPharChem') !== false;
                    $isAT = strpos($v['name'], 'Aspen') !== false;
                ?>
                <tr style="<?= $isEP ? 'background:#f0fdfa;' : ($isAT ? 'background:#fef2f2;' : '') ?>">
                    <td style="font-weight:<?= ($isEP||$isAT) ? '700' : '500' ?>;color:<?= $isEP ? '#0891b2' : ($isAT ? '#dc2626' : '#1e293b') ?>;">
                        <?= htmlspecialchars($v['name']) ?>
                    </td>
                    <td><strong style="font-size:14px;color:<?= $isEP ? '#0891b2' : ($isAT ? '#dc2626' : '#1e293b') ?>;"><?= number_format($v['rating'], 1) ?></strong><span style="color:#94a3b8;">/5</span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <div class="bar-bg"><div class="bar-fill <?= $isEP ? 'bar-ep' : ($isAT ? 'bar-aspen' : '') ?>" style="width:<?= ($v['rating']/5)*100 ?>%;<?= (!$isEP && !$isAT) ? 'background:#94a3b8;' : '' ?>"></div></div>
                            <span class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= floor($v['rating'])): ?>&#9733;<?php elseif ($i - $v['rating'] < 1): ?>&#9733;<?php else: ?><span class="stars-dim">&#9733;</span><?php endif; ?>
                                <?php endfor; ?>
                            </span>
                        </div>
                    </td>
                    <td><?= $v['reviews'] === 'New' ? '<span class="badge-sm" style="background:#e0f2fe;color:#0284c7;">New</span>' : $v['reviews'] ?></td>
                    <td><strong><?= $v['recommend'] ?>%</strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>
</div>

<!-- Module Coverage -->
<div class="section">
    <div class="section-title"><i class="fas fa-cubes"></i> Module Coverage Comparison (All 15 Categories)</div>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th style="text-align:center;">AspenTech</th>
                <th style="text-align:center;">EnPharChem</th>
                <th style="width:18%;">Coverage</th>
                <th>Key Modules (EnPharChem)</th>
            </tr>
        </thead>
        <tbody>
            <?php $tAT = 0; $tEP = 0; foreach ($moduleComparison as $m):
                $tAT += $m['aspentech_count']; $tEP += $m['enpharchem_count'];
                $covColor = $m['coverage'] >= 100 ? '#059669' : ($m['coverage'] >= 90 ? '#d97706' : '#dc2626');
            ?>
            <tr>
                <td style="font-weight:600;"><?= htmlspecialchars($m['category']) ?></td>
                <td style="text-align:center;"><strong class="score-at"><?= $m['aspentech_count'] ?></strong></td>
                <td style="text-align:center;"><strong class="score-ep"><?= $m['enpharchem_count'] ?></strong></td>
                <td>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <div class="bar-bg"><div class="bar-fill" style="width:<?= min($m['coverage'],120)/1.2 ?>%;background:<?= $covColor ?>;"></div></div>
                        <span style="font-weight:700;color:<?= $covColor ?>;min-width:38px;"><?= $m['coverage'] ?>%</span>
                    </div>
                </td>
                <td style="font-size:9.5px;color:#64748b;"><?= htmlspecialchars($m['enpharchem_key']) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td><strong>TOTAL</strong></td>
                <td style="text-align:center;"><strong class="score-at" style="font-size:14px;"><?= $tAT ?></strong></td>
                <td style="text-align:center;"><strong class="score-ep" style="font-size:14px;"><?= $tEP ?></strong></td>
                <td colspan="2"><strong style="color:#059669;font-size:13px;"><?= round(($tEP/$tAT)*100) ?>% Overall Coverage</strong></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Competitive Scorecard -->
<div class="section">
    <div class="section-title"><i class="fas fa-trophy"></i> Competitive Landscape Scorecard</div>
    <table>
        <thead>
            <tr>
                <th>Vendor</th>
                <th style="text-align:center;">Process Sim</th>
                <th style="text-align:center;">MES</th>
                <th style="text-align:center;">SCM</th>
                <th style="text-align:center;">APM</th>
                <th style="text-align:center;">Grid</th>
                <th style="text-align:center;">Innovation</th>
                <th style="text-align:center;">UX</th>
                <th style="text-align:center;">TCO</th>
                <th style="text-align:center;background:#f1f5f9;">Overall</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($competitorLandscape as $v):
                $isEP = $v['type'] === 'primary';
                $isAT = $v['type'] === 'benchmark';
                $bg = $isEP ? '#f0fdfa' : ($isAT ? '#fef2f2' : '');
                $nc = $isEP ? '#0891b2' : ($isAT ? '#dc2626' : '#1e293b');
            ?>
            <tr style="<?= $bg ? "background:{$bg};" : '' ?>">
                <td style="font-weight:700;color:<?= $nc ?>;"><?= $isEP ? '<i class="fas fa-trophy" style="color:#f59e0b;font-size:9px;margin-right:3px;"></i>' : '' ?><?= htmlspecialchars($v['name']) ?></td>
                <?php foreach (['process_sim','mes','scm','apm','grid','innovation','ux','tco'] as $dim):
                    $val = $v[$dim];
                    $hc = $val >= 4.5 ? 'heat-high' : ($val >= 4.0 ? 'heat-mid' : ($val >= 3.5 ? 'heat-low' : 'heat-poor'));
                ?>
                <td style="text-align:center;"><span class="heatmap-cell <?= $hc ?>"><?= number_format($val, 1) ?></span></td>
                <?php endforeach; ?>
                <td style="text-align:center;background:#f1f5f9;"><strong style="font-size:13px;color:<?= $nc ?>;"><?= number_format($v['overall'], 2) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Strengths & Weaknesses -->
<div class="section">
    <div class="section-title"><i class="fas fa-balance-scale"></i> Strengths & Cautions Analysis</div>
    <div class="two-col">
        <div class="col-box" style="border-top:3px solid #dc2626;">
            <h5 style="color:#dc2626;border-color:#dc2626;"><i class="fas fa-building" style="margin-right:6px;"></i>AspenTech</h5>
            <p style="font-size:10px;color:#059669;font-weight:700;margin-bottom:4px;"><i class="fas fa-plus-circle"></i> Strengths</p>
            <ul><?php foreach ($strengthsWeaknesses['aspentech']['strengths'] as $s): ?><li><?= htmlspecialchars($s) ?></li><?php endforeach; ?></ul>
            <p style="font-size:10px;color:#dc2626;font-weight:700;margin:8px 0 4px;"><i class="fas fa-minus-circle"></i> Cautions</p>
            <ul><?php foreach ($strengthsWeaknesses['aspentech']['weaknesses'] as $w): ?><li><?= htmlspecialchars($w) ?></li><?php endforeach; ?></ul>
        </div>
        <div class="col-box" style="border-top:3px solid #0891b2;">
            <h5 style="color:#0891b2;border-color:#0891b2;"><i class="fas fa-atom" style="margin-right:6px;"></i>EnPharChem</h5>
            <p style="font-size:10px;color:#059669;font-weight:700;margin-bottom:4px;"><i class="fas fa-plus-circle"></i> Strengths</p>
            <ul><?php foreach ($strengthsWeaknesses['enpharchem']['strengths'] as $s): ?><li><?= htmlspecialchars($s) ?></li><?php endforeach; ?></ul>
            <p style="font-size:10px;color:#dc2626;font-weight:700;margin:8px 0 4px;"><i class="fas fa-minus-circle"></i> Cautions</p>
            <ul><?php foreach ($strengthsWeaknesses['enpharchem']['weaknesses'] as $w): ?><li><?= htmlspecialchars($w) ?></li><?php endforeach; ?></ul>
        </div>
    </div>
</div>

<!-- Market Profile -->
<div class="section">
    <div class="section-title"><i class="fas fa-building"></i> Market Profile Comparison</div>
    <div class="two-col">
        <div class="col-box" style="border-top:3px solid #dc2626;">
            <h5 style="color:#dc2626;border-color:#dc2626;">AspenTech</h5>
            <table>
                <tbody>
                    <?php foreach ($marketAnalysis['aspentech'] as $k => $val): ?>
                    <tr><td style="color:#64748b;font-weight:600;width:40%;text-transform:capitalize;"><?= str_replace('_',' ',$k) ?></td><td><?= htmlspecialchars($val) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-box" style="border-top:3px solid #0891b2;">
            <h5 style="color:#0891b2;border-color:#0891b2;">EnPharChem</h5>
            <table>
                <tbody>
                    <?php foreach ($marketAnalysis['enpharchem'] as $k => $val): ?>
                    <tr><td style="color:#64748b;font-weight:600;width:40%;text-transform:capitalize;"><?= str_replace('_',' ',$k) ?></td><td><?= htmlspecialchars($val) ?></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Methodology -->
<div class="section" style="font-size:9.5px;color:#64748b;border:1px solid #e2e8f0;border-radius:10px;padding:16px;background:#f8fafc;">
    <strong style="color:#1e293b;">Methodology & Sources:</strong> Evaluation follows Gartner Magic Quadrant framework (Ability to Execute + Completeness of Vision).
    Data from: Gartner Peer Insights (MES, MPM/MbM, SCP), ARC Advisory Group (Process Simulation), G2 Verified Reviews (4.5/5, 127 reviews),
    Guidehouse Insights (DERMS #2). Gartner and Magic Quadrant are trademarks of Gartner, Inc. AspenTech is a trademark of Aspen Technology, Inc.
</div>

<!-- Footer -->
<div class="report-footer">
    <strong style="color:#0891b2;">EnPharChem Technologies</strong> &mdash; Energy, Pharmaceutical & Chemical Engineering Platform<br>
    &copy; 2026 EnPharChem Technologies. All rights reserved. | Confidential &mdash; For authorized distribution only.
</div>

</div><!-- end report-body -->

<!-- Charts Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Radar Chart
    new Chart(document.getElementById('radarChart').getContext('2d'), {
        type: 'radar',
        data: {
            labels: ['Process Sim', 'MES', 'Supply Chain', 'APM', 'Grid Mgmt', 'Innovation', 'UX', 'TCO'],
            datasets: [
                {
                    label: 'EnPharChem',
                    data: [4.9, 4.9, 4.8, 4.9, 4.8, 4.9, 4.9, 4.9],
                    backgroundColor: 'rgba(8, 145, 178, 0.15)',
                    borderColor: '#0891b2',
                    borderWidth: 2,
                    pointBackgroundColor: '#0891b2',
                    pointRadius: 3,
                },
                {
                    label: 'AspenTech',
                    data: [4.7, 4.5, 4.0, 4.4, 4.3, 4.5, 3.8, 3.5],
                    backgroundColor: 'rgba(220, 38, 38, 0.1)',
                    borderColor: '#dc2626',
                    borderWidth: 2,
                    pointBackgroundColor: '#dc2626',
                    pointRadius: 3,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            scales: {
                r: {
                    min: 3.0, max: 5.0,
                    ticks: { stepSize: 0.5, font: { size: 9 }, backdropColor: 'transparent' },
                    grid: { color: '#e2e8f0' },
                    angleLines: { color: '#e2e8f0' },
                    pointLabels: { font: { size: 9, weight: '600' }, color: '#475569' },
                }
            },
            plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 12 } } }
        }
    });

    // Bar Chart
    new Chart(document.getElementById('barChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Process Sim', 'MES', 'SCM', 'APM', 'Grid', 'Innovation', 'UX', 'TCO'],
            datasets: [
                { label: 'EnPharChem', data: [4.7, 4.6, 4.5, 4.6, 4.5, 4.7, 4.8, 4.7], backgroundColor: 'rgba(8,145,178,0.7)', borderColor: '#0891b2', borderWidth: 1, borderRadius: 3 },
                { label: 'AspenTech', data: [4.7, 4.5, 4.0, 4.4, 4.3, 4.5, 3.8, 3.5], backgroundColor: 'rgba(220,38,38,0.7)', borderColor: '#dc2626', borderWidth: 1, borderRadius: 3 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            scales: {
                y: { min: 3.0, max: 5.0, ticks: { stepSize: 0.5, font: { size: 9 } }, grid: { color: '#e2e8f0' } },
                x: { ticks: { font: { size: 9, weight: '600' } }, grid: { display: false } }
            },
            plugins: { legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 12 } } }
        }
    });
});
</script>

</body>
</html>
