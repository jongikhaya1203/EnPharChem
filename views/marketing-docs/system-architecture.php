<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnPharChem - System Architecture Overview</title>
    <style>
        /* ========== PRINT & PAGE SETUP ========== */
        @page {
            size: A4;
            margin: 20mm 18mm 25mm 18mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #1a1a2e;
            background: #f0f2f5;
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        /* ========== PRINT BAR ========== */
        .print-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            padding: 12px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }

        .print-bar .bar-title {
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .print-bar .bar-actions {
            display: flex;
            gap: 10px;
        }

        .print-bar .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .print-bar .btn-back {
            background: rgba(255,255,255,0.12);
            color: #ffffff;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .print-bar .btn-back:hover {
            background: rgba(255,255,255,0.22);
        }

        .print-bar .btn-pdf {
            background: #0d6efd;
            color: #ffffff;
        }

        .print-bar .btn-pdf:hover {
            background: #0b5ed7;
        }

        @media print {
            .print-bar { display: none !important; }
            body { background: #ffffff; padding: 0; }
            .document { box-shadow: none; margin: 0; max-width: 100%; }
            .cover-page { page-break-after: always; }
            .section { page-break-inside: avoid; }
            h2 { page-break-after: avoid; }
            .diagram-box, .stack-diagram, .grid-categories { page-break-inside: avoid; }
        }

        /* ========== DOCUMENT CONTAINER ========== */
        .document {
            max-width: 210mm;
            margin: 70px auto 40px auto;
            background: #ffffff;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        }

        /* ========== COVER PAGE ========== */
        .cover-page {
            min-height: 297mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 60px 50px;
            background: linear-gradient(160deg, #ffffff 0%, #f8f9ff 40%, #eef4ff 100%);
            position: relative;
            overflow: hidden;
        }

        .cover-page::before {
            content: '';
            position: absolute;
            top: -120px;
            right: -120px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(13,106,253,0.06) 0%, transparent 70%);
        }

        .cover-page::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(13,202,240,0.06) 0%, transparent 70%);
        }

        .cover-logo {
            width: 120px;
            height: 120px;
            border-radius: 28px;
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 50%, #198754 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 40px;
            box-shadow: 0 8px 32px rgba(13,106,253,0.25);
            position: relative;
            z-index: 1;
        }

        .cover-logo span {
            font-size: 48px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -2px;
        }

        .cover-title {
            font-size: 36px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }

        .cover-subtitle {
            font-size: 22px;
            font-weight: 300;
            color: #0d6efd;
            margin-bottom: 50px;
            position: relative;
            z-index: 1;
        }

        .cover-meta {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
            align-items: center;
        }

        .cover-meta .meta-line {
            font-size: 13px;
            color: #6c757d;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .cover-meta .meta-line strong {
            color: #1a1a2e;
        }

        .cover-classification {
            margin-top: 50px;
            padding: 10px 30px;
            border: 2px solid #0d6efd;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            color: #0d6efd;
            letter-spacing: 2px;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
        }

        .cover-divider {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #0d6efd, #0dcaf0);
            border-radius: 2px;
            margin: 30px 0;
            position: relative;
            z-index: 1;
        }

        /* ========== CONTENT PAGES ========== */
        .content {
            padding: 50px 50px 40px 50px;
        }

        h2 {
            font-size: 22px;
            font-weight: 700;
            color: #0d6efd;
            margin-top: 50px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #0d6efd;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h2:first-child {
            margin-top: 0;
        }

        h2 .section-num {
            background: #0d6efd;
            color: #ffffff;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 700;
            flex-shrink: 0;
        }

        h3 {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a2e;
            margin-top: 28px;
            margin-bottom: 12px;
        }

        h3 .sub-num {
            color: #0d6efd;
            margin-right: 6px;
        }

        p {
            margin-bottom: 14px;
            color: #2d2d44;
            text-align: justify;
        }

        ul, ol {
            margin-bottom: 14px;
            padding-left: 24px;
        }

        li {
            margin-bottom: 6px;
            color: #2d2d44;
        }

        code {
            background: #f0f4f8;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 10pt;
            color: #0d6efd;
        }

        /* ========== STACK DIAGRAM ========== */
        .stack-diagram {
            display: flex;
            flex-direction: column;
            gap: 0;
            margin: 24px 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        .stack-layer {
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }

        .stack-layer:last-child {
            border-bottom: none;
        }

        .stack-layer .layer-name {
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            min-width: 140px;
        }

        .stack-layer .layer-tech {
            font-size: 12px;
            opacity: 0.9;
            text-align: right;
        }

        .stack-browser { background: #6f42c1; color: #fff; }
        .stack-frontend { background: #0d6efd; color: #fff; }
        .stack-webserver { background: #0dcaf0; color: #000; }
        .stack-app { background: #198754; color: #fff; }
        .stack-database { background: #fd7e14; color: #000; }
        .stack-storage { background: #dc3545; color: #fff; }

        .stack-arrow {
            text-align: center;
            font-size: 18px;
            color: #adb5bd;
            padding: 2px 0;
            background: #f8f9fa;
        }

        /* ========== DIRECTORY TREE ========== */
        .dir-tree {
            background: #1a1a2e;
            color: #e2e8f0;
            padding: 24px 28px;
            border-radius: 10px;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 11.5px;
            line-height: 1.7;
            overflow-x: auto;
            margin: 20px 0;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
            white-space: pre;
        }

        .dir-tree .dc {
            color: #6c757d;
        }

        .dir-tree .df {
            color: #0dcaf0;
        }

        .dir-tree .ff {
            color: #ffc107;
        }

        /* ========== FLOW DIAGRAM ========== */
        .flow-diagram {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0;
            margin: 24px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }

        .flow-step {
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            color: #fff;
            min-width: 80px;
        }

        .flow-arrow {
            font-size: 20px;
            color: #adb5bd;
            padding: 0 4px;
            font-weight: 700;
        }

        .flow-blue { background: #0d6efd; }
        .flow-cyan { background: #0dcaf0; color: #000; }
        .flow-green { background: #198754; }
        .flow-orange { background: #fd7e14; color: #000; }
        .flow-purple { background: #6f42c1; }
        .flow-red { background: #dc3545; }
        .flow-teal { background: #20c997; color: #000; }

        /* ========== CATEGORY GRID ========== */
        .grid-categories {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin: 24px 0;
        }

        .cat-card {
            padding: 14px 16px;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            background: #ffffff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.05);
            text-align: center;
        }

        .cat-card .cat-icon {
            font-size: 22px;
            margin-bottom: 6px;
        }

        .cat-card .cat-name {
            font-size: 11px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 3px;
        }

        .cat-card .cat-count {
            font-size: 10px;
            color: #6c757d;
        }

        .cat-blue { border-left: 4px solid #0d6efd; }
        .cat-cyan { border-left: 4px solid #0dcaf0; }
        .cat-green { border-left: 4px solid #198754; }
        .cat-orange { border-left: 4px solid #fd7e14; }
        .cat-purple { border-left: 4px solid #6f42c1; }
        .cat-red { border-left: 4px solid #dc3545; }
        .cat-teal { border-left: 4px solid #20c997; }
        .cat-pink { border-left: 4px solid #d63384; }
        .cat-yellow { border-left: 4px solid #ffc107; }
        .cat-indigo { border-left: 4px solid #6610f2; }

        /* ========== INFO BOXES ========== */
        .info-box {
            padding: 16px 20px;
            border-radius: 8px;
            margin: 16px 0;
            font-size: 12px;
        }

        .info-box-blue {
            background: #e7f1ff;
            border-left: 4px solid #0d6efd;
            color: #084298;
        }

        .info-box-green {
            background: #d1e7dd;
            border-left: 4px solid #198754;
            color: #0f5132;
        }

        .info-box-cyan {
            background: #cff4fc;
            border-left: 4px solid #0dcaf0;
            color: #055160;
        }

        /* ========== TABLE ========== */
        .arch-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
            font-size: 12px;
        }

        .arch-table thead th {
            background: #0d6efd;
            color: #ffffff;
            padding: 10px 14px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .arch-table thead th:first-child {
            border-radius: 8px 0 0 0;
        }

        .arch-table thead th:last-child {
            border-radius: 0 8px 0 0;
        }

        .arch-table tbody td {
            padding: 10px 14px;
            border-bottom: 1px solid #e9ecef;
            color: #2d2d44;
        }

        .arch-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .arch-table tbody tr:last-child td:first-child {
            border-radius: 0 0 0 8px;
        }

        .arch-table tbody tr:last-child td:last-child {
            border-radius: 0 0 8px 0;
        }

        /* ========== CODE BLOCK ========== */
        .code-block {
            background: #1a1a2e;
            color: #e2e8f0;
            padding: 18px 22px;
            border-radius: 8px;
            font-family: 'Consolas', 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.6;
            margin: 14px 0;
            overflow-x: auto;
            white-space: pre;
        }

        .code-block .ck { color: #0dcaf0; }
        .code-block .cs { color: #198754; }
        .code-block .cc { color: #6c757d; }

        /* ========== INTEGRATION DIAGRAM ========== */
        .integration-diagram {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 24px 0;
            padding: 30px 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 1px solid #e9ecef;
        }

        .int-center {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            color: #fff;
            padding: 20px 24px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            text-align: center;
            min-width: 140px;
            box-shadow: 0 4px 16px rgba(13,106,253,0.3);
        }

        .int-spokes {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin: 0 16px;
        }

        .int-spoke {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .int-spoke-arrow {
            color: #adb5bd;
            font-size: 16px;
            font-weight: 700;
        }

        .int-node {
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            background: #ffffff;
            border: 2px solid #dee2e6;
            color: #1a1a2e;
            min-width: 120px;
            text-align: center;
        }

        /* ========== DEPLOYMENT DIAGRAM ========== */
        .deploy-diagram {
            margin: 24px 0;
            padding: 24px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 1px solid #e9ecef;
        }

        .deploy-label {
            text-align: center;
            margin-bottom: 12px;
            font-weight: 600;
            color: #6c757d;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .deploy-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .deploy-box {
            padding: 12px 18px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            min-width: 120px;
        }

        .deploy-box small {
            display: block;
            font-size: 10px;
            opacity: 0.8;
            font-weight: 400;
            margin-top: 2px;
        }

        .deploy-arrow-down {
            text-align: center;
            font-size: 20px;
            color: #adb5bd;
            margin-bottom: 8px;
        }

        .deploy-lb { background: #6f42c1; color: #fff; }
        .deploy-app { background: #0d6efd; color: #fff; }
        .deploy-db-primary { background: #198754; color: #fff; }
        .deploy-db-replica { background: #20c997; color: #000; }
        .deploy-storage { background: #fd7e14; color: #000; }

        /* ========== TABS DIAGRAM ========== */
        .tabs-demo {
            display: flex;
            gap: 0;
            margin: 16px 0 0 0;
            border-radius: 8px 8px 0 0;
            overflow: hidden;
        }

        .tab-item {
            padding: 10px 18px;
            font-size: 11px;
            font-weight: 600;
            background: #e9ecef;
            color: #6c757d;
            border-right: 1px solid #dee2e6;
        }

        .tab-item.active {
            background: #0d6efd;
            color: #ffffff;
        }

        .tab-content-demo {
            padding: 16px 20px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 16px;
        }

        /* ========== FOOTER ========== */
        .doc-footer {
            text-align: center;
            padding: 30px 50px;
            border-top: 2px solid #e9ecef;
            font-size: 11px;
            color: #6c757d;
            background: #f8f9fa;
        }

        /* ========== BADGE ========== */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .badge-blue { background: #0d6efd; color: #fff; }
        .badge-cyan { background: #0dcaf0; color: #000; }
        .badge-green { background: #198754; color: #fff; }
    </style>
</head>
<body>

<!-- ========== PRINT BAR ========== -->
<div class="print-bar">
    <div class="bar-title">EnPharChem &mdash; System Architecture Overview</div>
    <div class="bar-actions">
        <a href="javascript:history.back()" class="btn btn-back">&#8592; Back</a>
        <button class="btn btn-pdf" onclick="window.print()">&#128438; Download PDF</button>
    </div>
</div>

<div class="document">

    <!-- ========== COVER PAGE ========== -->
    <div class="cover-page">
        <div class="cover-logo"><span>EP</span></div>
        <div class="cover-title">EnPharChem</div>
        <div class="cover-subtitle">System Architecture Overview</div>
        <div class="cover-divider"></div>
        <div class="cover-meta">
            <div class="meta-line"><strong>Version 1.0</strong></div>
            <div class="meta-line">March 2026</div>
            <div class="meta-line">Enterprise Platform for Energy, Pharmaceutical &amp; Chemical Engineering</div>
        </div>
        <div class="cover-classification">Technical Architecture Document</div>
    </div>

    <!-- ========== CONTENT ========== -->
    <div class="content">

        <!-- ==================== SECTION 1: PLATFORM OVERVIEW ==================== -->
        <h2><span class="section-num">1</span> Platform Overview</h2>

        <p>
            <strong>EnPharChem</strong> is a comprehensive enterprise platform purpose-built for <strong>Energy, Pharmaceutical &amp; Chemical Engineering</strong> professionals. It provides a unified workspace for process simulation, equipment design, cost estimation, advanced process control, manufacturing execution, and operational optimization.
        </p>

        <div class="info-box info-box-blue">
            <strong>Key Platform Metrics:</strong> 115+ integrated modules across 15 engineering categories, serving the full lifecycle of process engineering from conceptual design through operational excellence.
        </div>

        <ul>
            <li><strong>Core Technology:</strong> PHP 8.1+ / MySQL 8.0+ / Apache 2.4+</li>
            <li><strong>Architecture:</strong> Model-View-Controller (MVC) with clean separation of concerns</li>
            <li><strong>Modules:</strong> 115+ modules organized across 15 engineering categories</li>
            <li><strong>Design Philosophy:</strong> Modular, extensible, and enterprise-ready</li>
            <li><strong>Interface:</strong> Modern dark-themed responsive UI with real-time simulation capabilities</li>
        </ul>

        <!-- ==================== SECTION 2: TECHNOLOGY STACK ==================== -->
        <h2><span class="section-num">2</span> Technology Stack</h2>

        <p>The platform is built on a proven, production-ready technology stack optimized for engineering computation workloads and data-intensive operations.</p>

        <div class="stack-diagram">
            <div class="stack-layer stack-browser">
                <span class="layer-name">Browser Layer</span>
                <span class="layer-tech">Chrome &bull; Firefox &bull; Edge &bull; Safari</span>
            </div>
            <div class="stack-arrow">&darr;</div>
            <div class="stack-layer stack-frontend">
                <span class="layer-name">Frontend</span>
                <span class="layer-tech">Bootstrap 5.3 &bull; Font Awesome 6.5 &bull; Chart.js 4.4 &bull; Custom CSS/JS</span>
            </div>
            <div class="stack-arrow">&darr;</div>
            <div class="stack-layer stack-webserver">
                <span class="layer-name">Web Server</span>
                <span class="layer-tech">Apache 2.4 with mod_rewrite &bull; URL Routing &bull; Static Assets</span>
            </div>
            <div class="stack-arrow">&darr;</div>
            <div class="stack-layer stack-app">
                <span class="layer-name">Application</span>
                <span class="layer-tech">PHP 8.1+ MVC Framework &bull; 23 Controllers &bull; 4 Models &bull; PDO</span>
            </div>
            <div class="stack-arrow">&darr;</div>
            <div class="stack-layer stack-database">
                <span class="layer-name">Database</span>
                <span class="layer-tech">MySQL 8.0 / MariaDB 10.6 &bull; InnoDB &bull; 35+ Tables</span>
            </div>
            <div class="stack-arrow">&darr;</div>
            <div class="stack-layer stack-storage">
                <span class="layer-name">Storage</span>
                <span class="layer-tech">File System &bull; JSON Data &bull; CSV/XML Exports</span>
            </div>
        </div>

        <!-- ==================== SECTION 3: APPLICATION ARCHITECTURE ==================== -->
        <h2><span class="section-num">3</span> Application Architecture (MVC)</h2>

        <p>
            EnPharChem follows a strict Model-View-Controller architecture with a front controller pattern. All requests are routed through a single entry point (<code>index.php</code>), which delegates to the appropriate controller based on URL pattern matching.
        </p>

        <h3><span class="sub-num">3.1</span> Directory Structure</h3>

        <div class="dir-tree"><span class="df">enpharchem/</span>
&#9500;&#9472;&#9472; <span class="ff">index.php</span>                <span class="dc">// Front Controller / Router</span>
&#9500;&#9472;&#9472; <span class="ff">.htaccess</span>                <span class="dc">// Apache URL Rewriting</span>
&#9500;&#9472;&#9472; <span class="df">config/</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">app.php</span>              <span class="dc">// Application configuration</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">database.php</span>         <span class="dc">// Database connection settings</span>
&#9474;   &#9492;&#9472;&#9472; <span class="ff">routes.php</span>           <span class="dc">// Route definitions</span>
&#9500;&#9472;&#9472; <span class="df">controllers/</span>             <span class="dc">// 23 Controllers</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">BaseController.php</span>   <span class="dc">// Abstract base with shared methods</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">AuthController.php</span>   <span class="dc">// Authentication &amp; authorization</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">DashboardController.php</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">ProcessSimEnergyController.php</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">ExchangerDesignController.php</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">CostEstimationController.php</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">SubsurfaceController.php</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">APCController.php</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">MESController.php</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">SupplyChainController.php</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">APMController.php</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">GridOperationsController.php</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">OptimizationController.php</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">ApiController.php</span>
&#9474;   &#9492;&#9472;&#9472; ...                      <span class="dc">// Additional domain controllers</span>
&#9500;&#9472;&#9472; <span class="df">models/</span>                  <span class="dc">// 4 Core Models</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">User.php</span>             <span class="dc">// User authentication &amp; profiles</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">Project.php</span>          <span class="dc">// Project management</span>
&#9474;   &#9500;&#9472;&#9472; <span class="ff">Simulation.php</span>       <span class="dc">// Simulation engine interface</span>
&#9474;   &#9492;&#9472;&#9472; <span class="ff">Module.php</span>           <span class="dc">// Module registry &amp; loading</span>
&#9500;&#9472;&#9472; <span class="df">views/</span>                   <span class="dc">// 30+ View Templates</span>
&#9474;   &#9500;&#9472;&#9472; <span class="df">layouts/</span><span class="ff">main.php</span>     <span class="dc">// Master layout template</span>
&#9474;   &#9500;&#9472;&#9472; <span class="df">auth/</span>                <span class="dc">// Login, register, profile</span>
&#9474;   &#9500;&#9472;&#9472; <span class="df">dashboard/</span>           <span class="dc">// Dashboard views</span>
&#9474;   &#9500;&#9472;&#9472; <span class="df">modules/</span>             <span class="dc">// Module workspace views</span>
&#9474;   &#9500;&#9472;&#9472; <span class="df">projects/</span>            <span class="dc">// Project management views</span>
&#9474;   &#9500;&#9472;&#9472; <span class="df">simulations/</span>         <span class="dc">// Simulation interface views</span>
&#9474;   &#9500;&#9472;&#9472; <span class="df">admin/</span>               <span class="dc">// Administration panels</span>
&#9474;   &#9500;&#9472;&#9472; <span class="df">benchmark/</span>           <span class="dc">// Performance benchmarks</span>
&#9474;   &#9500;&#9472;&#9472; <span class="df">control-panel/</span>       <span class="dc">// CMS &amp; management tools</span>
&#9474;   &#9492;&#9472;&#9472; <span class="df">marketing-docs/</span>      <span class="dc">// Technical documentation</span>
&#9500;&#9472;&#9472; <span class="df">assets/</span>
&#9474;   &#9500;&#9472;&#9472; <span class="df">css/</span><span class="ff">app.css</span>         <span class="dc">// Main stylesheet</span>
&#9474;   &#9492;&#9472;&#9472; <span class="df">js/</span><span class="ff">app.js</span>           <span class="dc">// Main JavaScript</span>
&#9492;&#9472;&#9472; <span class="df">database/</span>
    &#9500;&#9472;&#9472; <span class="ff">schema.sql</span>           <span class="dc">// Core database schema</span>
    &#9492;&#9472;&#9472; <span class="ff">control_panel_tables.sql</span> <span class="dc">// Control panel schema</span></div>

        <h3><span class="sub-num">3.2</span> Request Lifecycle</h3>

        <p>Every request follows a deterministic lifecycle from client to server and back. The front controller pattern ensures consistent request handling, authentication checks, and response formatting across all endpoints.</p>

        <div class="flow-diagram">
            <div class="flow-step flow-purple">Browser<br>Request</div>
            <span class="flow-arrow">&rarr;</span>
            <div class="flow-step flow-cyan">.htaccess<br>Rewrite</div>
            <span class="flow-arrow">&rarr;</span>
            <div class="flow-step flow-blue">index.php<br>Front Controller</div>
            <span class="flow-arrow">&rarr;</span>
            <div class="flow-step flow-green">Route<br>Matching</div>
            <span class="flow-arrow">&rarr;</span>
            <div class="flow-step flow-orange">Controller<br>Action</div>
            <span class="flow-arrow">&rarr;</span>
            <div class="flow-step flow-red">Model /<br>Database</div>
            <span class="flow-arrow">&rarr;</span>
            <div class="flow-step flow-teal">View<br>Render</div>
            <span class="flow-arrow">&rarr;</span>
            <div class="flow-step flow-purple">HTTP<br>Response</div>
        </div>

        <div class="info-box info-box-green">
            <strong>Request Flow Details:</strong> Apache mod_rewrite captures all non-asset requests and forwards them to <code>index.php</code>. The router parses the URI, matches against defined routes, instantiates the appropriate controller, calls the matched action method, which interacts with models/database and renders a view template wrapped in the main layout.
        </div>

        <h3><span class="sub-num">3.3</span> Controller Architecture</h3>

        <p>
            All 23 controllers extend <code>BaseController</code>, which provides shared functionality including authentication verification, view rendering, JSON response helpers, flash messaging, and input validation. This inheritance model ensures consistent behavior and reduces code duplication.
        </p>

        <table class="arch-table">
            <thead>
                <tr>
                    <th>BaseController Method</th>
                    <th>Purpose</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><code>view($name, $data)</code></td><td>Render a view template with layout wrapping</td></tr>
                <tr><td><code>json($data, $code)</code></td><td>Send JSON response with HTTP status code</td></tr>
                <tr><td><code>redirect($url)</code></td><td>HTTP redirect with optional flash message</td></tr>
                <tr><td><code>requireAuth()</code></td><td>Verify user authentication before action execution</td></tr>
                <tr><td><code>validate($rules)</code></td><td>Input validation against rule sets</td></tr>
                <tr><td><code>getDb()</code></td><td>Access PDO database singleton instance</td></tr>
            </tbody>
        </table>

        <h3><span class="sub-num">3.4</span> Database Abstraction</h3>

        <p>
            The platform uses PHP's PDO (PHP Data Objects) extension with a singleton connection pattern. All database queries use prepared statements with parameter binding to prevent SQL injection. A lightweight query builder provides fluent methods for common operations.
        </p>

        <div class="code-block"><span class="cc">// Database singleton pattern (config/database.php)</span>
<span class="ck">class</span> Database {
    <span class="ck">private static</span> ?PDO $instance = <span class="ck">null</span>;

    <span class="ck">public static function</span> <span class="cs">getInstance</span>(): PDO {
        <span class="ck">if</span> (self::$instance === <span class="ck">null</span>) {
            self::$instance = <span class="ck">new</span> PDO(
                <span class="cs">"mysql:host={$host};dbname={$dbname};charset=utf8mb4"</span>,
                $username, $password,
                [PDO::ATTR_ERRMODE =&gt; PDO::ERRMODE_EXCEPTION]
            );
        }
        <span class="ck">return</span> self::$instance;
    }
}

<span class="cc">// Prepared statement usage in models</span>
$stmt = $db-&gt;<span class="cs">prepare</span>(<span class="cs">"SELECT * FROM simulations WHERE project_id = :pid"</span>);
$stmt-&gt;<span class="cs">execute</span>([<span class="cs">':pid'</span> =&gt; $projectId]);</div>

        <!-- ==================== SECTION 4: DATABASE ARCHITECTURE ==================== -->
        <h2><span class="section-num">4</span> Database Architecture</h2>

        <h3><span class="sub-num">4.1</span> Entity Relationship Summary</h3>

        <p>
            The database follows a normalized relational design with clear entity boundaries. The core entity hierarchy flows from <strong>Users</strong> who own <strong>Projects</strong>, which contain <strong>Simulations</strong> that utilize <strong>Modules</strong>. Domain-specific tables extend this core with specialized data structures for each engineering discipline.
        </p>

        <ul>
            <li><strong>Users &rarr; Projects:</strong> One-to-many. Each user owns multiple projects.</li>
            <li><strong>Projects &rarr; Simulations:</strong> One-to-many. Each project contains multiple simulation runs.</li>
            <li><strong>Modules &rarr; Module Categories:</strong> Many-to-one. Modules are organized by engineering discipline.</li>
            <li><strong>Simulations &rarr; Modules:</strong> Many-to-one. Each simulation is associated with a specific module.</li>
            <li><strong>Domain Tables:</strong> Linked to simulations/projects via foreign keys for discipline-specific data.</li>
        </ul>

        <h3><span class="sub-num">4.2</span> Core Tables</h3>

        <table class="arch-table">
            <thead>
                <tr>
                    <th>Table</th>
                    <th>Columns</th>
                    <th>Purpose</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><code>users</code></td><td>14</td><td>User accounts, authentication credentials, roles, profiles</td></tr>
                <tr><td><code>projects</code></td><td>7</td><td>Engineering projects with metadata and ownership</td></tr>
                <tr><td><code>simulations</code></td><td>15</td><td>Simulation runs with parameters, status, and results</td></tr>
                <tr><td><code>modules</code></td><td>10</td><td>Module registry with descriptions, icons, and routes</td></tr>
                <tr><td><code>module_categories</code></td><td>7</td><td>Category groupings for the 15 engineering disciplines</td></tr>
            </tbody>
        </table>

        <h3><span class="sub-num">4.3</span> Domain Tables by Category</h3>

        <table class="arch-table">
            <thead>
                <tr>
                    <th>Domain</th>
                    <th>Tables</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><strong>Process Simulation</strong></td><td><code>process_flowsheets</code>, <code>streams</code>, <code>unit_operations</code>, <code>chemical_components</code>, <code>thermodynamic_models</code></td></tr>
                <tr><td><strong>Exchanger Design</strong></td><td><code>heat_exchangers</code></td></tr>
                <tr><td><strong>Cost Estimation</strong></td><td><code>cost_estimates</code>, <code>equipment_items</code></td></tr>
                <tr><td><strong>Subsurface Engineering</strong></td><td><code>reservoir_models</code>, <code>seismic_surveys</code></td></tr>
                <tr><td><strong>Advanced Process Control</strong></td><td><code>apc_controllers</code>, <code>apc_models</code></td></tr>
                <tr><td><strong>Manufacturing Execution</strong></td><td><code>plant_data_tags</code>, <code>plant_data_history</code>, <code>production_records</code></td></tr>
                <tr><td><strong>Supply Chain</strong></td><td><code>supply_chain_plans</code>, <code>crude_assays</code>, <code>blend_recipes</code></td></tr>
                <tr><td><strong>Asset Performance</strong></td><td><code>assets</code>, <code>asset_health_scores</code>, <code>maintenance_events</code></td></tr>
                <tr><td><strong>Grid Operations</strong></td><td><code>energy_networks</code>, <code>grid_assets</code>, <code>scada_points</code></td></tr>
                <tr><td><strong>Optimization</strong></td><td><code>optimization_problems</code></td></tr>
                <tr><td><strong>Control Panel</strong></td><td><code>ad_groups</code>, <code>ad_users</code>, <code>cms_pages</code>, <code>marketing_materials</code>, <code>training_courses</code>, <code>training_lessons</code></td></tr>
            </tbody>
        </table>

        <h3><span class="sub-num">4.4</span> Database Totals</h3>

        <div class="info-box info-box-cyan">
            <strong>Total Database Footprint:</strong> 35+ tables spanning core platform entities, 10 engineering domains, and administrative/CMS functionality. All tables use InnoDB storage engine with UTF-8 (utf8mb4) character set for full Unicode support.
        </div>

        <!-- ==================== SECTION 5: MODULE ARCHITECTURE ==================== -->
        <h2><span class="section-num">5</span> Module Architecture</h2>

        <p>The platform organizes its 115+ modules into 15 engineering categories. Each category maps to a dedicated controller and set of views, providing deep domain-specific functionality while maintaining a consistent user experience.</p>

        <h3><span class="sub-num">5.1</span> Module Categories</h3>

        <div class="grid-categories">
            <div class="cat-card cat-blue">
                <div class="cat-icon">&#9881;</div>
                <div class="cat-name">Process Simulation</div>
                <div class="cat-count">12 modules</div>
            </div>
            <div class="cat-card cat-cyan">
                <div class="cat-icon">&#9832;</div>
                <div class="cat-name">Heat Exchanger Design</div>
                <div class="cat-count">8 modules</div>
            </div>
            <div class="cat-card cat-green">
                <div class="cat-icon">&#36;</div>
                <div class="cat-name">Cost Estimation</div>
                <div class="cat-count">6 modules</div>
            </div>
            <div class="cat-card cat-orange">
                <div class="cat-icon">&#9968;</div>
                <div class="cat-name">Subsurface Engineering</div>
                <div class="cat-count">8 modules</div>
            </div>
            <div class="cat-card cat-purple">
                <div class="cat-icon">&#9878;</div>
                <div class="cat-name">Advanced Process Control</div>
                <div class="cat-count">7 modules</div>
            </div>
            <div class="cat-card cat-red">
                <div class="cat-icon">&#9874;</div>
                <div class="cat-name">Manufacturing Execution</div>
                <div class="cat-count">9 modules</div>
            </div>
            <div class="cat-card cat-teal">
                <div class="cat-icon">&#8644;</div>
                <div class="cat-name">Supply Chain &amp; Blending</div>
                <div class="cat-count">8 modules</div>
            </div>
            <div class="cat-card cat-pink">
                <div class="cat-icon">&#9883;</div>
                <div class="cat-name">Asset Performance Mgmt</div>
                <div class="cat-count">7 modules</div>
            </div>
            <div class="cat-card cat-yellow">
                <div class="cat-icon">&#9889;</div>
                <div class="cat-name">Grid Operations</div>
                <div class="cat-count">8 modules</div>
            </div>
            <div class="cat-card cat-indigo">
                <div class="cat-icon">&#8734;</div>
                <div class="cat-name">Optimization</div>
                <div class="cat-count">6 modules</div>
            </div>
            <div class="cat-card cat-blue">
                <div class="cat-icon">&#9783;</div>
                <div class="cat-name">Data Analytics</div>
                <div class="cat-count">7 modules</div>
            </div>
            <div class="cat-card cat-green">
                <div class="cat-icon">&#9998;</div>
                <div class="cat-name">Documentation &amp; Training</div>
                <div class="cat-count">5 modules</div>
            </div>
            <div class="cat-card cat-cyan">
                <div class="cat-icon">&#9888;</div>
                <div class="cat-name">Safety &amp; Compliance</div>
                <div class="cat-count">8 modules</div>
            </div>
            <div class="cat-card cat-orange">
                <div class="cat-icon">&#9728;</div>
                <div class="cat-name">Environmental</div>
                <div class="cat-count">6 modules</div>
            </div>
            <div class="cat-card cat-purple">
                <div class="cat-icon">&#9850;</div>
                <div class="cat-name">Utilities &amp; Integration</div>
                <div class="cat-count">10 modules</div>
            </div>
        </div>

        <h3><span class="sub-num">5.2</span> Module Loading Flow</h3>

        <div class="flow-diagram">
            <div class="flow-step flow-blue">Category<br>Controller</div>
            <span class="flow-arrow">&rarr;</span>
            <div class="flow-step flow-green">getModules<br>ByCategory()</div>
            <span class="flow-arrow">&rarr;</span>
            <div class="flow-step flow-orange">Module<br>Registry</div>
            <span class="flow-arrow">&rarr;</span>
            <div class="flow-step flow-purple">Module<br>Workspace View</div>
        </div>

        <h3><span class="sub-num">5.3</span> Module Workspace Interface</h3>

        <p>Each module presents a consistent tabbed workspace interface with five standard tabs, allowing engineers to move through a logical workflow from overview to results.</p>

        <div class="tabs-demo">
            <div class="tab-item active">Overview</div>
            <div class="tab-item">Simulation</div>
            <div class="tab-item">Configuration</div>
            <div class="tab-item">Results</div>
            <div class="tab-item">Documentation</div>
        </div>
        <div class="tab-content-demo">
            Module workspace content area &mdash; dynamically loaded based on selected tab. Each tab provides domain-specific forms, visualizations, and data management tools.
        </div>

        <!-- ==================== SECTION 6: API LAYER ==================== -->
        <h2><span class="section-num">6</span> API Layer</h2>

        <p>
            The <code>ApiController</code> provides a RESTful JSON API for programmatic access to platform data. All API endpoints return structured JSON responses with consistent error handling and HTTP status codes.
        </p>

        <table class="arch-table">
            <thead>
                <tr>
                    <th>Endpoint</th>
                    <th>Method</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><code>/api/simulations</code></td><td><span class="badge badge-green">GET</span> <span class="badge badge-blue">POST</span></td><td>List / create simulation runs</td></tr>
                <tr><td><code>/api/components</code></td><td><span class="badge badge-green">GET</span></td><td>Chemical component library lookup</td></tr>
                <tr><td><code>/api/flowsheet</code></td><td><span class="badge badge-green">GET</span> <span class="badge badge-blue">POST</span></td><td>Process flowsheet data management</td></tr>
                <tr><td><code>/api/dashboard-stats</code></td><td><span class="badge badge-green">GET</span></td><td>Aggregated dashboard statistics</td></tr>
            </tbody>
        </table>

        <h3>Request / Response Format</h3>

        <div class="code-block"><span class="cc">// Example API Request</span>
GET /api/dashboard-stats HTTP/1.1
Host: enpharchem.local
Accept: application/json

<span class="cc">// Example API Response</span>
{
    <span class="ck">"status"</span>: <span class="cs">"success"</span>,
    <span class="ck">"data"</span>: {
        <span class="ck">"total_projects"</span>: 47,
        <span class="ck">"active_simulations"</span>: 12,
        <span class="ck">"modules_available"</span>: 115,
        <span class="ck">"recent_activity"</span>: [...]
    },
    <span class="ck">"timestamp"</span>: <span class="cs">"2026-03-23T10:30:00Z"</span>
}</div>

        <!-- ==================== SECTION 7: FRONTEND ARCHITECTURE ==================== -->
        <h2><span class="section-num">7</span> Frontend Architecture</h2>

        <h3><span class="sub-num">7.1</span> Layout System</h3>
        <p>
            The master layout (<code>views/layouts/main.php</code>) wraps all page views using PHP output buffering (<code>ob_start()</code> / <code>ob_get_clean()</code>). This provides consistent navigation, sidebar, footer, and asset loading across every page while allowing each view to inject its own content and page-specific scripts.
        </p>

        <h3><span class="sub-num">7.2</span> Dark Theme System</h3>
        <p>
            The platform uses a dark-themed interface optimized for prolonged engineering work. Theme colors are managed through CSS custom properties (variables), enabling consistent styling and potential future theme switching.
        </p>

        <div class="code-block"><span class="cc">/* CSS Custom Properties for theming */</span>
:root {
    <span class="ck">--bg-primary</span>: <span class="cs">#1a1a2e</span>;
    <span class="ck">--bg-secondary</span>: <span class="cs">#16213e</span>;
    <span class="ck">--bg-card</span>: <span class="cs">#0f3460</span>;
    <span class="ck">--text-primary</span>: <span class="cs">#e2e8f0</span>;
    <span class="ck">--accent-blue</span>: <span class="cs">#0d6efd</span>;
    <span class="ck">--accent-cyan</span>: <span class="cs">#0dcaf0</span>;
    <span class="ck">--accent-green</span>: <span class="cs">#198754</span>;
}</div>

        <h3><span class="sub-num">7.3</span> Component Library</h3>
        <p>The UI is composed of reusable components that maintain visual consistency across all modules:</p>
        <ul>
            <li><strong>Stat Cards:</strong> KPI display tiles with icons, values, and trend indicators</li>
            <li><strong>Module Cards:</strong> Category-organized module selection with descriptions and quick-launch</li>
            <li><strong>Data Tables:</strong> Sortable, filterable tables for simulation results and data management</li>
            <li><strong>Badges &amp; Labels:</strong> Status indicators, severity levels, and categorization markers</li>
            <li><strong>Forms:</strong> Validated input forms for simulation parameters, configurations, and settings</li>
            <li><strong>Tabbed Workspace:</strong> Standard module interface with Overview, Simulation, Config, Results, Docs tabs</li>
            <li><strong>Flowsheet Canvas:</strong> Interactive process flow diagram editor with drag-and-drop unit operations</li>
        </ul>

        <h3><span class="sub-num">7.4</span> Chart.js Integration</h3>
        <p>
            Chart.js 4.4 powers all data visualizations including simulation results (line/scatter), benchmark comparisons (bar/radar), asset health gauges (doughnut), production trends (area), and cost breakdowns (pie). Charts are dynamically rendered with responsive sizing and dark theme color palettes.
        </p>

        <h3><span class="sub-num">7.5</span> Responsive Design</h3>
        <p>
            The interface adapts across screen sizes using Bootstrap 5.3's responsive grid. On mobile devices, the sidebar collapses into an off-canvas drawer, tables switch to card-based layouts, and the flowsheet canvas enables touch-based interaction. Breakpoints follow Bootstrap conventions: sm (576px), md (768px), lg (992px), xl (1200px).
        </p>

        <!-- ==================== SECTION 8: INTEGRATION ARCHITECTURE ==================== -->
        <h2><span class="section-num">8</span> Integration Architecture</h2>

        <p>
            EnPharChem is designed to integrate with existing plant infrastructure and enterprise systems. The integration layer supports bidirectional data exchange with industrial control systems, historians, ERP platforms, and laboratory systems.
        </p>

        <div class="integration-diagram">
            <div class="int-spokes">
                <div class="int-spoke">
                    <div class="int-node">Plant DCS / SCADA</div>
                    <span class="int-spoke-arrow">&harr;</span>
                </div>
                <div class="int-spoke">
                    <div class="int-node">Process Historian</div>
                    <span class="int-spoke-arrow">&harr;</span>
                </div>
                <div class="int-spoke">
                    <div class="int-node">Laboratory LIMS</div>
                    <span class="int-spoke-arrow">&harr;</span>
                </div>
            </div>
            <div class="int-center">
                EnPharChem<br>Platform
            </div>
            <div class="int-spokes">
                <div class="int-spoke">
                    <span class="int-spoke-arrow">&harr;</span>
                    <div class="int-node">ERP / SAP</div>
                </div>
                <div class="int-spoke">
                    <span class="int-spoke-arrow">&harr;</span>
                    <div class="int-node">Document Mgmt</div>
                </div>
                <div class="int-spoke">
                    <span class="int-spoke-arrow">&harr;</span>
                    <div class="int-node">Cloud Services</div>
                </div>
            </div>
        </div>

        <h3>Data Exchange Formats</h3>
        <table class="arch-table">
            <thead>
                <tr>
                    <th>Format</th>
                    <th>Use Case</th>
                    <th>Direction</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><strong>JSON</strong></td><td>API communication, configuration, simulation parameters</td><td>Bidirectional</td></tr>
                <tr><td><strong>CSV</strong></td><td>Bulk data import/export, historical data, reports</td><td>Bidirectional</td></tr>
                <tr><td><strong>XML</strong></td><td>ERP integration, regulatory compliance documents</td><td>Bidirectional</td></tr>
            </tbody>
        </table>

        <div class="info-box info-box-blue">
            <strong>Future Integration Roadmap:</strong> REST API expansion with OpenAPI 3.0 specification, OPC-UA connectivity for real-time plant data, MQTT for IoT sensor integration, and webhook support for event-driven architectures.
        </div>

        <!-- ==================== SECTION 9: DEPLOYMENT ARCHITECTURE ==================== -->
        <h2><span class="section-num">9</span> Deployment Architecture</h2>

        <h3><span class="sub-num">9.1</span> Single-Server Deployment (Development / Small Sites)</h3>
        <p>
            For development and small-scale deployments, EnPharChem runs on a single server using XAMPP (Apache + MySQL + PHP) or equivalent LAMP/WAMP stack. This configuration is suitable for teams of up to 25 concurrent users.
        </p>

        <div class="deploy-diagram">
            <div class="deploy-label">Single-Server (XAMPP)</div>
            <div class="deploy-row">
                <div class="deploy-box deploy-app" style="min-width: 320px;">Apache 2.4 + PHP 8.1 + MySQL 8.0<small>All services on one machine</small></div>
            </div>
        </div>

        <h3><span class="sub-num">9.2</span> Multi-Server Deployment (Production)</h3>

        <div class="deploy-diagram">
            <div class="deploy-label">Production Multi-Server Architecture</div>
            <div class="deploy-row">
                <div class="deploy-box deploy-lb" style="min-width: 260px;">Load Balancer<small>HAProxy / Nginx / AWS ALB</small></div>
            </div>
            <div class="deploy-arrow-down">&darr;</div>
            <div class="deploy-row">
                <div class="deploy-box deploy-app">App Server 1<small>Apache + PHP 8.1</small></div>
                <div class="deploy-box deploy-app">App Server 2<small>Apache + PHP 8.1</small></div>
            </div>
            <div class="deploy-arrow-down">&darr;</div>
            <div class="deploy-row">
                <div class="deploy-box deploy-db-primary">Primary DB<small>MySQL 8.0 (Write)</small></div>
                <div class="deploy-box deploy-db-replica">Replica DB<small>MySQL 8.0 (Read)</small></div>
                <div class="deploy-box deploy-storage">Shared Storage<small>NFS / S3</small></div>
            </div>
        </div>

        <h3><span class="sub-num">9.3</span> Cloud Deployment Options</h3>

        <table class="arch-table">
            <thead>
                <tr>
                    <th>Provider</th>
                    <th>Compute</th>
                    <th>Database</th>
                    <th>Storage</th>
                    <th>Load Balancer</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><strong>AWS</strong></td><td>EC2 / ECS</td><td>RDS MySQL</td><td>S3 + EFS</td><td>ALB</td></tr>
                <tr><td><strong>Azure</strong></td><td>App Service / VMs</td><td>Azure MySQL</td><td>Blob Storage</td><td>Application Gateway</td></tr>
                <tr><td><strong>GCP</strong></td><td>Compute Engine / GKE</td><td>Cloud SQL</td><td>Cloud Storage</td><td>Cloud Load Balancing</td></tr>
            </tbody>
        </table>

        <!-- ==================== SECTION 10: PERFORMANCE & SCALABILITY ==================== -->
        <h2><span class="section-num">10</span> Performance &amp; Scalability</h2>

        <h3><span class="sub-num">10.1</span> Database Indexing Strategy</h3>
        <p>
            All primary keys use auto-incrementing integers. Foreign key columns are indexed for join performance. Composite indexes are applied to frequently queried column combinations (e.g., <code>user_id + created_at</code> for project listings, <code>module_id + status</code> for simulation filtering). Full-text indexes support search functionality across module descriptions and documentation content.
        </p>

        <h3><span class="sub-num">10.2</span> Query Optimization</h3>
        <ul>
            <li><strong>Prepared Statements:</strong> All queries use PDO prepared statements for both security and query plan caching</li>
            <li><strong>Pagination:</strong> Large result sets use LIMIT/OFFSET with total count caching to avoid full table scans</li>
            <li><strong>Selective Columns:</strong> Queries specify exact columns rather than using SELECT * to minimize data transfer</li>
            <li><strong>Eager Loading:</strong> Related data is fetched in batched queries to avoid N+1 query patterns</li>
        </ul>

        <h3><span class="sub-num">10.3</span> Session Management</h3>
        <p>
            User sessions are managed via PHP's native session handler with secure cookie configuration (HttpOnly, Secure, SameSite=Strict). Session data stores user identity, role permissions, active project context, and CSRF tokens. For multi-server deployments, sessions can be migrated to database or Redis storage.
        </p>

        <h3><span class="sub-num">10.4</span> Caching Strategy</h3>

        <table class="arch-table">
            <thead>
                <tr>
                    <th>Cache Layer</th>
                    <th>Current Implementation</th>
                    <th>Future Roadmap</th>
                </tr>
            </thead>
            <tbody>
                <tr><td><strong>Query Results</strong></td><td>Application-level array cache</td><td>Redis with TTL-based invalidation</td></tr>
                <tr><td><strong>Module Registry</strong></td><td>Per-request singleton</td><td>Memcached with warm-up on deploy</td></tr>
                <tr><td><strong>Session Data</strong></td><td>File-based PHP sessions</td><td>Redis session handler</td></tr>
                <tr><td><strong>Static Assets</strong></td><td>Apache mod_expires headers</td><td>CDN (CloudFront / CloudFlare)</td></tr>
                <tr><td><strong>Simulation Results</strong></td><td>Database storage</td><td>Object cache with lazy computation</td></tr>
            </tbody>
        </table>

        <div class="info-box info-box-green">
            <strong>Scalability Path:</strong> The architecture supports horizontal scaling through stateless application servers behind a load balancer, read replicas for database scaling, and object storage for large simulation datasets. The MVC separation ensures no architectural changes are needed when scaling from single-server to multi-server deployment.
        </div>

    </div>

    <!-- ========== FOOTER ========== -->
    <div class="doc-footer">
        &copy; 2026 EnPharChem Technologies &mdash; System Architecture v1.0
    </div>

</div>

</body>
</html>