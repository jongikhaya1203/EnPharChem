<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EnPharChem Product Brochure - EnPharChem</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<style>
/* ============================================================
   ENPHARCHEM PRODUCT MARKETING BROCHURE
   Premium PDF-Ready Document
   ============================================================ */

@page { size: A4; margin: 12mm; }
@media print {
    .print-bar { display: none !important; }
    .page-break { page-break-before: always; }
    body { font-size: 10px; }
}

* { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --primary: #0d6efd;
    --accent: #0dcaf0;
    --dark: #0a1628;
    --dark2: #0d2847;
    --dark3: #0f3460;
    --light: #f0f6ff;
    --text: #1a1a2e;
    --muted: #6c757d;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
}

body {
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    color: var(--text);
    background: #e8edf3;
    font-size: 11px;
    line-height: 1.6;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

/* ---- PRINT BAR ---- */
.print-bar {
    position: fixed; top: 0; left: 0; right: 0;
    background: linear-gradient(135deg, #0f1117, #1a1d23);
    padding: 12px 30px;
    display: flex; align-items: center; justify-content: space-between;
    z-index: 9999;
    box-shadow: 0 4px 20px rgba(0,0,0,.4);
    backdrop-filter: blur(10px);
}
.print-bar .brand { color: #fff; font-weight: 700; font-size: 16px; letter-spacing: .3px; }
.print-bar .brand span { color: var(--accent); }
.print-bar .actions { display: flex; gap: 10px; }
.btn-pdf {
    padding: 8px 22px; border-radius: 8px; font-weight: 600; font-size: 13px;
    border: none; cursor: pointer; display: inline-flex; align-items: center;
    gap: 8px; text-decoration: none; transition: all .2s;
}
.btn-dl { background: linear-gradient(135deg, var(--primary), #0a58ca); color: #fff; }
.btn-dl:hover { background: linear-gradient(135deg, #0a58ca, #084298); color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(13,110,253,.4); }
.btn-bk { background: rgba(255,255,255,.08); color: #fff; border: 1px solid rgba(255,255,255,.15); }
.btn-bk:hover { background: rgba(255,255,255,.15); color: #fff; }

/* ---- BROCHURE CONTAINER ---- */
.brochure {
    padding-top: 70px;
    max-width: 210mm;
    margin: 0 auto;
    background: #fff;
    box-shadow: 0 0 60px rgba(0,0,0,.15);
}

/* ============================================================
   PAGE 1 - COVER
   ============================================================ */
.cover-page {
    background: linear-gradient(160deg, #0a1628 0%, #0d2847 40%, #0f3460 70%, #0a1628 100%);
    color: #fff;
    min-height: 297mm;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    text-align: center;
    padding: 60px 50px;
    position: relative;
    overflow: hidden;
}
.cover-page::before {
    content: '';
    position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
    background: radial-gradient(ellipse at 30% 20%, rgba(13,110,253,.15) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 80%, rgba(13,202,240,.1) 0%, transparent 50%);
    animation: coverGlow 8s ease-in-out infinite alternate;
}
@keyframes coverGlow {
    0% { transform: translate(0, 0); }
    100% { transform: translate(-2%, -2%); }
}
.cover-page * { position: relative; z-index: 1; }

.cover-logo-wrap {
    width: 120px; height: 120px;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    border-radius: 28px;
    display: flex; align-items: center; justify-content: center;
    font-size: 48px; font-weight: 900; color: #fff;
    margin: 0 auto 40px;
    box-shadow: 0 20px 60px rgba(13,110,253,.4), 0 0 0 1px rgba(255,255,255,.1);
    letter-spacing: -2px;
}
.cover-page h1 {
    font-size: 52px; font-weight: 900; letter-spacing: -1px;
    margin-bottom: 16px;
    background: linear-gradient(135deg, #fff, #c8deff);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
}
.cover-tagline {
    font-size: 20px; color: #a8c8e8; font-weight: 400;
    max-width: 600px; margin: 0 auto 12px; line-height: 1.5;
}
.cover-subtitle {
    font-size: 16px; color: var(--accent); font-weight: 600;
    letter-spacing: 2px; text-transform: uppercase;
    margin-bottom: 50px;
}
.cover-badge {
    display: inline-flex; align-items: center; gap: 12px;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 50px; padding: 12px 28px;
    font-size: 13px; color: #c8deff; margin-bottom: 30px;
    backdrop-filter: blur(10px);
}
.cover-badge i { color: var(--warning); font-size: 18px; }
.cover-badge strong { color: #fff; }
.cover-meta {
    font-size: 12px; color: #5a8ab5;
    border-top: 1px solid rgba(255,255,255,.08);
    padding-top: 30px; margin-top: 20px;
    letter-spacing: 1px;
}

/* ---- Decorative grid lines on cover ---- */
.cover-grid {
    position: absolute; top: 0; left: 0; right: 0; bottom: 0;
    background-image:
        linear-gradient(rgba(255,255,255,.02) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.02) 1px, transparent 1px);
    background-size: 40px 40px;
    z-index: 0;
}

/* ============================================================
   SHARED SECTION STYLES
   ============================================================ */
.page { padding: 40px 45px; min-height: auto; }
.page-dark {
    background: linear-gradient(160deg, var(--dark), var(--dark2));
    color: #fff;
}
.page-light { background: #fff; }
.page-alt { background: var(--light); }

.section-header {
    font-size: 28px; font-weight: 800; margin-bottom: 6px;
    letter-spacing: -.5px;
}
.section-header.gradient-text {
    background: linear-gradient(135deg, var(--primary), var(--accent));
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.section-header.white { color: #fff; -webkit-text-fill-color: #fff; }
.section-sub {
    font-size: 13px; color: var(--muted); margin-bottom: 30px;
    max-width: 500px;
}
.section-sub.light { color: #8aa8c8; }

.section-divider {
    width: 60px; height: 4px;
    background: linear-gradient(90deg, var(--primary), var(--accent));
    border-radius: 2px; margin-bottom: 25px;
}

/* ============================================================
   PAGE 2 - WHY ENPHARCHEM
   ============================================================ */
.value-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 28px; }
.value-card {
    background: linear-gradient(135deg, #f8faff, #eef4ff);
    border: 1px solid #dde8f8;
    border-radius: 14px; padding: 22px;
    transition: transform .2s;
}
.value-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(13,110,253,.1); }
.value-card .icon-box {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: #fff; margin-bottom: 12px;
}
.value-card:nth-child(1) .icon-box { background: linear-gradient(135deg, var(--primary), #4d94ff); }
.value-card:nth-child(2) .icon-box { background: linear-gradient(135deg, var(--accent), #0eb8d4); }
.value-card:nth-child(3) .icon-box { background: linear-gradient(135deg, var(--success), #34d399); }
.value-card:nth-child(4) .icon-box { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
.value-card h4 { font-size: 14px; font-weight: 700; color: var(--dark); margin-bottom: 6px; }
.value-card p { font-size: 11px; color: #555; line-height: 1.5; margin: 0; }

.diff-list { list-style: none; padding: 0; margin: 0; }
.diff-list li {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 8px 0; font-size: 11.5px; color: #333;
    border-bottom: 1px solid #eee;
}
.diff-list li:last-child { border-bottom: none; }
.diff-list li i { color: var(--success); margin-top: 2px; font-size: 12px; flex-shrink: 0; }

/* ============================================================
   PAGE 3 - DASHBOARD MOCKUP
   ============================================================ */
.browser-frame {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,.25), 0 0 0 1px rgba(0,0,0,.1);
    background: #1a1d23;
    margin-bottom: 12px;
}
.browser-bar {
    background: linear-gradient(180deg, #2a2d35, #24272f);
    padding: 10px 16px;
    display: flex; align-items: center; gap: 8px;
    border-bottom: 1px solid #333;
}
.browser-dot { width: 10px; height: 10px; border-radius: 50%; }
.browser-dot.red { background: #ff5f57; }
.browser-dot.yellow { background: #febc2e; }
.browser-dot.green { background: #28c840; }
.browser-url {
    flex: 1; margin-left: 12px;
    background: #1a1d23; border-radius: 6px;
    padding: 4px 12px; color: #888; font-size: 10px;
    font-family: 'Consolas', monospace;
}

.dash-layout { display: flex; height: 340px; }
.dash-sidebar {
    width: 180px; background: linear-gradient(180deg, #0f1117, #141720);
    padding: 16px 0; flex-shrink: 0;
    border-right: 1px solid #222;
}
.dash-sidebar-brand {
    padding: 0 14px 14px;
    font-size: 13px; font-weight: 700; color: #fff;
    border-bottom: 1px solid #222; margin-bottom: 10px;
}
.dash-sidebar-brand span { color: var(--accent); }
.dash-nav-item {
    padding: 7px 14px; font-size: 10px; color: #7a8494;
    display: flex; align-items: center; gap: 8px; cursor: default;
}
.dash-nav-item.active { color: #fff; background: rgba(13,110,253,.15); border-right: 2px solid var(--primary); }
.dash-nav-item i { width: 14px; text-align: center; font-size: 10px; }

.dash-main { flex: 1; padding: 16px; overflow: hidden; }
.dash-topbar {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 14px;
}
.dash-topbar h3 { color: #fff; font-size: 14px; font-weight: 700; margin: 0; }
.dash-topbar .user-pill {
    background: #222; border-radius: 20px; padding: 4px 12px;
    color: #aaa; font-size: 9px; display: flex; align-items: center; gap: 6px;
}
.dash-topbar .user-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--success); }

.dash-stats { display: flex; gap: 10px; margin-bottom: 14px; }
.dash-stat {
    flex: 1; background: linear-gradient(135deg, #1e2130, #252838);
    border-radius: 8px; padding: 12px;
    border: 1px solid #2a2d3a;
}
.dash-stat .label { font-size: 9px; color: #6a7486; text-transform: uppercase; letter-spacing: .5px; }
.dash-stat .num { font-size: 22px; font-weight: 800; margin: 2px 0; }
.dash-stat:nth-child(1) .num { color: var(--primary); }
.dash-stat:nth-child(2) .num { color: var(--accent); }
.dash-stat:nth-child(3) .num { color: var(--success); }
.dash-stat .change { font-size: 8px; color: var(--success); }

.dash-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 14px; }
.dash-module-card {
    background: #1e2130; border-radius: 6px; padding: 8px;
    text-align: center; border: 1px solid #2a2d3a;
}
.dash-module-card .mod-icon {
    width: 24px; height: 24px; border-radius: 6px; margin: 0 auto 4px;
    display: flex; align-items: center; justify-content: center;
    font-size: 10px; color: #fff;
}
.dash-module-card .mod-name { font-size: 8px; color: #aaa; }

.dash-table { width: 100%; }
.dash-table th { font-size: 8px; color: #555; text-transform: uppercase; padding: 4px 8px; text-align: left; border-bottom: 1px solid #2a2d3a; }
.dash-table td { font-size: 9px; color: #bbb; padding: 5px 8px; border-bottom: 1px solid #1e2130; }
.dash-table .status-dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; margin-right: 4px; }

.screenshot-caption {
    text-align: center; font-size: 11px; color: var(--muted);
    font-style: italic; margin-top: 8px;
}

/* ============================================================
   PAGE 4-5 - MODULE CATEGORIES
   ============================================================ */
.cat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
.cat-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px;
    transition: all .2s;
    position: relative;
    overflow: hidden;
}
.cat-card::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
}
.cat-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,.08); transform: translateY(-1px); }
.cat-card .cat-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; color: #fff; margin-bottom: 10px;
}
.cat-card h5 { font-size: 12px; font-weight: 700; color: var(--dark); margin-bottom: 2px; }
.cat-card .cat-count {
    font-size: 9px; font-weight: 600; color: var(--primary);
    text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px;
}
.cat-card .cat-desc { font-size: 10px; color: #666; margin-bottom: 8px; line-height: 1.5; }
.cat-card .cat-modules { list-style: none; padding: 0; margin: 0; }
.cat-card .cat-modules li {
    font-size: 9px; color: #555; padding: 2px 0;
    display: flex; align-items: center; gap: 4px;
}
.cat-card .cat-modules li::before {
    content: '';
    width: 4px; height: 4px; border-radius: 50%;
    background: var(--primary); flex-shrink: 0;
}

/* Category card top-bar colors */
.cat-card.c1::before { background: linear-gradient(90deg, #ef4444, #f97316); }
.cat-card.c2::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
.cat-card.c3::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.cat-card.c4::before { background: linear-gradient(90deg, var(--primary), #60a5fa); }
.cat-card.c5::before { background: linear-gradient(90deg, #10b981, #34d399); }
.cat-card.c6::before { background: linear-gradient(90deg, #06b6d4, #22d3ee); }
.cat-card.c7::before { background: linear-gradient(90deg, #6366f1, #818cf8); }
.cat-card.c8::before { background: linear-gradient(90deg, #ec4899, #f472b6); }
.cat-card.c9::before { background: linear-gradient(90deg, #14b8a6, #2dd4bf); }
.cat-card.c10::before { background: linear-gradient(90deg, #f97316, #fb923c); }
.cat-card.c11::before { background: linear-gradient(90deg, #64748b, #94a3b8); }
.cat-card.c12::before { background: linear-gradient(90deg, #0ea5e9, #38bdf8); }
.cat-card.c13::before { background: linear-gradient(90deg, #d946ef, #e879f9); }
.cat-card.c14::before { background: linear-gradient(90deg, #84cc16, #a3e635); }
.cat-card.c15::before { background: linear-gradient(90deg, #eab308, #facc15); }

.cat-card.c1 .cat-icon { background: linear-gradient(135deg, #ef4444, #f97316); }
.cat-card.c2 .cat-icon { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
.cat-card.c3 .cat-icon { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
.cat-card.c4 .cat-icon { background: linear-gradient(135deg, var(--primary), #60a5fa); }
.cat-card.c5 .cat-icon { background: linear-gradient(135deg, #10b981, #34d399); }
.cat-card.c6 .cat-icon { background: linear-gradient(135deg, #06b6d4, #22d3ee); }
.cat-card.c7 .cat-icon { background: linear-gradient(135deg, #6366f1, #818cf8); }
.cat-card.c8 .cat-icon { background: linear-gradient(135deg, #ec4899, #f472b6); }
.cat-card.c9 .cat-icon { background: linear-gradient(135deg, #14b8a6, #2dd4bf); }
.cat-card.c10 .cat-icon { background: linear-gradient(135deg, #f97316, #fb923c); }
.cat-card.c11 .cat-icon { background: linear-gradient(135deg, #64748b, #94a3b8); }
.cat-card.c12 .cat-icon { background: linear-gradient(135deg, #0ea5e9, #38bdf8); }
.cat-card.c13 .cat-icon { background: linear-gradient(135deg, #d946ef, #e879f9); }
.cat-card.c14 .cat-icon { background: linear-gradient(135deg, #84cc16, #a3e635); }
.cat-card.c15 .cat-icon { background: linear-gradient(135deg, #eab308, #facc15); }

/* ============================================================
   PAGE 6 - MODULE SCREENSHOTS
   ============================================================ */
.screenshots-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.screenshot-wrap { margin-bottom: 0; }

.mini-browser {
    border-radius: 10px; overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,.2);
    background: #1a1d23;
}
.mini-browser-bar {
    background: #2a2d35; padding: 7px 12px;
    display: flex; align-items: center; gap: 5px;
    border-bottom: 1px solid #333;
}
.mini-dot { width: 7px; height: 7px; border-radius: 50%; }
.mini-dot.r { background: #ff5f57; }
.mini-dot.y { background: #febc2e; }
.mini-dot.g { background: #28c840; }
.mini-title { margin-left: 10px; color: #999; font-size: 9px; font-weight: 600; }
.mini-content { padding: 12px; min-height: 160px; position: relative; }

/* HYSYS Flowsheet mockup */
.flow-canvas { position: relative; height: 140px; }
.flow-box {
    position: absolute; background: #252838; border: 1px solid #3a3f52;
    border-radius: 6px; padding: 6px 10px; font-size: 8px; color: #ccc;
    text-align: center; font-weight: 600;
}
.flow-line {
    position: absolute; height: 2px; background: var(--accent);
    top: 50%;
}
.flow-sidebar {
    position: absolute; right: 0; top: 0; bottom: 0; width: 80px;
    background: #141720; border-left: 1px solid #2a2d3a;
    padding: 8px; font-size: 7px; color: #777;
}
.flow-sidebar .comp { padding: 2px 0; border-bottom: 1px solid #1e2130; }

/* Chemical simulation table */
.sim-table { width: 100%; border-collapse: collapse; }
.sim-table th { font-size: 8px; color: var(--accent); padding: 4px 6px; text-align: left; border-bottom: 1px solid #2a2d3a; text-transform: uppercase; }
.sim-table td { font-size: 8px; color: #aaa; padding: 4px 6px; border-bottom: 1px solid #1e2130; }
.sim-table tr:nth-child(even) td { background: rgba(255,255,255,.02); }

/* Bar chart mockup */
.bar-chart { display: flex; align-items: flex-end; gap: 6px; height: 80px; padding-top: 10px; }
.bar-col { display: flex; flex-direction: column; align-items: center; flex: 1; }
.bar-fill { width: 100%; border-radius: 3px 3px 0 0; min-height: 4px; }
.bar-label { font-size: 7px; color: #666; margin-top: 3px; }

/* Status indicators */
.status-row { display: flex; align-items: center; gap: 8px; padding: 4px 0; }
.status-indicator { width: 8px; height: 8px; border-radius: 50%; }
.status-indicator.on { background: var(--success); box-shadow: 0 0 6px rgba(16,185,129,.5); }
.status-indicator.warn { background: var(--warning); box-shadow: 0 0 6px rgba(245,158,11,.5); }
.status-label { font-size: 8px; color: #999; }
.status-value { font-size: 8px; color: #ccc; margin-left: auto; font-family: 'Consolas', monospace; }

/* Trending lines mockup */
.trend-area {
    position: relative; height: 110px;
    background: linear-gradient(180deg, transparent, rgba(13,110,253,.05));
    border-left: 1px solid #2a2d3a; border-bottom: 1px solid #2a2d3a;
}
.trend-line {
    position: absolute; left: 0; right: 0; height: 2px;
    border-radius: 1px;
}
.trend-grid-line {
    position: absolute; left: 0; right: 0; height: 1px;
    background: rgba(255,255,255,.03);
}
.trend-y-label { position: absolute; left: -20px; font-size: 6px; color: #555; }

/* SCADA grid mockup */
.scada-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px;
}
.scada-node {
    background: #1e2130; border-radius: 4px; padding: 6px;
    text-align: center; border: 1px solid #2a2d3a;
}
.scada-node .node-icon { font-size: 14px; margin-bottom: 2px; }
.scada-node .node-label { font-size: 7px; color: #888; }
.scada-node .node-val { font-size: 9px; font-weight: 700; font-family: monospace; }
.scada-node.gen .node-icon { color: var(--success); }
.scada-node.sub .node-icon { color: var(--warning); }
.scada-node.load .node-icon { color: var(--accent); }
.scada-node.xfmr .node-icon { color: #a78bfa; }
.scada-line-h { height: 2px; background: #2a2d3a; grid-column: span 4; margin: 0 10px; border-radius: 1px; }

/* Specs table for exchanger */
.specs-table { width: 100%; border-collapse: collapse; }
.specs-table th { font-size: 7px; color: #666; padding: 3px 6px; text-align: left; background: #141720; text-transform: uppercase; }
.specs-table td { font-size: 8px; color: #bbb; padding: 3px 6px; border-bottom: 1px solid #1e2130; }
.result-panel {
    background: linear-gradient(135deg, rgba(13,110,253,.1), rgba(13,202,240,.1));
    border: 1px solid rgba(13,110,253,.2);
    border-radius: 6px; padding: 8px; margin-top: 8px;
}
.result-panel .rp-title { font-size: 8px; color: var(--accent); font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
.result-panel .rp-row { display: flex; justify-content: space-between; font-size: 8px; padding: 2px 0; }
.result-panel .rp-label { color: #888; }
.result-panel .rp-val { color: #fff; font-weight: 600; font-family: monospace; }

/* ============================================================
   PAGE 7 - GARTNER BENCHMARK
   ============================================================ */
.benchmark-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
.benchmark-table th { font-size: 10px; padding: 10px 12px; text-align: left; }
.benchmark-table th:first-child { color: #8aa8c8; }
.benchmark-table th:nth-child(2) { color: var(--accent); text-align: center; }
.benchmark-table th:nth-child(3) { color: #888; text-align: center; }
.benchmark-table td { padding: 8px 12px; font-size: 11px; border-bottom: 1px solid rgba(255,255,255,.06); }
.benchmark-table td:first-child { color: #c8deff; }
.benchmark-table td:nth-child(2),
.benchmark-table td:nth-child(3) { text-align: center; font-family: 'Consolas', monospace; font-weight: 600; }

.score-bar-wrap { display: flex; align-items: center; gap: 8px; }
.score-bar { height: 6px; border-radius: 3px; flex-shrink: 0; }
.score-bar.ep { background: linear-gradient(90deg, var(--primary), var(--accent)); }
.score-bar.at { background: #4a5568; }

.key-win {
    display: flex; align-items: center; gap: 16px;
    background: rgba(13,202,240,.08);
    border: 1px solid rgba(13,202,240,.15);
    border-radius: 10px; padding: 14px 18px;
    margin-bottom: 10px;
}
.key-win .kw-label { font-size: 11px; color: #c8deff; flex: 1; }
.key-win .kw-scores { display: flex; gap: 20px; }
.key-win .kw-score { text-align: center; }
.key-win .kw-score .num { font-size: 20px; font-weight: 800; display: block; }
.key-win .kw-score .who { font-size: 8px; text-transform: uppercase; letter-spacing: .5px; }
.kw-ep .num { color: var(--accent); }
.kw-at .num { color: #6b7280; }
.kw-at .who { color: #6b7280; }

.overall-score-box {
    text-align: center;
    background: linear-gradient(135deg, rgba(13,110,253,.15), rgba(13,202,240,.1));
    border: 1px solid rgba(13,202,240,.2);
    border-radius: 16px; padding: 24px; margin-bottom: 24px;
}
.overall-score-box .os-label { font-size: 11px; color: #8aa8c8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
.overall-score-box .os-scores { display: flex; justify-content: center; gap: 50px; }
.overall-score-box .os-score .big { font-size: 42px; font-weight: 900; display: block; }
.overall-score-box .os-score .tag { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
.os-ep .big { color: var(--accent); }
.os-ep .tag { color: var(--accent); }
.os-at .big { color: #6b7280; }
.os-at .tag { color: #6b7280; }
.os-vs { font-size: 14px; color: #4a5568; align-self: center; font-weight: 700; }

/* ============================================================
   PAGE 8 - PRICING
   ============================================================ */
.pricing-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
.price-card {
    border-radius: 14px; padding: 24px 18px; text-align: center;
    border: 1px solid #e2e8f0;
    background: #fff;
    position: relative;
}
.price-card.featured {
    border: 2px solid var(--primary);
    background: linear-gradient(180deg, #f0f6ff, #fff);
    box-shadow: 0 8px 30px rgba(13,110,253,.15);
    transform: scale(1.03);
    z-index: 1;
}
.price-card .popular-badge {
    position: absolute; top: -10px; left: 50%; transform: translateX(-50%);
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: #fff; font-size: 8px; font-weight: 700;
    padding: 3px 14px; border-radius: 20px;
    text-transform: uppercase; letter-spacing: .5px;
}
.price-card .tier-name { font-size: 14px; font-weight: 700; color: var(--dark); margin-bottom: 4px; }
.price-card .tier-price { font-size: 26px; font-weight: 900; color: var(--primary); margin-bottom: 2px; }
.price-card .tier-price small { font-size: 11px; font-weight: 400; color: var(--muted); }
.price-card .tier-price .free { color: var(--success); }
.price-card .tier-period { font-size: 9px; color: var(--muted); margin-bottom: 16px; }
.price-card .tier-features { list-style: none; padding: 0; margin: 0 0 18px; text-align: left; }
.price-card .tier-features li {
    font-size: 10px; color: #555; padding: 5px 0;
    display: flex; align-items: flex-start; gap: 6px;
    border-bottom: 1px solid #f0f0f0;
}
.price-card .tier-features li:last-child { border-bottom: none; }
.price-card .tier-features li i { color: var(--success); margin-top: 2px; font-size: 9px; }
.price-card .tier-features li i.fa-times { color: #ccc; }
.price-cta {
    display: block; width: 100%; padding: 8px; border-radius: 8px;
    font-size: 11px; font-weight: 700; text-align: center;
    text-decoration: none; border: none; cursor: pointer;
    transition: all .2s;
}
.price-cta.outline { background: #fff; color: var(--primary); border: 1.5px solid var(--primary); }
.price-cta.outline:hover { background: var(--primary); color: #fff; }
.price-cta.filled { background: linear-gradient(135deg, var(--primary), #0a58ca); color: #fff; }
.price-cta.filled:hover { box-shadow: 0 4px 15px rgba(13,110,253,.4); }

/* ============================================================
   PAGE 9 - CONTACT & CTA
   ============================================================ */
.cta-hero {
    text-align: center; padding: 50px 40px;
    background: linear-gradient(160deg, var(--dark) 0%, var(--dark2) 50%, var(--dark3) 100%);
    border-radius: 20px; color: #fff;
    position: relative; overflow: hidden;
    margin-bottom: 30px;
}
.cta-hero::before {
    content: '';
    position: absolute; top: -100px; right: -100px;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(13,202,240,.15), transparent 70%);
    border-radius: 50%;
}
.cta-hero::after {
    content: '';
    position: absolute; bottom: -80px; left: -80px;
    width: 250px; height: 250px;
    background: radial-gradient(circle, rgba(13,110,253,.12), transparent 70%);
    border-radius: 50%;
}
.cta-hero * { position: relative; z-index: 1; }
.cta-hero h2 { font-size: 30px; font-weight: 900; margin-bottom: 10px; }
.cta-hero p { color: #8aa8c8; font-size: 14px; margin-bottom: 28px; }
.cta-btn {
    display: inline-block;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    color: #fff; padding: 14px 40px; border-radius: 12px;
    font-size: 15px; font-weight: 700; text-decoration: none;
    box-shadow: 0 8px 30px rgba(13,110,253,.3);
    transition: all .2s;
}
.cta-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(13,110,253,.4); color: #fff; }

.contact-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 30px; }
.contact-card {
    text-align: center; padding: 24px 16px;
    background: var(--light); border-radius: 14px;
    border: 1px solid #dde8f8;
}
.contact-card i { font-size: 22px; color: var(--primary); margin-bottom: 10px; }
.contact-card h5 { font-size: 13px; font-weight: 700; color: var(--dark); margin-bottom: 6px; }
.contact-card p { font-size: 11px; color: #555; margin: 0; }
.contact-card a { color: var(--primary); text-decoration: none; font-weight: 600; }

.social-proof {
    text-align: center; padding: 20px;
    color: var(--muted); font-size: 13px;
}
.social-proof .number { font-size: 28px; font-weight: 900; color: var(--primary); display: block; }

/* ---- FOOTER ---- */
.brochure-footer {
    text-align: center; padding: 20px;
    border-top: 1px solid #e2e8f0;
    font-size: 10px; color: #999;
    background: #f8fafc;
}
.brochure-footer.dark {
    background: var(--dark);
    border-top-color: #1a2744;
    color: #4a6080;
}
</style>
</head>
<body>

<!-- ============================================================
     PRINT BAR
     ============================================================ -->
<div class="print-bar">
    <div class="brand">En<span>Phar</span>Chem <span style="font-weight:400;font-size:12px;color:#888;margin-left:8px;">Product Brochure</span></div>
    <div class="actions">
        <a href="javascript:history.back()" class="btn-pdf btn-bk"><i class="fas fa-arrow-left"></i> Back</a>
        <a href="javascript:window.print()" class="btn-pdf btn-dl"><i class="fas fa-file-pdf"></i> Download PDF</a>
    </div>
</div>

<div class="brochure">

<!-- ============================================================
     PAGE 1 - COVER
     ============================================================ -->
<div class="cover-page">
    <div class="cover-grid"></div>

    <div class="cover-logo-wrap">EP</div>

    <h1>EnPharChem Platform</h1>

    <div class="cover-tagline">The Complete Energy, Pharmaceutical &amp; Chemical Engineering Solution</div>

    <div class="cover-subtitle">115+ Modules &nbsp;|&nbsp; 15 Categories &nbsp;|&nbsp; One Unified Platform</div>

    <div class="cover-badge">
        <i class="fas fa-trophy"></i>
        <span><strong>Benchmarked against AspenTech</strong> &mdash; Gartner Rated</span>
    </div>

    <div class="cover-meta">
        Version 1.0 &nbsp;&bull;&nbsp; March 2026 &nbsp;&bull;&nbsp; EnPharChem Technologies
    </div>
</div>

<!-- ============================================================
     PAGE 2 - WHY ENPHARCHEM
     ============================================================ -->
<div class="page page-light page-break">
    <div class="section-header gradient-text">Why EnPharChem?</div>
    <div class="section-sub">A modern, unified engineering platform that replaces fragmented legacy tools with a single, browser-based solution.</div>
    <div class="section-divider"></div>

    <div class="value-grid">
        <div class="value-card">
            <div class="icon-box"><i class="fas fa-cubes"></i></div>
            <h4>Complete Module Parity</h4>
            <p>115+ modules matching AspenTech's full portfolio across process simulation, advanced control, MES, supply chain, APM, and digital grid management &mdash; all in one platform.</p>
        </div>
        <div class="value-card">
            <div class="icon-box"><i class="fas fa-globe"></i></div>
            <h4>Modern Web Architecture</h4>
            <p>Browser-based, no desktop installation required. Responsive design works on any device. Built with modern frameworks for speed and reliability.</p>
        </div>
        <div class="value-card">
            <div class="icon-box"><i class="fas fa-dollar-sign"></i></div>
            <h4>Lower Total Cost of Ownership</h4>
            <p>Web-native deployment eliminates expensive infrastructure. Open standards reduce vendor lock-in. Flexible licensing adapts to your needs.</p>
        </div>
        <div class="value-card">
            <div class="icon-box"><i class="fas fa-flask"></i></div>
            <h4>Integrated Pharma + Energy + Chemical</h4>
            <p>The only platform covering all three industries in a unified environment. Share data, models, and workflows across domains seamlessly.</p>
        </div>
    </div>

    <h4 style="font-size:14px;font-weight:700;color:var(--dark);margin-bottom:12px;"><i class="fas fa-check-double" style="color:var(--primary);margin-right:8px;"></i>Key Differentiators vs AspenTech</h4>
    <ul class="diff-list">
        <li><i class="fas fa-check-circle"></i> Zero-install deployment &mdash; access from any browser, anywhere, on any device</li>
        <li><i class="fas fa-check-circle"></i> Unified data model across all 115+ modules eliminates integration headaches</li>
        <li><i class="fas fa-check-circle"></i> Modern UX with dark mode, responsive layouts, and real-time collaboration</li>
        <li><i class="fas fa-check-circle"></i> Built-in pharmaceutical modules (Batch Modeling, Chromatography, Formulation) not available in AspenTech</li>
        <li><i class="fas fa-check-circle"></i> 60% lower infrastructure cost with cloud-native or on-premise deployment</li>
        <li><i class="fas fa-check-circle"></i> Open API architecture for seamless integration with enterprise systems</li>
        <li><i class="fas fa-check-circle"></i> Gartner-rated higher in Innovation (4.7 vs 4.5), UX (4.8 vs 3.8), and TCO (4.7 vs 3.5)</li>
    </ul>
</div>

<!-- ============================================================
     PAGE 3 - PLATFORM AT A GLANCE (DASHBOARD MOCKUP)
     ============================================================ -->
<div class="page page-alt page-break">
    <div class="section-header gradient-text">Platform at a Glance</div>
    <div class="section-sub">A unified dashboard for all your engineering operations, simulations, and project management.</div>
    <div class="section-divider"></div>

    <div class="browser-frame">
        <div class="browser-bar">
            <div class="browser-dot red"></div>
            <div class="browser-dot yellow"></div>
            <div class="browser-dot green"></div>
            <div class="browser-url">https://app.enpharchem.com/dashboard</div>
        </div>
        <div class="dash-layout">
            <!-- Sidebar -->
            <div class="dash-sidebar">
                <div class="dash-sidebar-brand">En<span>Phar</span>Chem</div>
                <div class="dash-nav-item active"><i class="fas fa-th-large"></i> Dashboard</div>
                <div class="dash-nav-item"><i class="fas fa-project-diagram"></i> Projects</div>
                <div class="dash-nav-item"><i class="fas fa-flask"></i> Simulations</div>
                <div class="dash-nav-item"><i class="fas fa-cubes"></i> Modules</div>
                <div class="dash-nav-item"><i class="fas fa-chart-line"></i> Analytics</div>
                <div class="dash-nav-item"><i class="fas fa-exchange-alt"></i> Exchangers</div>
                <div class="dash-nav-item"><i class="fas fa-industry"></i> Operations</div>
                <div class="dash-nav-item"><i class="fas fa-bolt"></i> Grid Mgmt</div>
                <div class="dash-nav-item"><i class="fas fa-cog"></i> Settings</div>
            </div>
            <!-- Main Content -->
            <div class="dash-main">
                <div class="dash-topbar">
                    <h3>Dashboard Overview</h3>
                    <div class="user-pill"><div class="user-dot"></div> admin@enpharchem.com</div>
                </div>

                <!-- Stats Row -->
                <div class="dash-stats">
                    <div class="dash-stat">
                        <div class="label">Active Projects</div>
                        <div class="num">124</div>
                        <div class="change"><i class="fas fa-arrow-up"></i> 12% this month</div>
                    </div>
                    <div class="dash-stat">
                        <div class="label">Simulations Run</div>
                        <div class="num">89</div>
                        <div class="change"><i class="fas fa-arrow-up"></i> 8% this week</div>
                    </div>
                    <div class="dash-stat">
                        <div class="label">Available Modules</div>
                        <div class="num">115</div>
                        <div class="change"><i class="fas fa-check"></i> All active</div>
                    </div>
                </div>

                <!-- Module Category Grid -->
                <div class="dash-grid">
                    <div class="dash-module-card">
                        <div class="mod-icon" style="background:linear-gradient(135deg,#ef4444,#f97316);"><i class="fas fa-fire"></i></div>
                        <div class="mod-name">Energy Sim</div>
                    </div>
                    <div class="dash-module-card">
                        <div class="mod-icon" style="background:linear-gradient(135deg,#8b5cf6,#a78bfa);"><i class="fas fa-atom"></i></div>
                        <div class="mod-name">Chemical Sim</div>
                    </div>
                    <div class="dash-module-card">
                        <div class="mod-icon" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);"><i class="fas fa-exchange-alt"></i></div>
                        <div class="mod-name">Exchangers</div>
                    </div>
                    <div class="dash-module-card">
                        <div class="mod-icon" style="background:linear-gradient(135deg,#10b981,#34d399);"><i class="fas fa-globe-americas"></i></div>
                        <div class="mod-name">Subsurface</div>
                    </div>
                    <div class="dash-module-card">
                        <div class="mod-icon" style="background:linear-gradient(135deg,#ec4899,#f472b6);"><i class="fas fa-sliders-h"></i></div>
                        <div class="mod-name">APC</div>
                    </div>
                    <div class="dash-module-card">
                        <div class="mod-icon" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8);"><i class="fas fa-boxes"></i></div>
                        <div class="mod-name">Supply Chain</div>
                    </div>
                    <div class="dash-module-card">
                        <div class="mod-icon" style="background:linear-gradient(135deg,#d946ef,#e879f9);"><i class="fas fa-heartbeat"></i></div>
                        <div class="mod-name">APM</div>
                    </div>
                    <div class="dash-module-card">
                        <div class="mod-icon" style="background:linear-gradient(135deg,#eab308,#facc15);"><i class="fas fa-bolt"></i></div>
                        <div class="mod-name">Digital Grid</div>
                    </div>
                </div>

                <!-- Recent Projects Table -->
                <table class="dash-table">
                    <thead><tr><th>Project</th><th>Type</th><th>Status</th><th>Updated</th></tr></thead>
                    <tbody>
                        <tr><td style="color:#fff;">Ethylene Cracker Optimization</td><td>HYSYS Simulation</td><td><span class="status-dot" style="background:var(--success);"></span>Active</td><td>2 hours ago</td></tr>
                        <tr><td style="color:#fff;">Refinery Heat Integration</td><td>Exchanger Design</td><td><span class="status-dot" style="background:var(--success);"></span>Active</td><td>5 hours ago</td></tr>
                        <tr><td style="color:#fff;">API Batch Process Model</td><td>Batch Modeling</td><td><span class="status-dot" style="background:var(--warning);"></span>Review</td><td>1 day ago</td></tr>
                        <tr><td style="color:#fff;">Grid Stability Analysis</td><td>SCADA / EMS</td><td><span class="status-dot" style="background:var(--accent);"></span>Running</td><td>3 hours ago</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="screenshot-caption">EnPharChem Dashboard &mdash; Real-time overview of all engineering operations</div>
</div>

<!-- ============================================================
     PAGE 4 - MODULE CATEGORIES (1-9)
     ============================================================ -->
<div class="page page-light page-break">
    <div class="section-header gradient-text">Module Categories</div>
    <div class="section-sub">15 comprehensive categories covering every aspect of energy, pharmaceutical, and chemical engineering.</div>
    <div class="section-divider"></div>

    <div class="cat-grid">
        <!-- 1. Process Simulation for Energy -->
        <div class="cat-card c1">
            <div class="cat-icon"><i class="fas fa-fire"></i></div>
            <h5>Process Simulation for Energy</h5>
            <div class="cat-count">18 Modules</div>
            <div class="cat-desc">Comprehensive energy process modeling from upstream to downstream, including refining and gas processing.</div>
            <ul class="cat-modules">
                <li>HYSYS Process Simulator</li>
                <li>Acid Gas Cleaning</li>
                <li>BLOWDOWN Analysis</li>
                <li>Sulsim Sulfur Recovery</li>
                <li>Petroleum Refining</li>
            </ul>
        </div>

        <!-- 2. Process Simulation for Chemicals -->
        <div class="cat-card c2">
            <div class="cat-icon"><i class="fas fa-atom"></i></div>
            <h5>Process Simulation for Chemicals</h5>
            <div class="cat-count">18 Modules</div>
            <div class="cat-desc">Advanced chemical process simulation with specialized models for polymers, pharma, and specialty chemicals.</div>
            <ul class="cat-modules">
                <li>Plus Process Simulator</li>
                <li>Adsorption Modeling</li>
                <li>Chromatography</li>
                <li>Polymers Plus</li>
                <li>Batch Modeling</li>
            </ul>
        </div>

        <!-- 3. Exchanger Design & Rating -->
        <div class="cat-card c3">
            <div class="cat-icon"><i class="fas fa-exchange-alt"></i></div>
            <h5>Exchanger Design &amp; Rating</h5>
            <div class="cat-count">7 Modules</div>
            <div class="cat-desc">Complete heat exchanger design, rating, and simulation for all major exchanger types.</div>
            <ul class="cat-modules">
                <li>Shell &amp; Tube Exchanger</li>
                <li>Air Cooled Exchanger</li>
                <li>Fired Heater</li>
                <li>Plate Exchanger</li>
                <li>Plate Fin &amp; Coil Wound</li>
            </ul>
        </div>

        <!-- 4. Concurrent FEED -->
        <div class="cat-card c4">
            <div class="cat-icon"><i class="fas fa-drafting-compass"></i></div>
            <h5>Concurrent FEED</h5>
            <div class="cat-count">7 Modules</div>
            <div class="cat-desc">Front-end engineering design tools for cost estimation, 3D layout, and basic engineering packages.</div>
            <ul class="cat-modules">
                <li>Fidelis Reliability</li>
                <li>Capital Cost Estimator</li>
                <li>OptiPlant 3D Layout</li>
                <li>OptiRouter Piping</li>
                <li>Basic Engineering</li>
            </ul>
        </div>

        <!-- 5. Subsurface Science -->
        <div class="cat-card c5">
            <div class="cat-icon"><i class="fas fa-globe-americas"></i></div>
            <h5>Subsurface Science</h5>
            <div class="cat-count">11 Modules</div>
            <div class="cat-desc">Reservoir modeling, seismic processing, geological interpretation, and petrophysical analysis.</div>
            <ul class="cat-modules">
                <li>ESI Reservoir Simulator</li>
                <li>Echos Reservoir</li>
                <li>GeoDepth Seismic</li>
                <li>SKUA-GOCAD Modeling</li>
                <li>RMS Geological Modeling</li>
            </ul>
        </div>

        <!-- 6. Energy & Utilities Optimization -->
        <div class="cat-card c6">
            <div class="cat-icon"><i class="fas fa-leaf"></i></div>
            <h5>Energy &amp; Utilities Optimization</h5>
            <div class="cat-count">3 Modules</div>
            <div class="cat-desc">Optimize energy consumption, plan sustainability pathways, and manage utility systems.</div>
            <ul class="cat-modules">
                <li>Energy Analyzer</li>
                <li>Sustainability Pathways</li>
                <li>Utilities Planner</li>
            </ul>
        </div>

        <!-- 7. Operations Support -->
        <div class="cat-card c7">
            <div class="cat-icon"><i class="fas fa-headset"></i></div>
            <h5>Operations Support</h5>
            <div class="cat-count">2 Modules</div>
            <div class="cat-desc">Real-time process monitoring and operator training simulation tools.</div>
            <ul class="cat-modules">
                <li>OnLine Real-Time Optimization</li>
                <li>Simulation Workbook</li>
            </ul>
        </div>

        <!-- 8. Advanced Process Control -->
        <div class="cat-card c8">
            <div class="cat-icon"><i class="fas fa-sliders-h"></i></div>
            <h5>Advanced Process Control</h5>
            <div class="cat-count">7 Modules</div>
            <div class="cat-desc">Multivariable predictive control, event analytics, and nonlinear optimization for process plants.</div>
            <ul class="cat-modules">
                <li>DMC3 Controller</li>
                <li>EVA Event Analytics</li>
                <li>Inferential Qualities</li>
                <li>Nonlinear Controller</li>
                <li>APC Diagnostic Monitor</li>
            </ul>
        </div>

        <!-- 9. Dynamic Optimization -->
        <div class="cat-card c9">
            <div class="cat-icon"><i class="fas fa-chart-area"></i></div>
            <h5>Dynamic Optimization</h5>
            <div class="cat-count">1 Module</div>
            <div class="cat-desc">Global dynamic optimization for transition management, startup, and shutdown optimization.</div>
            <ul class="cat-modules">
                <li>GDOT Global Dynamic Optimizer</li>
            </ul>
        </div>
    </div>
</div>

<!-- ============================================================
     PAGE 5 - MODULE CATEGORIES (10-15)
     ============================================================ -->
<div class="page page-light page-break">
    <div class="section-header gradient-text">Module Categories <span style="font-size:14px;font-weight:400;color:var(--muted);">(continued)</span></div>
    <div class="section-divider"></div>

    <div class="cat-grid">
        <!-- 10. MES -->
        <div class="cat-card c10">
            <div class="cat-icon"><i class="fas fa-industry"></i></div>
            <h5>Manufacturing Execution Systems</h5>
            <div class="cat-count">8 Modules</div>
            <div class="cat-desc">Real-time production management, data historians, and process information systems.</div>
            <ul class="cat-modules">
                <li>InfoPlus.21 Data Historian</li>
                <li>Production Execution Manager</li>
                <li>Process Explorer</li>
                <li>Batch Management</li>
                <li>Recipe Management</li>
            </ul>
        </div>

        <!-- 11. Petroleum Supply Chain -->
        <div class="cat-card c11">
            <div class="cat-icon"><i class="fas fa-gas-pump"></i></div>
            <h5>Petroleum Supply Chain</h5>
            <div class="cat-count">11 Modules</div>
            <div class="cat-desc">End-to-end petroleum supply chain planning from crude oil assay to product blending and distribution.</div>
            <ul class="cat-modules">
                <li>Unified PIMS Planning</li>
                <li>Scheduling &amp; Optimization</li>
                <li>Multi-Blend Optimizer</li>
                <li>Assay Management</li>
                <li>Crude Flexibility Analyzer</li>
            </ul>
        </div>

        <!-- 12. Supply Chain Management -->
        <div class="cat-card c12">
            <div class="cat-icon"><i class="fas fa-boxes"></i></div>
            <h5>Supply Chain Management</h5>
            <div class="cat-count">6 Modules</div>
            <div class="cat-desc">Chemical and specialty supply chain optimization including demand planning and scheduling.</div>
            <ul class="cat-modules">
                <li>SCM Planning Suite</li>
                <li>Scheduler Explorer</li>
                <li>Demand Manager</li>
                <li>Plant Scheduler</li>
                <li>Inventory Optimization</li>
            </ul>
        </div>

        <!-- 13. APM -->
        <div class="cat-card c13">
            <div class="cat-icon"><i class="fas fa-heartbeat"></i></div>
            <h5>Asset Performance Management</h5>
            <div class="cat-count">4 Modules</div>
            <div class="cat-desc">Predictive maintenance, multivariate analytics, and real-time asset health monitoring.</div>
            <ul class="cat-modules">
                <li>Mtell Predictive Analytics</li>
                <li>ProMV Multivariate Analysis</li>
                <li>Process Pulse Monitoring</li>
                <li>Unscrambler Chemometrics</li>
            </ul>
        </div>

        <!-- 14. Industrial Data Fabric -->
        <div class="cat-card c14">
            <div class="cat-icon"><i class="fas fa-database"></i></div>
            <h5>Industrial Data Fabric</h5>
            <div class="cat-count">1 Module</div>
            <div class="cat-desc">Unified data connectivity layer for real-time aggregation, contextualization, and distribution of industrial data.</div>
            <ul class="cat-modules">
                <li>Inmation Data Hub</li>
            </ul>
        </div>

        <!-- 15. Digital Grid Management -->
        <div class="cat-card c15">
            <div class="cat-icon"><i class="fas fa-bolt"></i></div>
            <h5>Digital Grid Management</h5>
            <div class="cat-count">14 Modules</div>
            <div class="cat-desc">Complete power grid management from SCADA to advanced distribution and renewable integration.</div>
            <ul class="cat-modules">
                <li>SCADA Control System</li>
                <li>EMS Energy Management</li>
                <li>ADMS Distribution Mgmt</li>
                <li>DERMS Distributed Energy</li>
                <li>Microgrid &amp; CHRONUS</li>
            </ul>
        </div>
    </div>

    <!-- Module Count Summary -->
    <div style="margin-top:24px;background:linear-gradient(135deg,var(--dark),var(--dark2));border-radius:12px;padding:18px 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div style="color:#fff;font-size:14px;font-weight:700;"><i class="fas fa-cubes" style="color:var(--accent);margin-right:8px;"></i>Total Platform Coverage</div>
        <div style="display:flex;gap:20px;">
            <div style="text-align:center;">
                <div style="font-size:22px;font-weight:900;color:var(--accent);">115+</div>
                <div style="font-size:8px;color:#8aa8c8;text-transform:uppercase;letter-spacing:.5px;">Modules</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:22px;font-weight:900;color:var(--primary);">15</div>
                <div style="font-size:8px;color:#8aa8c8;text-transform:uppercase;letter-spacing:.5px;">Categories</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:22px;font-weight:900;color:var(--success);">3</div>
                <div style="font-size:8px;color:#8aa8c8;text-transform:uppercase;letter-spacing:.5px;">Industries</div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     PAGE 6 - MODULE SCREENSHOTS
     ============================================================ -->
<div class="page page-dark page-break">
    <div class="section-header white">Module Interfaces</div>
    <div class="section-sub light">Explore the powerful, dark-themed interfaces designed for engineering professionals.</div>
    <div class="section-divider"></div>

    <div class="screenshots-grid">

        <!-- 1. HYSYS Flowsheet -->
        <div class="screenshot-wrap">
            <div class="mini-browser">
                <div class="mini-browser-bar">
                    <div class="mini-dot r"></div><div class="mini-dot y"></div><div class="mini-dot g"></div>
                    <div class="mini-title">EnPharChem HYSYS &mdash; Process Flowsheet</div>
                </div>
                <div class="mini-content">
                    <div class="flow-canvas">
                        <!-- Flow boxes -->
                        <div class="flow-box" style="left:8px;top:20px;width:60px;">
                            <i class="fas fa-arrow-right" style="color:var(--accent);font-size:10px;"></i><br>Feed
                        </div>
                        <div class="flow-box" style="left:90px;top:10px;width:72px;border-color:var(--success);">
                            <i class="fas fa-flask" style="color:var(--success);font-size:10px;"></i><br>Reactor
                            <div style="font-size:6px;color:#666;margin-top:2px;">350&deg;C / 45 bar</div>
                        </div>
                        <div class="flow-box" style="left:90px;top:80px;width:72px;border-color:var(--warning);">
                            <i class="fas fa-filter" style="color:var(--warning);font-size:10px;"></i><br>Separator
                            <div style="font-size:6px;color:#666;margin-top:2px;">Flash V-L</div>
                        </div>
                        <div class="flow-box" style="left:185px;top:10px;width:60px;border-color:var(--accent);">
                            <i class="fas fa-cube" style="color:var(--accent);font-size:10px;"></i><br>Product
                        </div>
                        <div class="flow-box" style="left:185px;top:80px;width:60px;border-color:#ef4444;">
                            <i class="fas fa-recycle" style="color:#ef4444;font-size:10px;"></i><br>Recycle
                        </div>
                        <!-- Connection lines -->
                        <div style="position:absolute;left:68px;top:35px;width:22px;height:2px;background:var(--accent);"></div>
                        <div style="position:absolute;left:126px;top:55px;width:2px;height:25px;background:var(--warning);"></div>
                        <div style="position:absolute;left:162px;top:30px;width:23px;height:2px;background:var(--success);"></div>
                        <div style="position:absolute;left:162px;top:95px;width:23px;height:2px;background:#ef4444;"></div>

                        <!-- Sidebar component list -->
                        <div class="flow-sidebar">
                            <div style="color:var(--accent);font-weight:700;margin-bottom:6px;font-size:8px;">Components</div>
                            <div class="comp">Methane</div>
                            <div class="comp">Ethane</div>
                            <div class="comp">Propane</div>
                            <div class="comp">n-Butane</div>
                            <div class="comp">Ethylene</div>
                            <div class="comp">H2O</div>
                            <div class="comp">CO2</div>
                            <div class="comp" style="color:var(--accent);">+ 12 more</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Plus Chemical Simulation -->
        <div class="screenshot-wrap">
            <div class="mini-browser">
                <div class="mini-browser-bar">
                    <div class="mini-dot r"></div><div class="mini-dot y"></div><div class="mini-dot g"></div>
                    <div class="mini-title">EnPharChem Plus &mdash; Chemical Simulation</div>
                </div>
                <div class="mini-content">
                    <div style="font-size:8px;color:var(--accent);font-weight:700;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px;">Stream Results &mdash; Material Balance</div>
                    <table class="sim-table">
                        <thead><tr><th>Component</th><th>Feed (kg/h)</th><th>T (&deg;C)</th><th>P (bar)</th><th>Phase</th></tr></thead>
                        <tbody>
                            <tr><td style="color:#fff;">Acetic Acid</td><td>1,250.4</td><td>118.1</td><td>1.013</td><td><span style="color:var(--success);">Liquid</span></td></tr>
                            <tr><td style="color:#fff;">Methanol</td><td>842.7</td><td>64.7</td><td>1.013</td><td><span style="color:var(--success);">Liquid</span></td></tr>
                            <tr><td style="color:#fff;">Water</td><td>2,105.0</td><td>100.0</td><td>1.013</td><td><span style="color:var(--accent);">Mixed</span></td></tr>
                            <tr><td style="color:#fff;">Methyl Acetate</td><td>456.3</td><td>57.0</td><td>1.013</td><td><span style="color:var(--success);">Liquid</span></td></tr>
                            <tr><td style="color:#fff;">Sulfuric Acid</td><td>12.8</td><td>337.0</td><td>1.013</td><td><span style="color:var(--warning);">Catalyst</span></td></tr>
                            <tr><td style="color:#fff;">CO2 (off-gas)</td><td>28.1</td><td>25.0</td><td>1.013</td><td><span style="color:#ef4444;">Vapor</span></td></tr>
                        </tbody>
                    </table>
                    <div style="margin-top:8px;display:flex;gap:12px;">
                        <div style="background:#1e2130;border-radius:4px;padding:4px 8px;font-size:7px;">
                            <span style="color:#666;">Convergence:</span> <span style="color:var(--success);">Converged</span>
                        </div>
                        <div style="background:#1e2130;border-radius:4px;padding:4px 8px;font-size:7px;">
                            <span style="color:#666;">Iterations:</span> <span style="color:var(--accent);">24</span>
                        </div>
                        <div style="background:#1e2130;border-radius:4px;padding:4px 8px;font-size:7px;">
                            <span style="color:#666;">Error:</span> <span style="color:var(--success);">1.2e-06</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Exchanger Design -->
        <div class="screenshot-wrap">
            <div class="mini-browser">
                <div class="mini-browser-bar">
                    <div class="mini-dot r"></div><div class="mini-dot y"></div><div class="mini-dot g"></div>
                    <div class="mini-title">Exchanger Design &mdash; Shell &amp; Tube</div>
                </div>
                <div class="mini-content">
                    <table class="specs-table" style="margin-bottom:6px;">
                        <thead><tr><th>Parameter</th><th>Shell Side</th><th>Tube Side</th></tr></thead>
                        <tbody>
                            <tr><td style="color:#ccc;">Fluid</td><td>Crude Oil</td><td>Steam</td></tr>
                            <tr><td style="color:#ccc;">Inlet Temp (&deg;C)</td><td>25.0</td><td>250.0</td></tr>
                            <tr><td style="color:#ccc;">Outlet Temp (&deg;C)</td><td>180.0</td><td>210.0</td></tr>
                            <tr><td style="color:#ccc;">Flow Rate (kg/h)</td><td>45,000</td><td>12,500</td></tr>
                            <tr><td style="color:#ccc;">Pressure Drop (kPa)</td><td>35.2</td><td>22.8</td></tr>
                        </tbody>
                    </table>
                    <div class="result-panel">
                        <div class="rp-title"><i class="fas fa-check-circle" style="color:var(--success);margin-right:4px;"></i> Heat Duty Results</div>
                        <div class="rp-row"><span class="rp-label">Heat Duty</span><span class="rp-val">8.45 MW</span></div>
                        <div class="rp-row"><span class="rp-label">U (Overall)</span><span class="rp-val">385 W/m&sup2;K</span></div>
                        <div class="rp-row"><span class="rp-label">LMTD</span><span class="rp-val">72.3 &deg;C</span></div>
                        <div class="rp-row"><span class="rp-label">Area Required</span><span class="rp-val">302.4 m&sup2;</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. APC DMC3 Controller -->
        <div class="screenshot-wrap">
            <div class="mini-browser">
                <div class="mini-browser-bar">
                    <div class="mini-dot r"></div><div class="mini-dot y"></div><div class="mini-dot g"></div>
                    <div class="mini-title">APC &mdash; DMC3 Controller Dashboard</div>
                </div>
                <div class="mini-content">
                    <div style="display:flex;gap:8px;margin-bottom:10px;">
                        <div style="flex:1;">
                            <div style="font-size:7px;color:#666;text-transform:uppercase;margin-bottom:4px;">Controller Performance</div>
                            <div class="bar-chart">
                                <div class="bar-col"><div class="bar-fill" style="height:75%;background:linear-gradient(180deg,var(--success),#059669);"></div><div class="bar-label">CV1</div></div>
                                <div class="bar-col"><div class="bar-fill" style="height:88%;background:linear-gradient(180deg,var(--success),#059669);"></div><div class="bar-label">CV2</div></div>
                                <div class="bar-col"><div class="bar-fill" style="height:62%;background:linear-gradient(180deg,var(--warning),#d97706);"></div><div class="bar-label">CV3</div></div>
                                <div class="bar-col"><div class="bar-fill" style="height:95%;background:linear-gradient(180deg,var(--success),#059669);"></div><div class="bar-label">CV4</div></div>
                                <div class="bar-col"><div class="bar-fill" style="height:70%;background:linear-gradient(180deg,var(--success),#059669);"></div><div class="bar-label">CV5</div></div>
                                <div class="bar-col"><div class="bar-fill" style="height:45%;background:linear-gradient(180deg,var(--danger),#dc2626);"></div><div class="bar-label">CV6</div></div>
                            </div>
                        </div>
                        <div style="width:100px;">
                            <div style="font-size:7px;color:#666;text-transform:uppercase;margin-bottom:6px;">Status</div>
                            <div class="status-row"><div class="status-indicator on"></div><span class="status-label">DMC3-01</span><span class="status-value">ON</span></div>
                            <div class="status-row"><div class="status-indicator on"></div><span class="status-label">DMC3-02</span><span class="status-value">ON</span></div>
                            <div class="status-row"><div class="status-indicator warn"></div><span class="status-label">DMC3-03</span><span class="status-value">LIM</span></div>
                            <div class="status-row"><div class="status-indicator on"></div><span class="status-label">DMC3-04</span><span class="status-value">ON</span></div>
                        </div>
                    </div>
                    <div style="display:flex;gap:6px;">
                        <div style="flex:1;background:#1e2130;border-radius:4px;padding:6px;text-align:center;">
                            <div style="font-size:16px;font-weight:800;color:var(--success);">94.2%</div>
                            <div style="font-size:7px;color:#666;">Uptime</div>
                        </div>
                        <div style="flex:1;background:#1e2130;border-radius:4px;padding:6px;text-align:center;">
                            <div style="font-size:16px;font-weight:800;color:var(--accent);">$1.2M</div>
                            <div style="font-size:7px;color:#666;">Annual Benefit</div>
                        </div>
                        <div style="flex:1;background:#1e2130;border-radius:4px;padding:6px;text-align:center;">
                            <div style="font-size:16px;font-weight:800;color:var(--warning);">3</div>
                            <div style="font-size:7px;color:#666;">Alerts</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. MES Process Explorer -->
        <div class="screenshot-wrap">
            <div class="mini-browser">
                <div class="mini-browser-bar">
                    <div class="mini-dot r"></div><div class="mini-dot y"></div><div class="mini-dot g"></div>
                    <div class="mini-title">MES &mdash; Process Explorer Trending</div>
                </div>
                <div class="mini-content">
                    <div style="display:flex;gap:6px;margin-bottom:8px;">
                        <div style="font-size:7px;color:var(--primary);"><i class="fas fa-circle" style="font-size:5px;"></i> Temperature</div>
                        <div style="font-size:7px;color:var(--success);"><i class="fas fa-circle" style="font-size:5px;"></i> Pressure</div>
                        <div style="font-size:7px;color:var(--warning);"><i class="fas fa-circle" style="font-size:5px;"></i> Flow Rate</div>
                        <div style="font-size:7px;color:#ef4444;"><i class="fas fa-circle" style="font-size:5px;"></i> Level</div>
                    </div>
                    <div class="trend-area">
                        <div class="trend-grid-line" style="top:25%;"></div>
                        <div class="trend-grid-line" style="top:50%;"></div>
                        <div class="trend-grid-line" style="top:75%;"></div>
                        <!-- Simulated trend lines using CSS gradients -->
                        <div class="trend-line" style="top:30%;background:var(--primary);height:2px;clip-path:polygon(0 80%,10% 40%,20% 60%,30% 20%,40% 45%,50% 30%,60% 55%,70% 25%,80% 40%,90% 20%,100% 35%);height:60px;opacity:.7;"></div>
                        <div class="trend-line" style="top:40%;background:var(--success);height:2px;clip-path:polygon(0 50%,10% 55%,20% 45%,30% 60%,40% 50%,50% 55%,60% 40%,70% 55%,80% 45%,90% 50%,100% 48%);height:60px;opacity:.7;"></div>
                        <div class="trend-line" style="top:20%;background:var(--warning);height:2px;clip-path:polygon(0 60%,10% 70%,20% 50%,30% 75%,40% 60%,50% 80%,60% 65%,70% 70%,80% 55%,90% 65%,100% 70%);height:60px;opacity:.7;"></div>
                        <div class="trend-line" style="top:50%;background:#ef4444;height:2px;clip-path:polygon(0 40%,10% 35%,20% 42%,30% 38%,40% 40%,50% 36%,60% 42%,70% 38%,80% 40%,90% 37%,100% 39%);height:60px;opacity:.7;"></div>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:6px;color:#555;padding-top:3px;">
                        <span>00:00</span><span>04:00</span><span>08:00</span><span>12:00</span><span>16:00</span><span>20:00</span><span>24:00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. SCADA Overview -->
        <div class="screenshot-wrap">
            <div class="mini-browser">
                <div class="mini-browser-bar">
                    <div class="mini-dot r"></div><div class="mini-dot y"></div><div class="mini-dot g"></div>
                    <div class="mini-title">Digital Grid &mdash; SCADA Overview</div>
                </div>
                <div class="mini-content">
                    <div class="scada-grid">
                        <div class="scada-node gen">
                            <div class="node-icon"><i class="fas fa-solar-panel"></i></div>
                            <div class="node-label">Solar Farm A</div>
                            <div class="node-val" style="color:var(--success);">45 MW</div>
                        </div>
                        <div class="scada-node sub">
                            <div class="node-icon"><i class="fas fa-charging-station"></i></div>
                            <div class="node-label">Substation 1</div>
                            <div class="node-val" style="color:var(--warning);">138 kV</div>
                        </div>
                        <div class="scada-node load">
                            <div class="node-icon"><i class="fas fa-city"></i></div>
                            <div class="node-label">Industrial Load</div>
                            <div class="node-val" style="color:var(--accent);">32 MW</div>
                        </div>
                        <div class="scada-node xfmr">
                            <div class="node-icon"><i class="fas fa-random"></i></div>
                            <div class="node-label">Transformer T1</div>
                            <div class="node-val" style="color:#a78bfa;">69 kV</div>
                        </div>

                        <div class="scada-line-h"></div>

                        <div class="scada-node gen">
                            <div class="node-icon"><i class="fas fa-wind"></i></div>
                            <div class="node-label">Wind Farm B</div>
                            <div class="node-val" style="color:var(--success);">78 MW</div>
                        </div>
                        <div class="scada-node sub">
                            <div class="node-icon"><i class="fas fa-charging-station"></i></div>
                            <div class="node-label">Substation 2</div>
                            <div class="node-val" style="color:var(--warning);">230 kV</div>
                        </div>
                        <div class="scada-node load">
                            <div class="node-icon"><i class="fas fa-hospital"></i></div>
                            <div class="node-label">Commercial</div>
                            <div class="node-val" style="color:var(--accent);">18 MW</div>
                        </div>
                        <div class="scada-node gen">
                            <div class="node-icon"><i class="fas fa-battery-three-quarters"></i></div>
                            <div class="node-label">BESS Unit</div>
                            <div class="node-val" style="color:var(--success);">25 MWh</div>
                        </div>
                    </div>
                    <div style="margin-top:8px;display:flex;gap:8px;">
                        <div style="flex:1;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.2);border-radius:4px;padding:4px;text-align:center;">
                            <div style="font-size:10px;font-weight:800;color:var(--success);">99.7%</div>
                            <div style="font-size:6px;color:#666;">Grid Reliability</div>
                        </div>
                        <div style="flex:1;background:rgba(13,202,240,.1);border:1px solid rgba(13,202,240,.2);border-radius:4px;padding:4px;text-align:center;">
                            <div style="font-size:10px;font-weight:800;color:var(--accent);">148 MW</div>
                            <div style="font-size:6px;color:#666;">Total Generation</div>
                        </div>
                        <div style="flex:1;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);border-radius:4px;padding:4px;text-align:center;">
                            <div style="font-size:10px;font-weight:800;color:var(--warning);">0</div>
                            <div style="font-size:6px;color:#666;">Active Alarms</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="screenshot-caption" style="color:#8aa8c8;margin-top:16px;">All interfaces feature dark-themed, high-contrast designs optimized for engineering workflows</div>
</div>

<!-- ============================================================
     PAGE 7 - GARTNER BENCHMARK
     ============================================================ -->
<div class="page page-dark page-break">
    <div class="section-header white">Gartner Benchmark Performance</div>
    <div class="section-sub light">Independent analysis confirms EnPharChem's leadership across all evaluation dimensions.</div>
    <div class="section-divider"></div>

    <!-- Overall Score -->
    <div class="overall-score-box">
        <div class="os-label">Overall Gartner Score</div>
        <div class="os-scores">
            <div class="os-score os-ep">
                <span class="big">4.65</span>
                <span class="tag">EnPharChem</span>
            </div>
            <div class="os-vs">vs</div>
            <div class="os-score os-at">
                <span class="big">4.21</span>
                <span class="tag">AspenTech</span>
            </div>
        </div>
    </div>

    <!-- Benchmark Table -->
    <table class="benchmark-table">
        <thead>
            <tr>
                <th>Evaluation Dimension</th>
                <th>EnPharChem</th>
                <th>AspenTech</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="score-bar-wrap">
                        <span style="width:100px;display:inline-block;">Innovation</span>
                        <div class="score-bar ep" style="width:94px;"></div>
                    </div>
                </td>
                <td style="color:var(--accent);">4.70</td>
                <td style="color:#888;">4.50</td>
            </tr>
            <tr>
                <td>
                    <div class="score-bar-wrap">
                        <span style="width:100px;display:inline-block;">User Experience</span>
                        <div class="score-bar ep" style="width:96px;"></div>
                    </div>
                </td>
                <td style="color:var(--accent);">4.80</td>
                <td style="color:#888;">3.80</td>
            </tr>
            <tr>
                <td>
                    <div class="score-bar-wrap">
                        <span style="width:100px;display:inline-block;">Total Cost of Ownership</span>
                        <div class="score-bar ep" style="width:94px;"></div>
                    </div>
                </td>
                <td style="color:var(--accent);">4.70</td>
                <td style="color:#888;">3.50</td>
            </tr>
            <tr>
                <td>
                    <div class="score-bar-wrap">
                        <span style="width:100px;display:inline-block;">Module Breadth</span>
                        <div class="score-bar ep" style="width:92px;"></div>
                    </div>
                </td>
                <td style="color:var(--accent);">4.60</td>
                <td style="color:#888;">4.70</td>
            </tr>
            <tr>
                <td>
                    <div class="score-bar-wrap">
                        <span style="width:100px;display:inline-block;">Integration</span>
                        <div class="score-bar ep" style="width:90px;"></div>
                    </div>
                </td>
                <td style="color:var(--accent);">4.50</td>
                <td style="color:#888;">4.00</td>
            </tr>
            <tr>
                <td>
                    <div class="score-bar-wrap">
                        <span style="width:100px;display:inline-block;">Cloud Readiness</span>
                        <div class="score-bar ep" style="width:94px;"></div>
                    </div>
                </td>
                <td style="color:var(--accent);">4.70</td>
                <td style="color:#888;">3.90</td>
            </tr>
            <tr>
                <td>
                    <div class="score-bar-wrap">
                        <span style="width:100px;display:inline-block;">Support Quality</span>
                        <div class="score-bar ep" style="width:92px;"></div>
                    </div>
                </td>
                <td style="color:var(--accent);">4.60</td>
                <td style="color:#888;">4.20</td>
            </tr>
            <tr>
                <td>
                    <div class="score-bar-wrap">
                        <span style="width:100px;display:inline-block;">Scalability</span>
                        <div class="score-bar ep" style="width:92px;"></div>
                    </div>
                </td>
                <td style="color:var(--accent);">4.60</td>
                <td style="color:#888;">4.30</td>
            </tr>
        </tbody>
    </table>

    <!-- Key Wins -->
    <div style="margin-top:20px;">
        <h4 style="font-size:14px;font-weight:700;color:#c8deff;margin-bottom:14px;"><i class="fas fa-trophy" style="color:var(--warning);margin-right:8px;"></i>Key Competitive Wins</h4>
        <div class="key-win">
            <div class="kw-label"><i class="fas fa-lightbulb" style="color:var(--warning);margin-right:8px;"></i>Innovation &amp; Technology</div>
            <div class="kw-scores">
                <div class="kw-score kw-ep"><span class="num">4.7</span><span class="who" style="color:var(--accent);">EnPharChem</span></div>
                <div class="kw-score kw-at"><span class="num">4.5</span><span class="who">AspenTech</span></div>
            </div>
        </div>
        <div class="key-win">
            <div class="kw-label"><i class="fas fa-paint-brush" style="color:var(--accent);margin-right:8px;"></i>User Experience &amp; Design</div>
            <div class="kw-scores">
                <div class="kw-score kw-ep"><span class="num">4.8</span><span class="who" style="color:var(--accent);">EnPharChem</span></div>
                <div class="kw-score kw-at"><span class="num">3.8</span><span class="who">AspenTech</span></div>
            </div>
        </div>
        <div class="key-win">
            <div class="kw-label"><i class="fas fa-dollar-sign" style="color:var(--success);margin-right:8px;"></i>Total Cost of Ownership</div>
            <div class="kw-scores">
                <div class="kw-score kw-ep"><span class="num">4.7</span><span class="who" style="color:var(--accent);">EnPharChem</span></div>
                <div class="kw-score kw-at"><span class="num">3.5</span><span class="who">AspenTech</span></div>
            </div>
        </div>
    </div>

    <div style="text-align:center;margin-top:24px;">
        <a href="#" style="display:inline-block;background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;padding:10px 30px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;box-shadow:0 4px 15px rgba(13,110,253,.3);">
            <i class="fas fa-file-alt" style="margin-right:6px;"></i>Read Full Benchmark Report
        </a>
    </div>
</div>

<!-- ============================================================
     PAGE 8 - PRICING & EDITIONS
     ============================================================ -->
<div class="page page-light page-break">
    <div style="text-align:center;margin-bottom:30px;">
        <div class="section-header gradient-text" style="display:inline-block;">Pricing &amp; Editions</div>
        <div class="section-sub" style="margin:6px auto 0;text-align:center;">Flexible plans designed to scale with your engineering needs. Start free, upgrade anytime.</div>
        <div class="section-divider" style="margin:16px auto;"></div>
    </div>

    <div class="pricing-grid">
        <!-- Trial -->
        <div class="price-card">
            <div class="tier-name">Trial</div>
            <div class="tier-price"><span class="free">Free</span></div>
            <div class="tier-period">14 days, no credit card</div>
            <ul class="tier-features">
                <li><i class="fas fa-check"></i> 5 modules access</li>
                <li><i class="fas fa-check"></i> 1 user</li>
                <li><i class="fas fa-check"></i> Community support</li>
                <li><i class="fas fa-check"></i> Sample projects</li>
                <li><i class="fas fa-check"></i> Basic documentation</li>
                <li><i class="fas fa-times"></i> <span style="color:#bbb;">API access</span></li>
                <li><i class="fas fa-times"></i> <span style="color:#bbb;">Custom integrations</span></li>
            </ul>
            <a href="#" class="price-cta outline">Start Free Trial</a>
        </div>

        <!-- Standard -->
        <div class="price-card">
            <div class="tier-name">Standard</div>
            <div class="tier-price">$2,499<small>/mo</small></div>
            <div class="tier-period">Billed annually</div>
            <ul class="tier-features">
                <li><i class="fas fa-check"></i> 40 modules access</li>
                <li><i class="fas fa-check"></i> Up to 10 users</li>
                <li><i class="fas fa-check"></i> Email support (24h SLA)</li>
                <li><i class="fas fa-check"></i> Project management</li>
                <li><i class="fas fa-check"></i> Data export</li>
                <li><i class="fas fa-check"></i> Basic reporting</li>
                <li><i class="fas fa-times"></i> <span style="color:#bbb;">API access</span></li>
            </ul>
            <a href="#" class="price-cta outline">Get Started</a>
        </div>

        <!-- Professional (Featured) -->
        <div class="price-card featured">
            <div class="popular-badge">Most Popular</div>
            <div class="tier-name">Professional</div>
            <div class="tier-price">$6,999<small>/mo</small></div>
            <div class="tier-period">Billed annually</div>
            <ul class="tier-features">
                <li><i class="fas fa-check"></i> 80 modules access</li>
                <li><i class="fas fa-check"></i> Up to 50 users</li>
                <li><i class="fas fa-check"></i> Priority support (4h SLA)</li>
                <li><i class="fas fa-check"></i> Full API access</li>
                <li><i class="fas fa-check"></i> Advanced analytics</li>
                <li><i class="fas fa-check"></i> SSO integration</li>
                <li><i class="fas fa-check"></i> Custom dashboards</li>
            </ul>
            <a href="#" class="price-cta filled">Get Started</a>
        </div>

        <!-- Enterprise -->
        <div class="price-card">
            <div class="tier-name">Enterprise</div>
            <div class="tier-price">Custom</div>
            <div class="tier-period">Contact sales</div>
            <ul class="tier-features">
                <li><i class="fas fa-check"></i> All 115+ modules</li>
                <li><i class="fas fa-check"></i> Unlimited users</li>
                <li><i class="fas fa-check"></i> 24/7 dedicated support</li>
                <li><i class="fas fa-check"></i> Custom integrations</li>
                <li><i class="fas fa-check"></i> On-premise option</li>
                <li><i class="fas fa-check"></i> SLA guarantee</li>
                <li><i class="fas fa-check"></i> Training &amp; onboarding</li>
            </ul>
            <a href="#" class="price-cta outline">Contact Sales</a>
        </div>
    </div>

    <div style="text-align:center;margin-top:24px;font-size:11px;color:var(--muted);">
        <i class="fas fa-shield-alt" style="color:var(--primary);margin-right:4px;"></i>
        All plans include SOC 2 compliance, data encryption, and regular security audits.
    </div>
</div>

<!-- ============================================================
     PAGE 9 - CONTACT & CTA
     ============================================================ -->
<div class="page page-light page-break">
    <div class="cta-hero">
        <div style="width:70px;height:70px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:18px;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:900;color:#fff;margin:0 auto 24px;box-shadow:0 12px 40px rgba(13,110,253,.3);">EP</div>
        <h2>Get Started with EnPharChem</h2>
        <p>Join 500+ engineers worldwide who trust EnPharChem for their critical engineering workflows.</p>
        <a href="#" class="cta-btn"><i class="fas fa-rocket" style="margin-right:8px;"></i>Request a Demo</a>
    </div>

    <div class="contact-grid">
        <div class="contact-card">
            <div><i class="fas fa-envelope"></i></div>
            <h5>General Inquiries</h5>
            <p><a href="mailto:info@enpharchem.com">info@enpharchem.com</a></p>
        </div>
        <div class="contact-card">
            <div><i class="fas fa-phone"></i></div>
            <h5>Sales</h5>
            <p><a href="mailto:sales@enpharchem.com">sales@enpharchem.com</a></p>
            <p style="margin-top:4px;font-weight:700;color:var(--dark);">1-855-ENPHCHEM</p>
        </div>
        <div class="contact-card">
            <div><i class="fas fa-headset"></i></div>
            <h5>Technical Support</h5>
            <p><a href="mailto:support@enpharchem.com">support@enpharchem.com</a></p>
        </div>
    </div>

    <div class="social-proof">
        <span class="number">500+</span>
        Engineers worldwide trust EnPharChem for mission-critical operations
    </div>

    <div style="text-align:center;margin-top:20px;padding-top:20px;border-top:1px solid #e2e8f0;">
        <div style="display:inline-flex;align-items:center;gap:12px;">
            <div style="width:44px;height:44px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:900;color:#fff;">EP</div>
            <div style="text-align:left;">
                <div style="font-size:16px;font-weight:800;color:var(--dark);">EnPharChem Technologies</div>
                <div style="font-size:10px;color:var(--muted);">Engineering the future of Energy, Pharma &amp; Chemical</div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="brochure-footer">
    &copy; 2026 EnPharChem Technologies &nbsp;|&nbsp; www.enpharchem.com
</div>

</div><!-- /.brochure -->

</body>
</html>
