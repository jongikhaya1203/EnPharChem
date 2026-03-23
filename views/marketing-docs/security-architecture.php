<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Architecture Whitepaper - EnPharChem Technologies</title>
    <style>
        /* ========== PAGE & PRINT SETUP ========== */
        @page {
            size: A4;
            margin: 20mm 18mm 25mm 18mm;
        }

        @media print {
            .print-bar { display: none !important; }
            body { background: #fff !important; }
            .page-break { page-break-before: always; }
            .no-break { page-break-inside: avoid; }
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #212529;
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
            z-index: 1000;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            padding: 12px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }

        .print-bar a.back-link {
            color: #adb5bd;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }

        .print-bar a.back-link:hover { color: #fff; }

        .print-bar .bar-title {
            color: #e9ecef;
            font-size: 14px;
            font-weight: 500;
        }

        .print-bar .btn-download {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
            color: #fff;
            border: none;
            padding: 8px 22px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .print-bar .btn-download:hover {
            background: linear-gradient(135deg, #0b5ed7, #0a58ca);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13,110,253,0.4);
        }

        /* ========== DOCUMENT CONTAINER ========== */
        .document {
            max-width: 210mm;
            margin: 70px auto 40px;
            background: #fff;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }

        @media print {
            .document { margin: 0; box-shadow: none; max-width: none; }
        }

        .doc-content {
            padding: 40px 50px;
        }

        /* ========== COLORS ========== */
        :root {
            --primary: #0d6efd;
            --accent: #0dcaf0;
            --danger: #dc3545;
            --success: #198754;
            --warning: #ffc107;
            --dark: #1a1a2e;
        }

        /* ========== COVER PAGE ========== */
        .cover-page {
            min-height: 280mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 50px;
            background: linear-gradient(180deg, #ffffff 0%, #f8f9ff 100%);
            position: relative;
            overflow: hidden;
        }

        .cover-page::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
        }

        .cover-page::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--primary));
        }

        .cover-logo {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            box-shadow: 0 8px 30px rgba(13,110,253,0.25);
        }

        .cover-logo span {
            color: #fff;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
        }

        .cover-company {
            font-size: 28pt;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .cover-subtitle {
            font-size: 13pt;
            color: #6c757d;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 50px;
        }

        .cover-title {
            font-size: 26pt;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .cover-edition {
            font-size: 12pt;
            color: #6c757d;
            margin-bottom: 60px;
        }

        .cover-shield {
            width: 120px;
            height: 140px;
            margin-bottom: 50px;
            position: relative;
        }

        .cover-shield svg {
            width: 100%;
            height: 100%;
        }

        .cover-classification {
            display: inline-block;
            border: 2px solid var(--danger);
            color: var(--danger);
            padding: 10px 28px;
            font-size: 10pt;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            border-radius: 4px;
        }

        .cover-meta {
            margin-top: 40px;
            font-size: 9pt;
            color: #adb5bd;
        }

        .cover-meta div { margin-bottom: 4px; }

        /* ========== HEADINGS ========== */
        h1.section-title {
            font-size: 20pt;
            font-weight: 800;
            color: var(--dark);
            border-bottom: 3px solid var(--primary);
            padding-bottom: 10px;
            margin: 40px 0 20px;
        }

        h2.subsection-title {
            font-size: 14pt;
            font-weight: 700;
            color: var(--primary);
            margin: 28px 0 12px;
            padding-left: 14px;
            border-left: 4px solid var(--accent);
        }

        h3.sub-subsection {
            font-size: 11pt;
            font-weight: 700;
            color: #343a40;
            margin: 18px 0 8px;
        }

        p { margin-bottom: 10px; text-align: justify; }

        /* ========== TABLES ========== */
        table.doc-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0 22px;
            font-size: 9.5pt;
        }

        table.doc-table thead th {
            background: var(--dark);
            color: #fff;
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table.doc-table tbody td {
            padding: 9px 12px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }

        table.doc-table tbody tr:nth-child(even) { background: #f8f9fa; }

        table.doc-table tbody tr:hover { background: #e8f0fe; }

        /* ========== BADGES ========== */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-green { background: #d1e7dd; color: #0f5132; }
        .badge-yellow { background: #fff3cd; color: #664d03; }
        .badge-red { background: #f8d7da; color: #842029; }
        .badge-blue { background: #cfe2ff; color: #084298; }

        .check { color: var(--success); font-weight: 700; font-size: 13pt; }
        .cross { color: var(--danger); font-weight: 700; font-size: 13pt; }
        .partial { color: var(--warning); font-weight: 700; font-size: 13pt; }

        /* ========== FLOW DIAGRAMS ========== */
        .flow-diagram {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0;
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            justify-content: center;
        }

        .flow-box {
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 9pt;
            font-weight: 600;
            text-align: center;
            min-width: 90px;
            border: 2px solid;
            background: #fff;
        }

        .flow-box.blue { border-color: var(--primary); color: var(--primary); }
        .flow-box.accent { border-color: var(--accent); color: #0aa2c0; }
        .flow-box.green { border-color: var(--success); color: var(--success); }
        .flow-box.red { border-color: var(--danger); color: var(--danger); }
        .flow-box.dark { border-color: var(--dark); color: var(--dark); }

        .flow-arrow {
            font-size: 18pt;
            color: #6c757d;
            margin: 0 6px;
            font-weight: 300;
        }

        .flow-arrow-down {
            display: block;
            text-align: center;
            font-size: 18pt;
            color: #6c757d;
            margin: 6px 0;
            width: 100%;
        }

        /* ========== VERTICAL FLOW DIAGRAM ========== */
        .flow-vertical {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
            margin: 20px auto;
            padding: 24px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            max-width: 500px;
        }

        .flow-vertical .flow-box { min-width: 220px; }

        .flow-vertical .flow-arrow-down { width: auto; margin: 4px 0; }

        /* ========== NETWORK DIAGRAM ========== */
        .network-diagram {
            margin: 20px 0;
            padding: 30px 20px;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            border-radius: 10px;
            color: #fff;
        }

        .network-layer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 4px;
        }

        .network-box {
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 9pt;
            font-weight: 600;
            text-align: center;
            min-width: 140px;
            border: 2px solid;
        }

        .network-box.external {
            border-color: var(--danger);
            background: rgba(220,53,69,0.15);
            color: #ff8a95;
        }

        .network-box.perimeter {
            border-color: var(--warning);
            background: rgba(255,193,7,0.12);
            color: #ffd95c;
        }

        .network-box.internal {
            border-color: var(--success);
            background: rgba(25,135,84,0.15);
            color: #75d4a5;
        }

        .network-box.data {
            border-color: var(--primary);
            background: rgba(13,110,253,0.15);
            color: #7cb8ff;
        }

        .network-arrow-down {
            display: block;
            text-align: center;
            font-size: 16pt;
            color: #6c757d;
            margin: 4px 0;
        }

        .network-label {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #6c757d;
            margin-bottom: 4px;
            text-align: center;
        }

        /* ========== ROLE HIERARCHY ========== */
        .role-hierarchy {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            margin: 20px 0;
            padding: 24px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .role-box {
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 10pt;
            text-align: center;
            color: #fff;
            min-width: 160px;
            position: relative;
        }

        .role-box.r-super { background: linear-gradient(135deg, #dc3545, #b02a37); min-width: 240px; }
        .role-box.r-admin { background: linear-gradient(135deg, #e67e22, #d35400); min-width: 220px; }
        .role-box.r-eng { background: linear-gradient(135deg, #0d6efd, #0b5ed7); min-width: 200px; }
        .role-box.r-oper { background: linear-gradient(135deg, #198754, #146c43); min-width: 180px; }
        .role-box.r-viewer { background: linear-gradient(135deg, #6c757d, #565e64); min-width: 160px; }

        .role-arrow { color: #adb5bd; font-size: 14pt; }

        /* ========== CALLOUT BOXES ========== */
        .callout {
            padding: 16px 20px;
            border-radius: 8px;
            margin: 16px 0;
            border-left: 5px solid;
            font-size: 10pt;
        }

        .callout-info {
            background: #cfe2ff;
            border-color: var(--primary);
            color: #084298;
        }

        .callout-warning {
            background: #fff3cd;
            border-color: var(--warning);
            color: #664d03;
        }

        .callout-danger {
            background: #f8d7da;
            border-color: var(--danger);
            color: #842029;
        }

        .callout-success {
            background: #d1e7dd;
            border-color: var(--success);
            color: #0f5132;
        }

        .callout strong { display: block; margin-bottom: 4px; font-size: 10pt; }

        /* ========== STAT CARDS ========== */
        .stats-row {
            display: flex;
            gap: 14px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .stat-card {
            flex: 1;
            min-width: 120px;
            padding: 18px 14px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #e9ecef;
            background: #fff;
        }

        .stat-card .stat-val {
            font-size: 22pt;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
        }

        .stat-card .stat-label {
            font-size: 8pt;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 6px;
        }

        /* ========== CHECKLIST ========== */
        .checklist {
            list-style: none;
            padding: 0;
            margin: 14px 0;
        }

        .checklist li {
            padding: 8px 0 8px 30px;
            position: relative;
            border-bottom: 1px solid #f0f0f0;
            font-size: 10pt;
        }

        .checklist li::before {
            content: '\2610';
            position: absolute;
            left: 4px;
            top: 8px;
            font-size: 14pt;
            color: var(--primary);
        }

        /* ========== INCIDENT PHASES ========== */
        .phase-row {
            display: flex;
            gap: 12px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .phase-card {
            flex: 1;
            min-width: 130px;
            padding: 16px;
            border-radius: 10px;
            text-align: center;
            color: #fff;
            position: relative;
        }

        .phase-card .phase-num {
            font-size: 24pt;
            font-weight: 800;
            opacity: 0.3;
            line-height: 1;
        }

        .phase-card .phase-name {
            font-size: 11pt;
            font-weight: 700;
            margin-top: 4px;
        }

        .phase-card .phase-desc {
            font-size: 8pt;
            margin-top: 6px;
            opacity: 0.85;
            line-height: 1.4;
        }

        .phase-card.p1 { background: linear-gradient(135deg, #dc3545, #b02a37); }
        .phase-card.p2 { background: linear-gradient(135deg, #e67e22, #d35400); }
        .phase-card.p3 { background: linear-gradient(135deg, #0d6efd, #0b5ed7); }
        .phase-card.p4 { background: linear-gradient(135deg, #198754, #146c43); }

        /* ========== TABLE OF CONTENTS ========== */
        .toc {
            margin: 20px 0;
            padding: 24px 30px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .toc-title {
            font-size: 14pt;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 14px;
        }

        .toc ol {
            padding-left: 20px;
            margin: 0;
        }

        .toc li {
            padding: 4px 0;
            font-size: 10pt;
            color: #495057;
        }

        .toc li strong { color: var(--dark); }

        /* ========== FOOTER ========== */
        .doc-footer {
            margin-top: 40px;
            padding-top: 16px;
            border-top: 2px solid var(--primary);
            text-align: center;
            font-size: 8pt;
            color: #6c757d;
            letter-spacing: 0.5px;
        }

        /* ========== SEVERITY DOT ========== */
        .sev-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 6px;
            vertical-align: middle;
        }

        .sev-dot.critical { background: #dc3545; }
        .sev-dot.high { background: #e67e22; }
        .sev-dot.medium { background: #ffc107; }
        .sev-dot.low { background: #198754; }

        /* ========== MISC ========== */
        code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9pt;
            font-family: 'Consolas', 'Courier New', monospace;
            color: #d63384;
        }

        .text-muted { color: #6c757d; }
        .text-primary { color: var(--primary); }
        .text-danger { color: var(--danger); }
        .text-success { color: var(--success); }
        .fw-bold { font-weight: 700; }
        .mb-0 { margin-bottom: 0; }
    </style>
</head>
<body>

<!-- ===================== PRINT BAR ===================== -->
<div class="print-bar">
    <a href="javascript:history.back()" class="back-link">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back
    </a>
    <span class="bar-title">Security Architecture Whitepaper</span>
    <button class="btn-download" onclick="window.print()">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
        Download PDF
    </button>
</div>

<div class="document">

<!-- ===================== COVER PAGE ===================== -->
<div class="cover-page">
    <div class="cover-logo"><span>EC</span></div>
    <div class="cover-company">EnPharChem</div>
    <div class="cover-subtitle">Technologies</div>

    <div class="cover-shield">
        <svg viewBox="0 0 120 140" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M60 5L10 30V65C10 100 60 135 60 135C60 135 110 100 110 65V30L60 5Z" fill="url(#shieldGrad)" stroke="#0d6efd" stroke-width="3"/>
            <path d="M48 70L56 80L76 56" stroke="#fff" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
            <defs>
                <linearGradient id="shieldGrad" x1="10" y1="5" x2="110" y2="135">
                    <stop offset="0%" stop-color="#0d6efd"/>
                    <stop offset="100%" stop-color="#0dcaf0"/>
                </linearGradient>
            </defs>
        </svg>
    </div>

    <div class="cover-title">Security Architecture<br>Whitepaper</div>
    <div class="cover-edition">March 2026 Edition &mdash; Version 2.0</div>

    <div class="cover-classification">Confidential &mdash; Security Documentation</div>

    <div class="cover-meta">
        <div>Document ID: EP-SEC-ARCH-2026-003</div>
        <div>Prepared by: EnPharChem Security Engineering Team</div>
        <div>Last Review: March 2026</div>
    </div>
</div>

<!-- ===================== MAIN CONTENT ===================== -->
<div class="doc-content">

    <!-- TABLE OF CONTENTS -->
    <div class="page-break"></div>
    <div class="toc">
        <div class="toc-title">Table of Contents</div>
        <ol>
            <li><strong>Executive Summary</strong></li>
            <li><strong>Authentication &amp; Access Control</strong>
                <ol style="margin-top:4px;">
                    <li>Authentication Flow</li>
                    <li>Password Policy</li>
                    <li>Session Management</li>
                    <li>Multi-Factor Authentication</li>
                </ol>
            </li>
            <li><strong>Role-Based Access Control (RBAC)</strong></li>
            <li><strong>Data Security</strong>
                <ol style="margin-top:4px;">
                    <li>Encryption at Rest</li>
                    <li>Encryption in Transit</li>
                    <li>Database Security</li>
                    <li>Input Validation &amp; Output Encoding</li>
                    <li>File Upload Security</li>
                </ol>
            </li>
            <li><strong>Network Security Architecture</strong></li>
            <li><strong>Application Security</strong>
                <ol style="margin-top:4px;">
                    <li>OWASP Top 10 Mitigation</li>
                    <li>Security Headers</li>
                    <li>Error Handling</li>
                </ol>
            </li>
            <li><strong>Audit &amp; Compliance</strong>
                <ol style="margin-top:4px;">
                    <li>Audit Logging</li>
                    <li>Compliance Frameworks</li>
                    <li>Data Retention Policies</li>
                </ol>
            </li>
            <li><strong>Incident Response</strong></li>
            <li><strong>Security Recommendations</strong></li>
        </ol>
    </div>

    <!-- ==================== SECTION 1 ==================== -->
    <div class="page-break"></div>
    <h1 class="section-title">1. Executive Summary</h1>

    <p>
        EnPharChem Technologies has implemented a comprehensive <strong>defense-in-depth security architecture</strong> designed to protect critical pharmaceutical and chemical engineering data, simulation models, and operational systems. Our security posture is built upon multiple overlapping layers of protection, ensuring that no single point of failure can compromise the integrity, confidentiality, or availability of our platform.
    </p>
    <p>
        This whitepaper provides a detailed technical overview of the security mechanisms, policies, and architectural decisions that underpin the EnPharChem platform. It is intended for internal security teams, auditors, compliance officers, and enterprise clients evaluating the platform's security posture.
    </p>
    <p>
        Our approach is grounded in industry-recognized frameworks, including alignment with SOC 2 Type II controls, ISO 27001 information security management standards, and NIST 800-53 security controls. Every component of the application stack &mdash; from the network perimeter to the database layer &mdash; has been architected with security as a primary consideration.
    </p>

    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-val">5</div>
            <div class="stat-label">Security Layers</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">5</div>
            <div class="stat-label">RBAC Roles</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="font-size:16pt;">AES-256</div>
            <div class="stat-label">Encryption Standard</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="font-size:16pt;">SOC 2</div>
            <div class="stat-label">Aligned</div>
        </div>
        <div class="stat-card">
            <div class="stat-val" style="font-size:16pt;">TLS 1.3</div>
            <div class="stat-label">Transport Layer</div>
        </div>
    </div>

    <div class="callout callout-info">
        <strong>Defense-in-Depth Layers</strong>
        Network Perimeter Security &rarr; Application Firewall &rarr; Authentication &amp; Authorization &rarr; Data Encryption &rarr; Audit &amp; Monitoring. Each layer operates independently, providing redundant protection in the event that any single layer is bypassed.
    </div>

    <!-- ==================== SECTION 2 ==================== -->
    <div class="page-break"></div>
    <h1 class="section-title">2. Authentication &amp; Access Control</h1>

    <p>
        Authentication is the first and most critical line of defense in the EnPharChem platform. We employ a multi-layered authentication system that combines strong credential management with robust session handling and anti-forgery protections.
    </p>

    <h2 class="subsection-title">2.1 Authentication Flow</h2>
    <p>
        The following diagram illustrates the complete authentication flow from initial user request through to dashboard access. Each step includes a security validation checkpoint that must pass before proceeding to the next stage.
    </p>

    <div class="flow-vertical">
        <div class="flow-box blue">User Request</div>
        <div class="flow-arrow-down">&darr;</div>
        <div class="flow-box accent">Login Form Rendered</div>
        <div class="flow-arrow-down">&darr;</div>
        <div class="flow-box red">CSRF Token Validation</div>
        <div class="flow-arrow-down">&darr;</div>
        <div class="flow-box dark">Credential Lookup (DB Query)</div>
        <div class="flow-arrow-down">&darr;</div>
        <div class="flow-box red">bcrypt Hash Verification</div>
        <div class="flow-arrow-down">&darr;</div>
        <div class="flow-box accent">MFA Challenge (if enabled)</div>
        <div class="flow-arrow-down">&darr;</div>
        <div class="flow-box green">Session Creation &amp; Regeneration</div>
        <div class="flow-arrow-down">&darr;</div>
        <div class="flow-box green">Dashboard Access Granted</div>
    </div>

    <div class="callout callout-danger">
        <strong>Security Gate</strong>
        If CSRF token validation or bcrypt verification fails, the request is immediately rejected with a generic error message. No information about which step failed is disclosed to the client, preventing user enumeration attacks.
    </div>

    <h2 class="subsection-title">2.2 Password Policy</h2>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Policy</th><th>Requirement</th><th>Implementation</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>Minimum Length</td>
                <td>8 characters</td>
                <td>Server-side validation + client-side hint</td>
            </tr>
            <tr>
                <td>Hashing Algorithm</td>
                <td>bcrypt</td>
                <td><code>password_hash()</code> with <code>PASSWORD_BCRYPT</code></td>
            </tr>
            <tr>
                <td>Cost Factor</td>
                <td>12 rounds</td>
                <td>Configurable via <code>['cost' =&gt; 12]</code></td>
            </tr>
            <tr>
                <td>Plaintext Storage</td>
                <td><span class="badge badge-red">Prohibited</span></td>
                <td>No plaintext passwords stored anywhere in the system</td>
            </tr>
            <tr>
                <td>Password Verification</td>
                <td>Timing-safe comparison</td>
                <td><code>password_verify()</code> with constant-time comparison</td>
            </tr>
            <tr>
                <td>Rehashing</td>
                <td>Automatic on login</td>
                <td><code>password_needs_rehash()</code> check on each authentication</td>
            </tr>
        </tbody>
    </table>

    <h2 class="subsection-title">2.3 Session Management</h2>

    <p>
        EnPharChem utilizes PHP's native session management with hardened configuration to prevent session hijacking, fixation, and replay attacks.
    </p>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Parameter</th><th>Value</th><th>Purpose</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>Session Timeout</td>
                <td>8 hours</td>
                <td>Automatic logout after period of inactivity</td>
            </tr>
            <tr>
                <td>Secure Cookie Flag</td>
                <td><span class="badge badge-green">Enabled</span></td>
                <td>Cookies only transmitted over HTTPS</td>
            </tr>
            <tr>
                <td>HttpOnly Flag</td>
                <td><span class="badge badge-green">Enabled</span></td>
                <td>Prevents JavaScript access to session cookies</td>
            </tr>
            <tr>
                <td>SameSite Attribute</td>
                <td><code>Strict</code></td>
                <td>Mitigates CSRF via cookie restriction</td>
            </tr>
            <tr>
                <td>Session Regeneration</td>
                <td>On authentication</td>
                <td><code>session_regenerate_id(true)</code> prevents fixation</td>
            </tr>
            <tr>
                <td>Session Storage</td>
                <td>Server-side file system</td>
                <td>Session data never exposed to client</td>
            </tr>
        </tbody>
    </table>

    <h2 class="subsection-title">2.4 Multi-Factor Authentication</h2>

    <p>
        EnPharChem supports Time-based One-Time Password (TOTP) as a second authentication factor, compatible with standard authenticator applications such as Google Authenticator, Authy, and Microsoft Authenticator.
    </p>

    <table class="doc-table no-break">
        <thead>
            <tr><th>MFA Feature</th><th>Details</th></tr>
        </thead>
        <tbody>
            <tr><td>Algorithm</td><td>TOTP (RFC 6238) with SHA-1 HMAC, 6-digit codes, 30-second window</td></tr>
            <tr><td>Enrollment</td><td>QR code provisioning via <code>otpauth://</code> URI scheme</td></tr>
            <tr><td>Backup Codes</td><td>10 single-use recovery codes generated at enrollment, bcrypt-hashed at rest</td></tr>
            <tr><td>Enforcement</td><td>Optional per-user, mandatory for Superuser and Admin roles</td></tr>
            <tr><td>Rate Limiting</td><td>5 failed MFA attempts triggers 15-minute account lockout</td></tr>
        </tbody>
    </table>

    <!-- ==================== SECTION 3 ==================== -->
    <div class="page-break"></div>
    <h1 class="section-title">3. Role-Based Access Control (RBAC)</h1>

    <p>
        EnPharChem implements a hierarchical RBAC model with five distinct roles. Each role inherits the permissions of all roles below it in the hierarchy, with additional privileges granted at each level. This ensures the principle of least privilege is enforced across the platform.
    </p>

    <h2 class="subsection-title">3.1 Role Hierarchy</h2>

    <div class="role-hierarchy">
        <div class="role-box r-super">Superuser <span style="font-weight:400;font-size:8pt;opacity:0.8;">&mdash; Full System Access</span></div>
        <div class="role-arrow">&darr;</div>
        <div class="role-box r-admin">Admin <span style="font-weight:400;font-size:8pt;opacity:0.8;">&mdash; Administrative Control</span></div>
        <div class="role-arrow">&darr;</div>
        <div class="role-box r-eng">Engineer <span style="font-weight:400;font-size:8pt;opacity:0.8;">&mdash; Technical Operations</span></div>
        <div class="role-arrow">&darr;</div>
        <div class="role-box r-oper">Operator <span style="font-weight:400;font-size:8pt;opacity:0.8;">&mdash; Daily Operations</span></div>
        <div class="role-arrow">&darr;</div>
        <div class="role-box r-viewer">Viewer <span style="font-weight:400;font-size:8pt;opacity:0.8;">&mdash; Read-Only Access</span></div>
    </div>

    <h2 class="subsection-title">3.2 Permissions Matrix</h2>

    <table class="doc-table">
        <thead>
            <tr>
                <th>Feature</th>
                <th>Superuser</th>
                <th>Admin</th>
                <th>Engineer</th>
                <th>Operator</th>
                <th>Viewer</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Dashboard</td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
            </tr>
            <tr>
                <td>Projects (View)</td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
            </tr>
            <tr>
                <td>Projects (Create/Edit)</td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="cross">&#10007;</span></td>
                <td><span class="cross">&#10007;</span></td>
            </tr>
            <tr>
                <td>Simulations</td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="partial">&#9679;</span></td>
                <td><span class="cross">&#10007;</span></td>
            </tr>
            <tr>
                <td>Modules Configuration</td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="cross">&#10007;</span></td>
                <td><span class="cross">&#10007;</span></td>
            </tr>
            <tr>
                <td>Control Panel</td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="partial">&#9679;</span></td>
                <td><span class="cross">&#10007;</span></td>
                <td><span class="cross">&#10007;</span></td>
            </tr>
            <tr>
                <td>Admin Panel</td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="cross">&#10007;</span></td>
                <td><span class="cross">&#10007;</span></td>
                <td><span class="cross">&#10007;</span></td>
            </tr>
            <tr>
                <td>Data Management</td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="partial">&#9679;</span></td>
                <td><span class="cross">&#10007;</span></td>
            </tr>
            <tr>
                <td>User Management</td>
                <td><span class="check">&#10003;</span></td>
                <td><span class="partial">&#9679;</span></td>
                <td><span class="cross">&#10007;</span></td>
                <td><span class="cross">&#10007;</span></td>
                <td><span class="cross">&#10007;</span></td>
            </tr>
        </tbody>
    </table>

    <p class="text-muted" style="font-size:9pt;">
        <span class="check">&#10003;</span> = Full Access &nbsp;&nbsp;
        <span class="partial">&#9679;</span> = Limited Access &nbsp;&nbsp;
        <span class="cross">&#10007;</span> = No Access
    </p>

    <h2 class="subsection-title">3.3 Role Descriptions</h2>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Role</th><th>Description</th><th>Typical Assignment</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="badge badge-red">Superuser</span></td>
                <td>Full unrestricted access to all platform features, including system configuration, user management with role elevation, and audit log access. Can manage all roles including other Superusers.</td>
                <td>CTO, Lead Security Officer</td>
            </tr>
            <tr>
                <td><span class="badge badge-yellow">Admin</span></td>
                <td>Administrative access to platform management, user provisioning (up to Engineer level), and configuration of operational parameters. Cannot elevate users to Superuser.</td>
                <td>IT Manager, Department Head</td>
            </tr>
            <tr>
                <td><span class="badge badge-blue">Engineer</span></td>
                <td>Full access to technical features including project creation, simulation execution, and module configuration. Read-only access to select control panel features. No user management capabilities.</td>
                <td>Chemical Engineer, Process Engineer</td>
            </tr>
            <tr>
                <td><span class="badge badge-green">Operator</span></td>
                <td>Day-to-day operational access. Can view projects, run pre-configured simulations, and read operational data. Cannot create or modify project structures or configurations.</td>
                <td>Plant Operator, Lab Technician</td>
            </tr>
            <tr>
                <td><span class="badge" style="background:#e9ecef;color:#495057;">Viewer</span></td>
                <td>Read-only access to dashboards and project summaries. Intended for stakeholders requiring visibility without operational capability. Cannot execute any write operations.</td>
                <td>Auditor, External Consultant</td>
            </tr>
        </tbody>
    </table>

    <!-- ==================== SECTION 4 ==================== -->
    <div class="page-break"></div>
    <h1 class="section-title">4. Data Security</h1>

    <p>
        Data security at EnPharChem is implemented across multiple layers, covering data at rest, data in transit, database interactions, and input/output handling. Each layer employs industry-standard mechanisms to ensure confidentiality and integrity.
    </p>

    <h2 class="subsection-title">4.1 Encryption at Rest</h2>

    <p>
        Sensitive data stored within the EnPharChem platform is protected using strong encryption algorithms. We distinguish between reversible encryption (for data that must be retrieved) and irreversible hashing (for credentials).
    </p>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Data Type</th><th>Algorithm</th><th>Key Size</th><th>Notes</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>Sensitive Fields (API keys, tokens)</td>
                <td>AES-256-CBC</td>
                <td>256-bit</td>
                <td>OpenSSL implementation with unique IV per record</td>
            </tr>
            <tr>
                <td>Passwords</td>
                <td>bcrypt</td>
                <td>N/A (hash)</td>
                <td>Cost factor 12, automatically salted</td>
            </tr>
            <tr>
                <td>MFA Backup Codes</td>
                <td>bcrypt</td>
                <td>N/A (hash)</td>
                <td>One-way hash, codes displayed once at generation</td>
            </tr>
            <tr>
                <td>Database Backups</td>
                <td>AES-256-GCM</td>
                <td>256-bit</td>
                <td>Encrypted at rest with key rotation every 90 days</td>
            </tr>
        </tbody>
    </table>

    <div class="callout callout-warning">
        <strong>Key Management</strong>
        Encryption keys are stored in environment variables outside the web root, never in source code or configuration files accessible via HTTP. Key rotation is performed quarterly with backward compatibility for decrypting older records during the transition period.
    </div>

    <h2 class="subsection-title">4.2 Encryption in Transit</h2>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Control</th><th>Configuration</th><th>Status</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>TLS Version</td>
                <td>TLS 1.3 (minimum TLS 1.2)</td>
                <td><span class="badge badge-green">Active</span></td>
            </tr>
            <tr>
                <td>HTTPS Enforcement</td>
                <td>HTTP to HTTPS redirect via <code>.htaccess</code> and server config</td>
                <td><span class="badge badge-green">Active</span></td>
            </tr>
            <tr>
                <td>HSTS Header</td>
                <td><code>Strict-Transport-Security: max-age=31536000; includeSubDomains</code></td>
                <td><span class="badge badge-green">Active</span></td>
            </tr>
            <tr>
                <td>Certificate</td>
                <td>SHA-256 RSA 2048-bit, auto-renewed via Let's Encrypt or enterprise CA</td>
                <td><span class="badge badge-green">Active</span></td>
            </tr>
            <tr>
                <td>Cipher Suites</td>
                <td>TLS_AES_256_GCM_SHA384, TLS_CHACHA20_POLY1305_SHA256</td>
                <td><span class="badge badge-green">Active</span></td>
            </tr>
        </tbody>
    </table>

    <h2 class="subsection-title">4.3 Database Security</h2>

    <p>
        All database interactions in EnPharChem are handled through PHP Data Objects (PDO) with prepared statements, completely eliminating the risk of SQL injection attacks. The application follows the principle of least privilege for database user permissions.
    </p>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Control</th><th>Implementation</th><th>Risk Mitigated</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>Parameterized Queries</td>
                <td>PDO prepared statements with bound parameters for all queries</td>
                <td>SQL Injection</td>
            </tr>
            <tr>
                <td>Least Privilege</td>
                <td>Application DB user has SELECT, INSERT, UPDATE, DELETE only; no DROP, ALTER, or GRANT</td>
                <td>Privilege Escalation</td>
            </tr>
            <tr>
                <td>Connection Security</td>
                <td>MySQL connections over localhost socket; remote access disabled</td>
                <td>Network-based DB attacks</td>
            </tr>
            <tr>
                <td>Query Logging</td>
                <td>Slow query log enabled; general query log in audit mode</td>
                <td>Performance &amp; forensic analysis</td>
            </tr>
            <tr>
                <td>Backup Encryption</td>
                <td>Automated daily backups encrypted with AES-256-GCM</td>
                <td>Data exfiltration from backups</td>
            </tr>
        </tbody>
    </table>

    <h2 class="subsection-title">4.4 Input Validation &amp; Output Encoding</h2>

    <p>
        EnPharChem implements strict input validation on all user-supplied data and consistent output encoding to prevent cross-site scripting (XSS) and injection attacks.
    </p>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Control</th><th>Implementation</th><th>Details</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>Output Encoding</td>
                <td><code>htmlspecialchars($data, ENT_QUOTES, 'UTF-8')</code></td>
                <td>Applied to all dynamic output rendered in HTML context</td>
            </tr>
            <tr>
                <td>CSRF Protection</td>
                <td>Synchronizer token pattern</td>
                <td>Unique per-session token validated on all state-changing requests</td>
            </tr>
            <tr>
                <td>Input Type Validation</td>
                <td>PHP filter functions and type casting</td>
                <td><code>filter_input()</code>, <code>intval()</code>, regex patterns for structured input</td>
            </tr>
            <tr>
                <td>Content-Type Validation</td>
                <td>Request header verification</td>
                <td>API endpoints validate <code>Content-Type</code> header matches expected type</td>
            </tr>
            <tr>
                <td>Length Limits</td>
                <td>Server-side enforcement</td>
                <td>Maximum length enforced for all text inputs to prevent buffer-style attacks</td>
            </tr>
        </tbody>
    </table>

    <h2 class="subsection-title">4.5 File Upload Security</h2>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Control</th><th>Configuration</th><th>Purpose</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>MIME Type Validation</td>
                <td>Whitelist: PDF, CSV, XLSX, PNG, JPG</td>
                <td>Prevent executable uploads</td>
            </tr>
            <tr>
                <td>File Extension Check</td>
                <td>Double-extension blocked (e.g., <code>.php.jpg</code>)</td>
                <td>Prevent extension spoofing</td>
            </tr>
            <tr>
                <td>Magic Byte Verification</td>
                <td><code>finfo_file()</code> check against actual file content</td>
                <td>Verify true file type regardless of extension</td>
            </tr>
            <tr>
                <td>Size Limit</td>
                <td>10 MB maximum per file</td>
                <td>Prevent denial-of-service via large uploads</td>
            </tr>
            <tr>
                <td>Storage Location</td>
                <td>Outside web root in sandboxed directory</td>
                <td>Files not directly accessible via URL</td>
            </tr>
            <tr>
                <td>Filename Sanitization</td>
                <td>UUID-based renaming, original name stored in DB</td>
                <td>Prevent directory traversal and name collisions</td>
            </tr>
        </tbody>
    </table>

    <!-- ==================== SECTION 5 ==================== -->
    <div class="page-break"></div>
    <h1 class="section-title">5. Network Security Architecture</h1>

    <p>
        The EnPharChem platform operates within a layered network architecture designed to isolate public-facing components from internal application logic and data stores. Each network zone enforces strict access controls and traffic filtering.
    </p>

    <h2 class="subsection-title">5.1 Network Topology</h2>

    <div class="network-diagram">
        <div class="network-label">External Zone (Untrusted)</div>
        <div class="network-layer">
            <div class="network-box external">Internet<br><span style="font-size:7pt;opacity:0.7;">Public Traffic</span></div>
        </div>
        <div class="network-arrow-down">&darr;</div>

        <div class="network-label">DMZ - Perimeter Security</div>
        <div class="network-layer">
            <div class="network-box perimeter">Firewall<br><span style="font-size:7pt;opacity:0.7;">iptables / WAF</span></div>
        </div>
        <div class="network-arrow-down">&darr;</div>
        <div class="network-layer">
            <div class="network-box perimeter">Load Balancer<br><span style="font-size:7pt;opacity:0.7;">SSL Termination</span></div>
        </div>
        <div class="network-arrow-down">&darr;</div>

        <div class="network-label">Application Zone (Internal)</div>
        <div class="network-layer">
            <div class="network-box internal">Web Server<br><span style="font-size:7pt;opacity:0.7;">Apache 2.4</span></div>
        </div>
        <div class="network-arrow-down">&darr;</div>
        <div class="network-layer">
            <div class="network-box internal">Application Layer<br><span style="font-size:7pt;opacity:0.7;">PHP 8.x Runtime</span></div>
        </div>
        <div class="network-arrow-down">&darr;</div>

        <div class="network-label">Data Zone (Restricted)</div>
        <div class="network-layer">
            <div class="network-box data">Database Server<br><span style="font-size:7pt;opacity:0.7;">MySQL 8.x</span></div>
        </div>
        <div class="network-arrow-down">&darr;</div>
        <div class="network-layer">
            <div class="network-box data">Backup Storage<br><span style="font-size:7pt;opacity:0.7;">Encrypted / Off-site</span></div>
        </div>
    </div>

    <h2 class="subsection-title">5.2 DMZ Configuration</h2>

    <p>
        The Demilitarized Zone (DMZ) acts as a buffer between the public internet and internal systems. Only the web server and load balancer are exposed within the DMZ. The application layer and database reside in an isolated internal network segment with no direct internet access. All outbound traffic from the data zone is restricted to approved update and backup endpoints only.
    </p>

    <h2 class="subsection-title">5.3 Port Security</h2>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Port</th><th>Service</th><th>Access Level</th><th>Notes</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><code>443</code></td>
                <td>HTTPS</td>
                <td><span class="badge badge-green">Public</span></td>
                <td>Primary application access; TLS 1.3</td>
            </tr>
            <tr>
                <td><code>80</code></td>
                <td>HTTP</td>
                <td><span class="badge badge-yellow">Redirect Only</span></td>
                <td>301 redirect to HTTPS; no content served</td>
            </tr>
            <tr>
                <td><code>22</code></td>
                <td>SSH</td>
                <td><span class="badge badge-red">Restricted</span></td>
                <td>Key-based auth only; IP whitelist; fail2ban enabled</td>
            </tr>
            <tr>
                <td><code>3306</code></td>
                <td>MySQL</td>
                <td><span class="badge badge-red">Internal Only</span></td>
                <td>Bound to localhost; remote access disabled</td>
            </tr>
            <tr>
                <td><code>8080</code></td>
                <td>Admin Panel</td>
                <td><span class="badge badge-red">VPN Only</span></td>
                <td>Accessible only via corporate VPN tunnel</td>
            </tr>
            <tr>
                <td><code>*</code></td>
                <td>All Others</td>
                <td><span class="badge badge-red">Blocked</span></td>
                <td>Default deny policy; all unlisted ports are closed</td>
            </tr>
        </tbody>
    </table>

    <!-- ==================== SECTION 6 ==================== -->
    <div class="page-break"></div>
    <h1 class="section-title">6. Application Security</h1>

    <h2 class="subsection-title">6.1 OWASP Top 10 Mitigation</h2>

    <p>
        The following table maps each OWASP Top 10 vulnerability category to the specific controls implemented within the EnPharChem platform.
    </p>

    <table class="doc-table">
        <thead>
            <tr><th style="width:30%;">OWASP Vulnerability</th><th style="width:35%;">EnPharChem Protection</th><th style="width:35%;">Implementation Detail</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>A01: Broken Access Control</strong></td>
                <td>RBAC with 5 hierarchical roles</td>
                <td>Server-side authorization checks on every request; role verified from session before granting access</td>
            </tr>
            <tr>
                <td><strong>A02: Cryptographic Failures</strong></td>
                <td>AES-256 encryption, bcrypt hashing</td>
                <td>TLS 1.3 in transit; AES-256-CBC at rest; no sensitive data in URLs or logs</td>
            </tr>
            <tr>
                <td><strong>A03: Injection</strong></td>
                <td>PDO prepared statements</td>
                <td>All database queries use parameterized statements; no string concatenation in SQL</td>
            </tr>
            <tr>
                <td><strong>A04: Insecure Design</strong></td>
                <td>Threat modeling, secure SDLC</td>
                <td>Security requirements in design phase; defense-in-depth architecture</td>
            </tr>
            <tr>
                <td><strong>A05: Security Misconfiguration</strong></td>
                <td><code>.htaccess</code> hardening</td>
                <td>Directory listing disabled; error display off; unnecessary modules removed</td>
            </tr>
            <tr>
                <td><strong>A06: Vulnerable Components</strong></td>
                <td>Dependency monitoring</td>
                <td>Regular Composer audit; outdated library checks; automated CVE scanning</td>
            </tr>
            <tr>
                <td><strong>A07: Auth Failures</strong></td>
                <td>bcrypt + session management</td>
                <td>Rate limiting on login; account lockout; session regeneration; secure cookie flags</td>
            </tr>
            <tr>
                <td><strong>A08: Data Integrity Failures</strong></td>
                <td>CSRF tokens, input validation</td>
                <td>Signed tokens on all forms; integrity checks on critical operations</td>
            </tr>
            <tr>
                <td><strong>A09: Logging &amp; Monitoring</strong></td>
                <td>Comprehensive audit logging</td>
                <td>All auth events, data changes, and admin actions logged with user, IP, and timestamp</td>
            </tr>
            <tr>
                <td><strong>A10: SSRF</strong></td>
                <td>URL validation, network restrictions</td>
                <td>Outbound requests restricted to whitelist; no user-supplied URLs processed without validation</td>
            </tr>
        </tbody>
    </table>

    <h2 class="subsection-title">6.2 Security Headers</h2>

    <p>
        The following HTTP security headers are enforced via <code>.htaccess</code> configuration and applied to all responses served by the application.
    </p>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Header</th><th>Value</th><th>Purpose</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><code>X-Content-Type-Options</code></td>
                <td><code>nosniff</code></td>
                <td>Prevents MIME-type sniffing; browser respects declared Content-Type</td>
            </tr>
            <tr>
                <td><code>X-Frame-Options</code></td>
                <td><code>DENY</code></td>
                <td>Prevents clickjacking by disallowing iframe embedding</td>
            </tr>
            <tr>
                <td><code>X-XSS-Protection</code></td>
                <td><code>1; mode=block</code></td>
                <td>Enables browser XSS filter with blocking mode (legacy browsers)</td>
            </tr>
            <tr>
                <td><code>Content-Security-Policy</code></td>
                <td><code>default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:</code></td>
                <td>Restricts resource origins; mitigates XSS and data injection</td>
            </tr>
            <tr>
                <td><code>Referrer-Policy</code></td>
                <td><code>strict-origin-when-cross-origin</code></td>
                <td>Controls referrer information sent with requests</td>
            </tr>
            <tr>
                <td><code>Permissions-Policy</code></td>
                <td><code>camera=(), microphone=(), geolocation=()</code></td>
                <td>Disables unnecessary browser APIs</td>
            </tr>
        </tbody>
    </table>

    <h2 class="subsection-title">6.3 Error Handling</h2>

    <p>
        EnPharChem implements strict error handling policies to prevent information leakage while maintaining diagnostic capability for authorized personnel.
    </p>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Control</th><th>Production Behavior</th><th>Development Behavior</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>PHP Error Display</td>
                <td><code>display_errors = Off</code></td>
                <td><code>display_errors = On</code></td>
            </tr>
            <tr>
                <td>Error Logging</td>
                <td>Errors logged to secure file outside web root</td>
                <td>Logged to console and file</td>
            </tr>
            <tr>
                <td>Stack Traces</td>
                <td><span class="badge badge-red">Never Exposed</span></td>
                <td>Displayed for debugging</td>
            </tr>
            <tr>
                <td>403 Forbidden</td>
                <td>Custom branded error page with no technical details</td>
                <td>Standard message</td>
            </tr>
            <tr>
                <td>404 Not Found</td>
                <td>Custom branded error page; does not reveal directory structure</td>
                <td>Standard message</td>
            </tr>
            <tr>
                <td>500 Server Error</td>
                <td>Generic "something went wrong" page; incident ID for support</td>
                <td>Full error details</td>
            </tr>
            <tr>
                <td>Database Errors</td>
                <td>Caught via try/catch; generic message returned to user</td>
                <td>PDO exception details shown</td>
            </tr>
        </tbody>
    </table>

    <!-- ==================== SECTION 7 ==================== -->
    <div class="page-break"></div>
    <h1 class="section-title">7. Audit &amp; Compliance</h1>

    <h2 class="subsection-title">7.1 Audit Logging</h2>

    <p>
        EnPharChem maintains a comprehensive audit trail through the <code>audit_log</code> database table. Every security-relevant action is recorded with full context, enabling forensic analysis, compliance reporting, and anomaly detection.
    </p>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Field</th><th>Type</th><th>Description</th></tr>
        </thead>
        <tbody>
            <tr><td><code>id</code></td><td>INT (PK, Auto)</td><td>Unique log entry identifier</td></tr>
            <tr><td><code>user_id</code></td><td>INT (FK)</td><td>ID of the user who performed the action</td></tr>
            <tr><td><code>username</code></td><td>VARCHAR(100)</td><td>Username at time of action (denormalized for historical integrity)</td></tr>
            <tr><td><code>action</code></td><td>VARCHAR(50)</td><td>Action type: LOGIN, LOGOUT, CREATE, UPDATE, DELETE, EXPORT, etc.</td></tr>
            <tr><td><code>entity_type</code></td><td>VARCHAR(50)</td><td>Target entity: user, project, simulation, module, setting, etc.</td></tr>
            <tr><td><code>entity_id</code></td><td>INT</td><td>ID of the affected entity</td></tr>
            <tr><td><code>details</code></td><td>TEXT</td><td>JSON-encoded description of changes (old value / new value)</td></tr>
            <tr><td><code>ip_address</code></td><td>VARCHAR(45)</td><td>Client IP address (IPv4/IPv6 compatible)</td></tr>
            <tr><td><code>user_agent</code></td><td>VARCHAR(255)</td><td>Browser/client user agent string</td></tr>
            <tr><td><code>created_at</code></td><td>TIMESTAMP</td><td>UTC timestamp of the event</td></tr>
        </tbody>
    </table>

    <div class="callout callout-info">
        <strong>Audit Log Integrity</strong>
        The audit log table has restricted permissions: the application database user has INSERT-only access. Audit records cannot be modified or deleted by the application. Only the DBA role (separate credentials) has SELECT access for reporting. This ensures tamper-resistant logging.
    </div>

    <p><strong>Logged Events Include:</strong></p>
    <ul style="margin: 8px 0 16px 20px; font-size:10pt;">
        <li>All authentication attempts (successful and failed)</li>
        <li>Password changes and MFA enrollment/removal</li>
        <li>User account creation, modification, and deactivation</li>
        <li>Role assignments and permission changes</li>
        <li>Project and simulation create/update/delete operations</li>
        <li>Data exports and report generation</li>
        <li>Configuration changes to system settings</li>
        <li>File uploads and deletions</li>
    </ul>

    <h2 class="subsection-title">7.2 Compliance Frameworks</h2>

    <table class="doc-table">
        <thead>
            <tr><th>Framework</th><th>Alignment Status</th><th>Key Controls</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>SOC 2 Type II</strong></td>
                <td><span class="badge badge-green">Aligned</span></td>
                <td>Access controls, encryption, audit logging, incident response, change management, availability monitoring</td>
            </tr>
            <tr>
                <td><strong>ISO 27001</strong></td>
                <td><span class="badge badge-yellow">Partial</span></td>
                <td>Information security policy, risk assessment, access control (A.9), cryptography (A.10), operations security (A.12)</td>
            </tr>
            <tr>
                <td><strong>NIST 800-53</strong></td>
                <td><span class="badge badge-green">Aligned</span></td>
                <td>AC (Access Control), AU (Audit), IA (Identification &amp; Authentication), SC (System &amp; Communications Protection)</td>
            </tr>
            <tr>
                <td><strong>GDPR</strong></td>
                <td><span class="badge badge-green">Aligned</span></td>
                <td>Data minimization, right to erasure support, data processing records, encryption, breach notification procedures</td>
            </tr>
            <tr>
                <td><strong>NERC CIP</strong></td>
                <td><span class="badge badge-yellow">Partial</span></td>
                <td>Electronic security perimeters (CIP-005), system security management (CIP-007), incident reporting (CIP-008)</td>
            </tr>
        </tbody>
    </table>

    <h2 class="subsection-title">7.3 Data Retention Policies</h2>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Data Category</th><th>Retention Period</th><th>Disposal Method</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>Audit Logs</td>
                <td>7 years</td>
                <td>Archived to encrypted cold storage after 1 year; purged after 7 years</td>
            </tr>
            <tr>
                <td>Session Data</td>
                <td>8 hours (active) / 24 hours (expired)</td>
                <td>Automatic garbage collection via PHP session handler</td>
            </tr>
            <tr>
                <td>User Accounts (deactivated)</td>
                <td>2 years</td>
                <td>Soft-deleted with anonymization after 90 days; hard-deleted after 2 years</td>
            </tr>
            <tr>
                <td>Project Data</td>
                <td>Duration of contract + 5 years</td>
                <td>Client-initiated deletion with verification; cryptographic erasure of encryption keys</td>
            </tr>
            <tr>
                <td>Database Backups</td>
                <td>90 days rolling</td>
                <td>Automated purge of backups older than 90 days; secure overwrite</td>
            </tr>
            <tr>
                <td>Error Logs</td>
                <td>30 days</td>
                <td>Log rotation with automatic deletion</td>
            </tr>
        </tbody>
    </table>

    <!-- ==================== SECTION 8 ==================== -->
    <div class="page-break"></div>
    <h1 class="section-title">8. Incident Response</h1>

    <p>
        EnPharChem maintains a formal Incident Response (IR) plan aligned with NIST SP 800-61 guidelines. The IR process is structured into four phases, each with defined responsibilities, actions, and escalation paths.
    </p>

    <h2 class="subsection-title">8.1 Incident Response Phases</h2>

    <div class="phase-row">
        <div class="phase-card p1">
            <div class="phase-num">01</div>
            <div class="phase-name">Identification</div>
            <div class="phase-desc">Detect and confirm the security event. Analyze audit logs, alerts, and anomalies. Classify incident severity and assign IR team lead.</div>
        </div>
        <div class="phase-card p2">
            <div class="phase-num">02</div>
            <div class="phase-name">Containment</div>
            <div class="phase-desc">Isolate affected systems. Revoke compromised credentials. Implement short-term containment (network isolation) and long-term containment (patching).</div>
        </div>
        <div class="phase-card p3">
            <div class="phase-num">03</div>
            <div class="phase-name">Eradication</div>
            <div class="phase-desc">Remove the root cause. Eliminate malware, close vulnerabilities, and verify remediation. Update security controls to prevent recurrence.</div>
        </div>
        <div class="phase-card p4">
            <div class="phase-num">04</div>
            <div class="phase-name">Recovery</div>
            <div class="phase-desc">Restore systems to normal operation. Validate integrity of data and services. Monitor for re-compromise. Conduct post-incident review.</div>
        </div>
    </div>

    <h2 class="subsection-title">8.2 Severity Levels &amp; Response Times</h2>

    <table class="doc-table">
        <thead>
            <tr><th>Severity</th><th>Classification</th><th>Description</th><th>Response Time</th><th>Escalation</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="sev-dot critical"></span><strong>P1 - Critical</strong></td>
                <td><span class="badge badge-red">Critical</span></td>
                <td>Active data breach, system compromise, ransomware. Production data at immediate risk.</td>
                <td><strong>15 minutes</strong></td>
                <td>CISO + Executive Team + Legal</td>
            </tr>
            <tr>
                <td><span class="sev-dot high"></span><strong>P2 - High</strong></td>
                <td><span class="badge badge-yellow">High</span></td>
                <td>Exploited vulnerability, unauthorized access detected, significant service degradation.</td>
                <td><strong>1 hour</strong></td>
                <td>Security Lead + IT Manager</td>
            </tr>
            <tr>
                <td><span class="sev-dot medium"></span><strong>P3 - Medium</strong></td>
                <td><span class="badge badge-blue">Medium</span></td>
                <td>Suspicious activity, failed brute-force attempts, policy violations, non-critical vulnerability discovered.</td>
                <td><strong>4 hours</strong></td>
                <td>Security Team</td>
            </tr>
            <tr>
                <td><span class="sev-dot low"></span><strong>P4 - Low</strong></td>
                <td><span class="badge badge-green">Low</span></td>
                <td>Informational findings, minor policy deviations, scan results with no active exploitation.</td>
                <td><strong>24 hours</strong></td>
                <td>IT Operations</td>
            </tr>
        </tbody>
    </table>

    <div class="callout callout-danger">
        <strong>Breach Notification</strong>
        In the event of a confirmed data breach involving personal or regulated data, EnPharChem will notify affected parties within 72 hours in compliance with GDPR Article 33 and applicable regulatory requirements. Legal counsel is engaged immediately upon P1 classification.
    </div>

    <!-- ==================== SECTION 9 ==================== -->
    <div class="page-break"></div>
    <h1 class="section-title">9. Security Recommendations</h1>

    <h2 class="subsection-title">9.1 Hardening Checklist</h2>

    <p>
        The following checklist outlines recommended security hardening measures for ongoing maintenance and improvement of the EnPharChem platform security posture.
    </p>

    <ul class="checklist">
        <li><strong>Enforce MFA for all user roles</strong> &mdash; Extend mandatory TOTP enrollment from Admin/Superuser to all active users, including Operators and Viewers, to eliminate password-only authentication vectors.</li>
        <li><strong>Implement automated dependency scanning</strong> &mdash; Integrate <code>composer audit</code> and Snyk or similar CVE scanning into the CI/CD pipeline to detect vulnerable libraries before deployment.</li>
        <li><strong>Deploy Web Application Firewall (WAF)</strong> &mdash; Add ModSecurity or cloud-based WAF with OWASP Core Rule Set (CRS) to detect and block common attack patterns at the network edge.</li>
        <li><strong>Enable Content Security Policy reporting</strong> &mdash; Configure CSP <code>report-uri</code> directive to collect violation reports, enabling detection of XSS attempts and misconfigured resources.</li>
        <li><strong>Implement rate limiting on all endpoints</strong> &mdash; Deploy application-level rate limiting beyond login (API endpoints, data exports, search queries) to mitigate automated abuse and scraping.</li>
        <li><strong>Conduct annual penetration testing</strong> &mdash; Engage third-party security firm for comprehensive black-box and gray-box penetration testing with formal remediation tracking.</li>
        <li><strong>Establish a bug bounty program</strong> &mdash; Consider a private bug bounty program for trusted researchers to supplement internal security testing with external expertise.</li>
        <li><strong>Implement database activity monitoring (DAM)</strong> &mdash; Deploy real-time database monitoring to detect anomalous query patterns, unauthorized access attempts, and data exfiltration indicators.</li>
        <li><strong>Harden PHP configuration</strong> &mdash; Disable dangerous functions (<code>exec</code>, <code>system</code>, <code>passthru</code>, <code>shell_exec</code>), set <code>open_basedir</code>, and restrict <code>allow_url_include</code> to <code>Off</code>.</li>
        <li><strong>Implement SIEM integration</strong> &mdash; Forward audit logs and security events to a centralized Security Information and Event Management system for correlation, alerting, and long-term analysis.</li>
    </ul>

    <h2 class="subsection-title">9.2 Security Review Schedule</h2>

    <table class="doc-table no-break">
        <thead>
            <tr><th>Review Activity</th><th>Frequency</th><th>Responsible Team</th><th>Deliverable</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>Vulnerability Scanning</td>
                <td>Weekly (automated)</td>
                <td>Security Engineering</td>
                <td>Scan report with remediation priorities</td>
            </tr>
            <tr>
                <td>Access Control Review</td>
                <td>Monthly</td>
                <td>IT Administration</td>
                <td>User access audit report; stale accounts flagged</td>
            </tr>
            <tr>
                <td>Dependency Audit</td>
                <td>Monthly</td>
                <td>Development Team</td>
                <td>Composer audit output; CVE assessment</td>
            </tr>
            <tr>
                <td>Security Configuration Review</td>
                <td>Quarterly</td>
                <td>Security Engineering</td>
                <td>Configuration compliance checklist</td>
            </tr>
            <tr>
                <td>Penetration Testing</td>
                <td>Annually</td>
                <td>Third-Party Vendor</td>
                <td>Pentest report with findings and remediation plan</td>
            </tr>
            <tr>
                <td>Incident Response Drill</td>
                <td>Semi-Annually</td>
                <td>Security + IT Operations</td>
                <td>Tabletop exercise report; IR plan updates</td>
            </tr>
            <tr>
                <td>Full Security Architecture Review</td>
                <td>Annually</td>
                <td>CISO + Security Engineering</td>
                <td>Updated security architecture whitepaper</td>
            </tr>
        </tbody>
    </table>

    <!-- ==================== FOOTER ==================== -->
    <div class="doc-footer">
        <p>&copy; 2026 EnPharChem Technologies &mdash; Security Architecture &mdash; Confidential</p>
        <p style="margin-top:4px;">This document contains proprietary security information. Unauthorized distribution is strictly prohibited.</p>
    </div>

</div><!-- end doc-content -->
</div><!-- end document -->

</body>
</html>
