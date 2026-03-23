<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EnPharChem - Installation Manual v1.0</title>
<style>
    /* ========== RESET & BASE ========== */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --blue: #0d6efd;
        --cyan: #0dcaf0;
        --dark: #1a1a2e;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-500: #6c757d;
        --gray-700: #495057;
        --gray-900: #212529;
        --white: #ffffff;
        --font-sans: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
        --font-mono: 'Cascadia Code', 'Fira Code', 'Consolas', 'Courier New', monospace;
    }

    /* ========== PAGE SETUP FOR PDF ========== */
    @page {
        size: A4;
        margin: 20mm 18mm 25mm 18mm;
    }

    @media print {
        .print-bar { display: none !important; }
        body { background: #fff !important; }
        .manual-container { box-shadow: none !important; max-width: 100% !important; padding: 0 !important; margin: 0 !important; }
        .cover-page { page-break-after: always; }
        .toc-section { page-break-after: always; }
        .manual-section { page-break-before: always; }
        .no-break { page-break-inside: avoid; }
        .page-footer { position: fixed; bottom: 0; left: 0; right: 0; }
        a { text-decoration: none !important; color: var(--gray-900) !important; }
    }

    html { print-color-adjust: exact; -webkit-print-color-adjust: exact; }

    body {
        font-family: var(--font-sans);
        font-size: 11pt;
        line-height: 1.65;
        color: var(--gray-900);
        background: var(--gray-200);
    }

    /* ========== PRINT BAR ========== */
    .print-bar {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: var(--dark);
        color: #fff;
        padding: 12px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 12px rgba(0,0,0,0.25);
    }
    .print-bar .bar-title {
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .print-bar .bar-actions { display: flex; gap: 10px; }
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
        color: #fff;
    }
    .print-bar .btn-back:hover { background: rgba(255,255,255,0.22); }
    .print-bar .btn-pdf {
        background: linear-gradient(135deg, var(--blue), var(--cyan));
        color: #fff;
    }
    .print-bar .btn-pdf:hover { opacity: 0.9; transform: translateY(-1px); }

    /* ========== CONTAINER ========== */
    .manual-container {
        max-width: 210mm;
        margin: 0 auto;
        background: var(--white);
        box-shadow: 0 0 40px rgba(0,0,0,0.1);
        padding: 0;
    }

    /* ========== COVER PAGE ========== */
    .cover-page {
        min-height: 297mm;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 60px 50px;
        position: relative;
        background: linear-gradient(180deg, #f8faff 0%, #ffffff 40%, #ffffff 70%, #f0f6ff 100%);
    }
    .cover-page::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--blue), var(--cyan));
    }
    .cover-logo {
        width: 100px;
        height: 100px;
        border-radius: 20px;
        background: linear-gradient(135deg, var(--blue), var(--cyan));
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 40px;
        box-shadow: 0 8px 30px rgba(13,110,253,0.3);
    }
    .cover-logo span {
        font-size: 42px;
        font-weight: 800;
        color: #fff;
        letter-spacing: -1px;
    }
    .cover-title {
        font-size: 38pt;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }
    .cover-subtitle {
        font-size: 22pt;
        font-weight: 300;
        color: var(--blue);
        margin-bottom: 50px;
    }
    .cover-meta {
        border-top: 2px solid var(--gray-300);
        padding-top: 30px;
        margin-top: 20px;
    }
    .cover-meta table {
        margin: 0 auto;
        border-collapse: collapse;
        font-size: 11pt;
    }
    .cover-meta td {
        padding: 6px 20px;
        text-align: left;
    }
    .cover-meta td:first-child {
        font-weight: 600;
        color: var(--gray-500);
        text-transform: uppercase;
        font-size: 9pt;
        letter-spacing: 0.8px;
    }
    .cover-meta td:last-child {
        color: var(--gray-900);
    }
    .cover-classification {
        margin-top: 50px;
        padding: 10px 30px;
        border: 2px solid var(--blue);
        border-radius: 6px;
        font-size: 10pt;
        font-weight: 600;
        color: var(--blue);
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    /* ========== TABLE OF CONTENTS ========== */
    .toc-section {
        padding: 50px;
        min-height: 297mm;
    }
    .toc-title {
        font-size: 22pt;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 30px;
        padding-bottom: 12px;
        border-bottom: 3px solid var(--blue);
    }
    .toc-list {
        list-style: none;
    }
    .toc-list li {
        padding: 8px 0;
        border-bottom: 1px dotted var(--gray-300);
        display: flex;
        justify-content: space-between;
        align-items: baseline;
    }
    .toc-list li a {
        text-decoration: none;
        color: var(--gray-900);
        font-weight: 500;
        transition: color 0.2s;
    }
    .toc-list li a:hover { color: var(--blue); }
    .toc-list li .toc-page {
        color: var(--gray-500);
        font-size: 10pt;
        min-width: 30px;
        text-align: right;
    }
    .toc-list .toc-main > a { font-weight: 700; font-size: 12pt; }
    .toc-list .toc-sub {
        padding-left: 24px;
        border-bottom-color: var(--gray-200);
    }
    .toc-list .toc-sub a { font-weight: 400; font-size: 10.5pt; color: var(--gray-700); }

    /* ========== SECTIONS ========== */
    .manual-section {
        padding: 50px;
    }
    .section-header {
        margin-bottom: 30px;
        padding-bottom: 12px;
        border-bottom: 3px solid var(--blue);
    }
    .section-number {
        font-size: 13pt;
        font-weight: 700;
        color: var(--cyan);
        text-transform: uppercase;
        letter-spacing: 1px;
        display: block;
        margin-bottom: 4px;
    }
    .section-title {
        font-size: 22pt;
        font-weight: 700;
        color: var(--dark);
    }
    .subsection-title {
        font-size: 15pt;
        font-weight: 700;
        color: var(--blue);
        margin-top: 30px;
        margin-bottom: 14px;
        padding-left: 14px;
        border-left: 4px solid var(--cyan);
    }
    .sub-subsection-title {
        font-size: 12pt;
        font-weight: 600;
        color: var(--gray-700);
        margin-top: 22px;
        margin-bottom: 10px;
    }
    p {
        margin-bottom: 12px;
        color: var(--gray-700);
    }
    ul, ol {
        margin-bottom: 14px;
        padding-left: 24px;
    }
    li { margin-bottom: 6px; color: var(--gray-700); }

    /* ========== TABLES ========== */
    .manual-table {
        width: 100%;
        border-collapse: collapse;
        margin: 16px 0 24px 0;
        font-size: 10pt;
    }
    .manual-table thead th {
        background: linear-gradient(135deg, var(--blue), #3d8bfd);
        color: #fff;
        padding: 10px 14px;
        text-align: left;
        font-weight: 600;
        font-size: 9.5pt;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid var(--blue);
    }
    .manual-table tbody td {
        padding: 9px 14px;
        border: 1px solid var(--gray-300);
        vertical-align: top;
    }
    .manual-table tbody tr:nth-child(even) {
        background: var(--gray-100);
    }
    .manual-table tbody tr:hover {
        background: #e8f0fe;
    }
    .manual-table code {
        background: var(--gray-200);
        padding: 1px 5px;
        border-radius: 3px;
        font-family: var(--font-mono);
        font-size: 9pt;
    }

    /* ========== CODE BLOCKS ========== */
    .code-block {
        background: #f6f8fa;
        border: 1px solid var(--gray-300);
        border-left: 4px solid var(--blue);
        border-radius: 4px;
        padding: 16px 20px;
        margin: 14px 0 20px 0;
        font-family: var(--font-mono);
        font-size: 9.5pt;
        line-height: 1.55;
        overflow-x: auto;
        white-space: pre;
        color: var(--gray-900);
    }
    .code-block .comment { color: #6a737d; }
    .code-block .keyword { color: #d73a49; }
    .code-block .string { color: #032f62; }
    .code-label {
        display: inline-block;
        background: var(--blue);
        color: #fff;
        font-size: 8.5pt;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 4px 4px 0 0;
        margin-bottom: -1px;
        position: relative;
        top: 1px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ========== CHECKLIST ========== */
    .checklist {
        list-style: none;
        padding-left: 0;
        margin: 16px 0;
    }
    .checklist li {
        padding: 10px 14px 10px 40px;
        position: relative;
        border: 1px solid var(--gray-200);
        border-radius: 6px;
        margin-bottom: 6px;
        background: var(--gray-100);
    }
    .checklist li::before {
        content: '';
        position: absolute;
        left: 14px;
        top: 12px;
        width: 16px;
        height: 16px;
        border: 2px solid var(--blue);
        border-radius: 3px;
    }
    .checklist li .item-num {
        font-weight: 700;
        color: var(--blue);
        margin-right: 6px;
    }

    /* ========== INFO BOXES ========== */
    .info-box {
        padding: 14px 18px;
        border-radius: 6px;
        margin: 16px 0;
        font-size: 10pt;
    }
    .info-box.note {
        background: #e7f1ff;
        border: 1px solid #b6d4fe;
        color: #084298;
    }
    .info-box.warning {
        background: #fff3cd;
        border: 1px solid #ffe69c;
        color: #664d03;
    }
    .info-box.danger {
        background: #f8d7da;
        border: 1px solid #f5c2c7;
        color: #842029;
    }
    .info-box.success {
        background: #d1e7dd;
        border: 1px solid #badbcc;
        color: #0f5132;
    }
    .info-box strong { display: block; margin-bottom: 4px; }

    /* ========== STEP INDICATOR ========== */
    .step-indicator {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--blue), var(--cyan));
        color: #fff;
        font-weight: 700;
        font-size: 14pt;
        margin-right: 10px;
        flex-shrink: 0;
        vertical-align: middle;
    }

    /* ========== PAGE FOOTER ========== */
    .page-footer-print {
        display: none;
    }
    @media print {
        .page-footer-print {
            display: block;
            position: fixed;
            bottom: 5mm;
            left: 18mm;
            right: 18mm;
            text-align: center;
            font-size: 8pt;
            color: var(--gray-500);
            border-top: 1px solid var(--gray-300);
            padding-top: 4px;
        }
    }
    .page-footer-screen {
        text-align: center;
        font-size: 9pt;
        color: var(--gray-500);
        padding: 20px 50px;
        border-top: 1px solid var(--gray-300);
        margin: 0 50px;
    }

    /* ========== LICENSE TIERS ========== */
    .tier-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin: 18px 0;
    }
    .tier-card {
        border: 1px solid var(--gray-300);
        border-radius: 8px;
        padding: 16px;
        text-align: center;
    }
    .tier-card.highlight {
        border-color: var(--blue);
        background: #f0f6ff;
    }
    .tier-card h4 {
        font-size: 11pt;
        margin-bottom: 6px;
        color: var(--blue);
    }
    .tier-card p {
        font-size: 9pt;
        margin-bottom: 0;
    }
</style>
</head>
<body>

<!-- ==================== PRINT BAR ==================== -->
<div class="print-bar">
    <div class="bar-title">EnPharChem - Installation Manual v1.0</div>
    <div class="bar-actions">
        <a href="javascript:history.back()" class="btn btn-back">&#8592; Back</a>
        <button class="btn btn-pdf" onclick="window.print()">&#128438; Download PDF</button>
    </div>
</div>

<div class="manual-container">

<!-- ==================== COVER PAGE ==================== -->
<div class="cover-page">
    <div class="cover-logo"><span>EP</span></div>
    <div class="cover-title">EnPharChem</div>
    <div class="cover-subtitle">Installation Manual</div>

    <div class="cover-meta">
        <table>
            <tr><td>Document Title</td><td>EnPharChem Platform Installation Manual</td></tr>
            <tr><td>Version</td><td>1.0</td></tr>
            <tr><td>Release Date</td><td>March 2026</td></tr>
            <tr><td>Classification</td><td>Technical Documentation</td></tr>
            <tr><td>Prepared By</td><td>EnPharChem Technologies</td></tr>
            <tr><td>Document ID</td><td>EP-TEC-INST-2026-001</td></tr>
            <tr><td>Confidentiality</td><td>Internal &amp; Licensed Partners</td></tr>
        </table>
    </div>

    <div class="cover-classification">Technical Documentation - Confidential</div>
</div>

<!-- ==================== TABLE OF CONTENTS ==================== -->
<div class="toc-section">
    <div class="toc-title">Table of Contents</div>
    <ul class="toc-list">
        <li class="toc-main"><a href="#section-1">1. Introduction</a></li>
        <li class="toc-sub"><a href="#section-1-1">1.1 Purpose of This Document</a></li>
        <li class="toc-sub"><a href="#section-1-2">1.2 Intended Audience</a></li>
        <li class="toc-sub"><a href="#section-1-3">1.3 Document Conventions</a></li>

        <li class="toc-main"><a href="#section-2">2. System Requirements</a></li>
        <li class="toc-sub"><a href="#section-2-1">2.1 Hardware Requirements</a></li>
        <li class="toc-sub"><a href="#section-2-2">2.2 Software Requirements</a></li>
        <li class="toc-sub"><a href="#section-2-3">2.3 Browser Support</a></li>
        <li class="toc-sub"><a href="#section-2-4">2.4 Network Requirements</a></li>

        <li class="toc-main"><a href="#section-3">3. Pre-Installation Checklist</a></li>

        <li class="toc-main"><a href="#section-4">4. Installation Steps</a></li>
        <li class="toc-sub"><a href="#section-4-1">4.1 Download and Install XAMPP</a></li>
        <li class="toc-sub"><a href="#section-4-2">4.2 Configure MySQL</a></li>
        <li class="toc-sub"><a href="#section-4-3">4.3 Configure Apache</a></li>
        <li class="toc-sub"><a href="#section-4-4">4.4 Deploy EnPharChem Files</a></li>
        <li class="toc-sub"><a href="#section-4-5">4.5 Configure Database Connection</a></li>
        <li class="toc-sub"><a href="#section-4-6">4.6 Run Database Installation</a></li>
        <li class="toc-sub"><a href="#section-4-7">4.7 Run Control Panel Migration</a></li>
        <li class="toc-sub"><a href="#section-4-8">4.8 Create Super User Account</a></li>
        <li class="toc-sub"><a href="#section-4-9">4.9 Verify Installation</a></li>

        <li class="toc-main"><a href="#section-5">5. Post-Installation Configuration</a></li>
        <li class="toc-sub"><a href="#section-5-1">5.1 Change Default Passwords</a></li>
        <li class="toc-sub"><a href="#section-5-2">5.2 Configure Email Settings</a></li>
        <li class="toc-sub"><a href="#section-5-3">5.3 Set Up SSL/HTTPS</a></li>
        <li class="toc-sub"><a href="#section-5-4">5.4 Configure Backup Schedule</a></li>
        <li class="toc-sub"><a href="#section-5-5">5.5 Enable Audit Logging</a></li>

        <li class="toc-main"><a href="#section-6">6. Module Activation</a></li>
        <li class="toc-sub"><a href="#section-6-1">6.1 Module Categories Overview</a></li>
        <li class="toc-sub"><a href="#section-6-2">6.2 License Tiers</a></li>

        <li class="toc-main"><a href="#section-7">7. Troubleshooting</a></li>

        <li class="toc-main"><a href="#section-8">8. Support &amp; Contact</a></li>
    </ul>
</div>

<!-- ==================== SECTION 1: INTRODUCTION ==================== -->
<div class="manual-section" id="section-1">
    <div class="section-header">
        <span class="section-number">Section 1</span>
        <div class="section-title">Introduction</div>
    </div>

    <h3 class="subsection-title" id="section-1-1">1.1 Purpose of This Document</h3>
    <p>
        This Installation Manual provides detailed, step-by-step instructions for deploying the EnPharChem
        Enterprise Resource Planning (ERP) platform. It covers all aspects of the installation process, from
        verifying system prerequisites through deploying application files, configuring database connections,
        and performing post-installation hardening.
    </p>
    <p>
        The procedures described herein have been tested and validated against the supported environments
        listed in Section 2. Following this guide in its entirety will result in a fully functional EnPharChem
        installation ready for user onboarding and module activation.
    </p>

    <h3 class="subsection-title" id="section-1-2">1.2 Intended Audience</h3>
    <p>This document is intended for:</p>
    <ul>
        <li><strong>IT Administrators</strong> responsible for deploying and maintaining the EnPharChem platform within their organization's infrastructure.</li>
        <li><strong>System Engineers</strong> tasked with configuring the server environment, database, and network settings.</li>
        <li><strong>DevOps Personnel</strong> managing automated deployments, CI/CD pipelines, and environment provisioning.</li>
        <li><strong>Technical Consultants</strong> engaged by EnPharChem Technologies to assist with on-site or remote installations.</li>
    </ul>
    <p>Readers should possess a working knowledge of web server administration, database management, and basic command-line operations on their target operating system.</p>

    <h3 class="subsection-title" id="section-1-3">1.3 Document Conventions</h3>
    <p>The following conventions are used throughout this manual:</p>
    <table class="manual-table">
        <thead>
            <tr><th>Convention</th><th>Meaning</th></tr>
        </thead>
        <tbody>
            <tr><td><code>monospace text</code></td><td>Commands, file paths, configuration values, or code snippets</td></tr>
            <tr><td><strong>Bold text</strong></td><td>UI elements, button labels, or key terms on first use</td></tr>
            <tr><td style="color: var(--blue);">Blue note box</td><td>Helpful tips and additional information</td></tr>
            <tr><td style="color: #664d03;">Yellow warning box</td><td>Important precautions or caveats</td></tr>
            <tr><td style="color: #842029;">Red danger box</td><td>Critical warnings that may result in data loss or system failure</td></tr>
            <tr><td><em>Italic text</em></td><td>Variable values you must replace with your own</td></tr>
        </tbody>
    </table>
</div>

<!-- ==================== SECTION 2: SYSTEM REQUIREMENTS ==================== -->
<div class="manual-section" id="section-2">
    <div class="section-header">
        <span class="section-number">Section 2</span>
        <div class="section-title">System Requirements</div>
    </div>

    <h3 class="subsection-title" id="section-2-1">2.1 Hardware Requirements</h3>
    <p>The following table outlines the hardware specifications for each deployment tier. Select the tier that matches your organization's expected user load and data volume.</p>

    <table class="manual-table no-break">
        <thead>
            <tr><th>Component</th><th>Minimum</th><th>Recommended</th><th>Enterprise</th></tr>
        </thead>
        <tbody>
            <tr><td><strong>CPU Cores</strong></td><td>4 cores</td><td>8 cores</td><td>16+ cores</td></tr>
            <tr><td><strong>RAM</strong></td><td>8 GB</td><td>32 GB</td><td>64 GB+</td></tr>
            <tr><td><strong>Storage</strong></td><td>100 GB SSD</td><td>500 GB SSD</td><td>1 TB+ NVMe</td></tr>
            <tr><td><strong>Network</strong></td><td>100 Mbps</td><td>1 Gbps</td><td>10 Gbps</td></tr>
            <tr><td><strong>Users Supported</strong></td><td>1 &ndash; 25</td><td>25 &ndash; 200</td><td>200 &ndash; 5,000+</td></tr>
            <tr><td><strong>Use Case</strong></td><td>Development / Testing</td><td>Small &ndash; Medium Business</td><td>Large Enterprise / Multi-site</td></tr>
        </tbody>
    </table>

    <div class="info-box note">
        <strong>Note:</strong> For Enterprise deployments, consider separating the database server and application server onto dedicated machines for optimal performance. Database-intensive workloads benefit significantly from NVMe storage and higher RAM allocations for InnoDB buffer pool.
    </div>

    <h3 class="subsection-title" id="section-2-2">2.2 Software Requirements</h3>
    <table class="manual-table no-break">
        <thead>
            <tr><th>Software</th><th>Minimum Version</th><th>Recommended Version</th><th>Notes</th></tr>
        </thead>
        <tbody>
            <tr><td><strong>Operating System</strong></td><td>Windows 10 / Ubuntu 20.04</td><td>Windows 11 / Ubuntu 22.04+</td><td>64-bit required</td></tr>
            <tr><td><strong>XAMPP</strong></td><td>8.2.0</td><td>8.2.12+</td><td>Includes Apache, PHP, MariaDB</td></tr>
            <tr><td><strong>PHP</strong></td><td>8.1.0</td><td>8.2+</td><td>Extensions: pdo_mysql, mbstring, openssl, curl, gd, zip</td></tr>
            <tr><td><strong>MySQL / MariaDB</strong></td><td>MySQL 8.0 / MariaDB 10.6</td><td>MySQL 8.0.34+ / MariaDB 10.11+</td><td>InnoDB engine required</td></tr>
            <tr><td><strong>Apache</strong></td><td>2.4.x</td><td>2.4.58+</td><td>mod_rewrite, mod_ssl required</td></tr>
            <tr><td><strong>Composer</strong></td><td>2.5</td><td>2.7+</td><td>For dependency management (if applicable)</td></tr>
        </tbody>
    </table>

    <h3 class="subsection-title" id="section-2-3">2.3 Browser Support</h3>
    <p>EnPharChem's web interface is optimized for modern browsers. The following versions are officially supported:</p>
    <table class="manual-table no-break">
        <thead>
            <tr><th>Browser</th><th>Minimum Version</th><th>Status</th></tr>
        </thead>
        <tbody>
            <tr><td>Google Chrome</td><td>100+</td><td>Fully supported (primary)</td></tr>
            <tr><td>Mozilla Firefox</td><td>100+</td><td>Fully supported</td></tr>
            <tr><td>Microsoft Edge</td><td>100+</td><td>Fully supported</td></tr>
            <tr><td>Apple Safari</td><td>16+</td><td>Supported</td></tr>
            <tr><td>Internet Explorer</td><td>&mdash;</td><td>Not supported</td></tr>
        </tbody>
    </table>

    <h3 class="subsection-title" id="section-2-4">2.4 Network Requirements</h3>
    <p>Ensure the following network conditions are met before proceeding with installation:</p>
    <table class="manual-table no-break">
        <thead>
            <tr><th>Requirement</th><th>Details</th></tr>
        </thead>
        <tbody>
            <tr><td><strong>Protocol</strong></td><td>HTTPS (TLS 1.2 or higher) required for production deployments</td></tr>
            <tr><td><strong>Port 80 (HTTP)</strong></td><td>Required for initial setup; redirect to HTTPS in production</td></tr>
            <tr><td><strong>Port 443 (HTTPS)</strong></td><td>Required for secure web access</td></tr>
            <tr><td><strong>Port 3306 / 3311</strong></td><td>MySQL/MariaDB database connections (3311 is the EnPharChem default)</td></tr>
            <tr><td><strong>Firewall</strong></td><td>Allow inbound traffic on ports 80, 443; restrict port 3311 to localhost or application server only</td></tr>
            <tr><td><strong>DNS</strong></td><td>A valid domain name or static IP address for production deployments</td></tr>
        </tbody>
    </table>

    <div class="info-box warning">
        <strong>Warning:</strong> Never expose the MySQL/MariaDB port (3306 or 3311) to the public internet. Database connections should be restricted to localhost or the application server's private IP address via firewall rules.
    </div>
</div>

<!-- ==================== SECTION 3: PRE-INSTALLATION CHECKLIST ==================== -->
<div class="manual-section" id="section-3">
    <div class="section-header">
        <span class="section-number">Section 3</span>
        <div class="section-title">Pre-Installation Checklist</div>
    </div>

    <p>Before beginning the installation, verify that all of the following conditions are met. Completing this checklist will prevent common installation issues and ensure a smooth deployment.</p>

    <ol class="checklist">
        <li><span class="item-num">1.</span> Server hardware meets or exceeds the <strong>minimum</strong> requirements listed in Section 2.1.</li>
        <li><span class="item-num">2.</span> Operating system is fully updated with the latest security patches and service packs.</li>
        <li><span class="item-num">3.</span> Administrator or root-level access is available on the target server.</li>
        <li><span class="item-num">4.</span> Required network ports (80, 443, 3311) are open and not blocked by firewalls or security software.</li>
        <li><span class="item-num">5.</span> A valid EnPharChem license key has been obtained from EnPharChem Technologies (required for module activation).</li>
        <li><span class="item-num">6.</span> No conflicting web server software (IIS, nginx) is running on ports 80 or 443.</li>
        <li><span class="item-num">7.</span> Antivirus or endpoint protection software is configured to exclude the <code>C:\xampp</code> directory to prevent file-locking conflicts.</li>
        <li><span class="item-num">8.</span> A backup of any existing data or configuration has been created if upgrading from a previous version.</li>
        <li><span class="item-num">9.</span> The EnPharChem installation package has been downloaded and its SHA-256 checksum verified against the value provided in the release notes.</li>
        <li><span class="item-num">10.</span> A dedicated database user and schema name have been planned (avoid using the MySQL <code>root</code> account in production).</li>
    </ol>

    <div class="info-box note">
        <strong>Tip:</strong> Print this checklist and sign off each item as it is verified. Attach the signed checklist to your deployment documentation for compliance and audit purposes.
    </div>
</div>

<!-- ==================== SECTION 4: INSTALLATION STEPS ==================== -->
<div class="manual-section" id="section-4">
    <div class="section-header">
        <span class="section-number">Section 4</span>
        <div class="section-title">Installation Steps</div>
    </div>

    <p>Follow each step in order. Do not skip steps unless explicitly instructed. Each step includes verification instructions to confirm successful completion before proceeding.</p>

    <!-- Step 4.1 -->
    <h3 class="subsection-title" id="section-4-1"><span class="step-indicator">1</span> 4.1 Download and Install XAMPP</h3>
    <p>XAMPP provides the complete server stack (Apache, MySQL/MariaDB, PHP) required by EnPharChem. Download the latest XAMPP 8.2+ installer from the official Apache Friends website.</p>

    <h4 class="sub-subsection-title">Windows Installation</h4>
    <span class="code-label">Command Prompt (Administrator)</span>
    <div class="code-block"><span class="comment"># Download XAMPP 8.2.12 installer (or latest available)</span>
<span class="comment"># Run the installer and select the following components:</span>
<span class="comment">#   - Apache</span>
<span class="comment">#   - MySQL (MariaDB)</span>
<span class="comment">#   - PHP</span>
<span class="comment">#   - phpMyAdmin</span>

<span class="comment"># Install to the default directory:</span>
C:\xampp

<span class="comment"># Verify installation:</span>
C:\xampp\php\php.exe -v
<span class="comment"># Expected output: PHP 8.2.x (cli)</span></div>

    <h4 class="sub-subsection-title">Linux Installation</h4>
    <span class="code-label">Terminal</span>
    <div class="code-block"><span class="comment"># Download and set permissions</span>
chmod 755 xampp-linux-x64-8.2.12-0-installer.run
sudo ./xampp-linux-x64-8.2.12-0-installer.run

<span class="comment"># Default install path: /opt/lampp</span>
<span class="comment"># Verify installation:</span>
/opt/lampp/bin/php -v</div>

    <div class="info-box note">
        <strong>Note:</strong> On Windows, ensure you run the installer as Administrator. If Windows Defender SmartScreen blocks the installer, click "More info" and then "Run anyway."
    </div>

    <!-- Step 4.2 -->
    <h3 class="subsection-title" id="section-4-2"><span class="step-indicator">2</span> 4.2 Configure MySQL</h3>
    <p>EnPharChem uses port <strong>3311</strong> by default to avoid conflicts with other MySQL instances. You must update the MySQL configuration file before starting the service.</p>

    <span class="code-label">C:\xampp\mysql\bin\my.ini</span>
    <div class="code-block"><span class="comment"># Locate the [mysqld] section and update the following:</span>

[mysqld]
port=3311
innodb_buffer_pool_size=512M
innodb_log_file_size=128M
innodb_file_per_table=1
max_connections=200
max_allowed_packet=64M
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci
sql_mode=STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION

<span class="comment"># Locate the [client] section and update:</span>
[client]
port=3311
default-character-set=utf8mb4</div>

    <p>After modifying <code>my.ini</code>, start MySQL from the XAMPP Control Panel or via the command line:</p>

    <span class="code-label">Command</span>
    <div class="code-block"><span class="comment"># Start MySQL service</span>
C:\xampp\mysql\bin\mysqld.exe --defaults-file="C:\xampp\mysql\bin\my.ini"

<span class="comment"># Verify MySQL is running on port 3311:</span>
C:\xampp\mysql\bin\mysql.exe -u root -P 3311 -e "SELECT VERSION();"</div>

    <div class="info-box warning">
        <strong>Important:</strong> If you are running another MySQL instance on the default port 3306, ensure there are no port conflicts. EnPharChem specifically requires port 3311 to be configured in both MySQL and the application database configuration.
    </div>

    <!-- Step 4.3 -->
    <h3 class="subsection-title" id="section-4-3"><span class="step-indicator">3</span> 4.3 Configure Apache</h3>
    <p>Enable the required Apache modules and configure a virtual host for EnPharChem.</p>

    <h4 class="sub-subsection-title">Enable mod_rewrite</h4>
    <span class="code-label">C:\xampp\apache\conf\httpd.conf</span>
    <div class="code-block"><span class="comment"># Uncomment the following line (remove the leading #):</span>
LoadModule rewrite_module modules/mod_rewrite.so

<span class="comment"># Also ensure mod_ssl is enabled for HTTPS:</span>
LoadModule ssl_module modules/mod_ssl.so

<span class="comment"># Ensure AllowOverride is set to All for the htdocs directory:</span>
&lt;Directory "C:/xampp/htdocs"&gt;
    Options Indexes FollowSymLinks Includes ExecCGI
    AllowOverride All
    Require all granted
&lt;/Directory&gt;</div>

    <h4 class="sub-subsection-title">Virtual Host Configuration (Optional)</h4>
    <span class="code-label">C:\xampp\apache\conf\extra\httpd-vhosts.conf</span>
    <div class="code-block">&lt;VirtualHost *:80&gt;
    ServerName enpharchem.local
    ServerAlias www.enpharchem.local
    DocumentRoot "C:/xampp/htdocs/enpharchem"

    &lt;Directory "C:/xampp/htdocs/enpharchem"&gt;
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    &lt;/Directory&gt;

    ErrorLog "logs/enpharchem-error.log"
    CustomLog "logs/enpharchem-access.log" combined
&lt;/VirtualHost&gt;</div>

    <p>Restart Apache after making configuration changes:</p>
    <span class="code-label">Command</span>
    <div class="code-block"><span class="comment"># Restart Apache via XAMPP Control Panel, or:</span>
C:\xampp\apache\bin\httpd.exe -k restart

<span class="comment"># Verify Apache is running:</span>
curl -I http://localhost
<span class="comment"># Expected: HTTP/1.1 200 OK</span></div>

    <!-- Step 4.4 -->
    <h3 class="subsection-title" id="section-4-4"><span class="step-indicator">4</span> 4.4 Deploy EnPharChem Files</h3>
    <p>Copy the EnPharChem application files to the Apache document root.</p>

    <span class="code-label">Windows</span>
    <div class="code-block"><span class="comment"># Extract the EnPharChem package to the web root:</span>
xcopy /E /I /Y "C:\Downloads\enpharchem-v1.0" "C:\xampp\htdocs\enpharchem"

<span class="comment"># Verify the directory structure:</span>
dir C:\xampp\htdocs\enpharchem

<span class="comment"># Expected structure:</span>
<span class="comment">#   enpharchem/</span>
<span class="comment">#   +-- config/</span>
<span class="comment">#   +-- controllers/</span>
<span class="comment">#   +-- models/</span>
<span class="comment">#   +-- views/</span>
<span class="comment">#   +-- assets/</span>
<span class="comment">#   +-- includes/</span>
<span class="comment">#   +-- index.php</span>
<span class="comment">#   +-- install.php</span>
<span class="comment">#   +-- .htaccess</span></div>

    <span class="code-label">Linux</span>
    <div class="code-block"><span class="comment"># Extract and copy files</span>
sudo cp -r /tmp/enpharchem-v1.0 /opt/lampp/htdocs/enpharchem

<span class="comment"># Set proper ownership and permissions</span>
sudo chown -R daemon:daemon /opt/lampp/htdocs/enpharchem
sudo find /opt/lampp/htdocs/enpharchem -type d -exec chmod 755 {} \;
sudo find /opt/lampp/htdocs/enpharchem -type f -exec chmod 644 {} \;</div>

    <!-- Step 4.5 -->
    <h3 class="subsection-title" id="section-4-5"><span class="step-indicator">5</span> 4.5 Configure Database Connection</h3>
    <p>Update the database configuration file with your MySQL connection details.</p>

    <span class="code-label">C:\xampp\htdocs\enpharchem\config\database.php</span>
    <div class="code-block">&lt;?php
<span class="comment">// EnPharChem Database Configuration</span>

return [
    <span class="string">'host'</span>     =&gt; <span class="string">'localhost'</span>,
    <span class="string">'port'</span>     =&gt; <span class="string">'3311'</span>,
    <span class="string">'database'</span> =&gt; <span class="string">'enpharchem_db'</span>,
    <span class="string">'username'</span> =&gt; <span class="string">'enpharchem_user'</span>,
    <span class="string">'password'</span> =&gt; <span class="string">'your_secure_password_here'</span>,
    <span class="string">'charset'</span>  =&gt; <span class="string">'utf8mb4'</span>,
    <span class="string">'options'</span>  =&gt; [
        PDO::ATTR_ERRMODE            =&gt; PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE =&gt; PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   =&gt; <span class="keyword">false</span>,
    ],
];</div>

    <p>Create the database and user in MySQL before proceeding:</p>
    <span class="code-label">MySQL Console</span>
    <div class="code-block"><span class="comment">-- Connect to MySQL on port 3311</span>
mysql -u root -P 3311

<span class="comment">-- Create the database</span>
CREATE DATABASE enpharchem_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

<span class="comment">-- Create a dedicated user (replace password)</span>
CREATE USER 'enpharchem_user'@'localhost' IDENTIFIED BY 'your_secure_password_here';

<span class="comment">-- Grant privileges</span>
GRANT ALL PRIVILEGES ON enpharchem_db.* TO 'enpharchem_user'@'localhost';
FLUSH PRIVILEGES;</div>

    <div class="info-box danger">
        <strong>Security Warning:</strong> Replace <code>your_secure_password_here</code> with a strong, unique password. Never use the MySQL root account for the application in production environments. Ensure the password meets your organization's complexity requirements (minimum 12 characters, mixed case, numbers, and symbols).
    </div>

    <!-- Step 4.6 -->
    <h3 class="subsection-title" id="section-4-6"><span class="step-indicator">6</span> 4.6 Run Database Installation</h3>
    <p>The database installation script creates all required tables, indexes, default data, and module registrations. Open your web browser and navigate to:</p>

    <span class="code-label">URL</span>
    <div class="code-block">http://localhost/enpharchem/install.php</div>

    <p>The installation script performs the following operations:</p>
    <ul>
        <li>Creates <strong>30+ database tables</strong> including users, roles, permissions, modules, categories, audit logs, settings, and module-specific data tables.</li>
        <li>Registers <strong>15 module categories</strong> with their associated metadata and display configurations.</li>
        <li>Installs <strong>115+ individual modules</strong> across all categories with default activation states and license requirements.</li>
        <li>Seeds default configuration values, system settings, and initial role definitions.</li>
        <li>Creates required database indexes for optimal query performance.</li>
        <li>Sets up foreign key constraints to maintain referential integrity.</li>
    </ul>

    <div class="info-box success">
        <strong>Success Indicator:</strong> Upon completion, the script displays a summary showing the number of tables created, modules registered, and categories configured. You should see "Installation completed successfully" with zero errors.
    </div>

    <div class="info-box warning">
        <strong>Warning:</strong> Do not refresh or close the browser during installation. The process may take 30&ndash;90 seconds depending on server performance. If the installation fails partway through, drop the database, recreate it, and run the script again.
    </div>

    <!-- Step 4.7 -->
    <h3 class="subsection-title" id="section-4-7"><span class="step-indicator">7</span> 4.7 Run Control Panel Migration</h3>
    <p>After the base installation, run the Control Panel migration to create additional management tables:</p>

    <span class="code-label">URL</span>
    <div class="code-block">http://localhost/enpharchem/migrate_control_panel.php</div>

    <p>This migration creates <strong>6 additional tables</strong>:</p>
    <table class="manual-table no-break">
        <thead>
            <tr><th>Table Name</th><th>Purpose</th></tr>
        </thead>
        <tbody>
            <tr><td><code>control_panel_settings</code></td><td>Global system configuration and feature toggles</td></tr>
            <tr><td><code>control_panel_widgets</code></td><td>Dashboard widget definitions and layout configurations</td></tr>
            <tr><td><code>control_panel_logs</code></td><td>Administrative action audit trail</td></tr>
            <tr><td><code>control_panel_notifications</code></td><td>System notification queue and delivery tracking</td></tr>
            <tr><td><code>control_panel_scheduled_tasks</code></td><td>Cron job definitions and execution history</td></tr>
            <tr><td><code>control_panel_user_preferences</code></td><td>Per-user UI preferences and dashboard customizations</td></tr>
        </tbody>
    </table>

    <!-- Step 4.8 -->
    <h3 class="subsection-title" id="section-4-8"><span class="step-indicator">8</span> 4.8 Create Super User Account</h3>
    <p>Create the initial super administrator account that has full access to all system functions:</p>

    <span class="code-label">URL</span>
    <div class="code-block">http://localhost/enpharchem/setup_superuser.php</div>

    <p>Complete the form with the following information:</p>
    <ul>
        <li><strong>Username:</strong> Your chosen administrator username</li>
        <li><strong>Email:</strong> A valid email address for password recovery</li>
        <li><strong>Password:</strong> A strong password (minimum 12 characters)</li>
        <li><strong>Full Name:</strong> The administrator's full name</li>
        <li><strong>Role:</strong> Super Administrator (automatically assigned)</li>
    </ul>

    <div class="info-box danger">
        <strong>Critical:</strong> Store the super user credentials securely. This account has unrestricted access to all modules, user management, system configuration, and data. Enable two-factor authentication immediately after first login.
    </div>

    <!-- Step 4.9 -->
    <h3 class="subsection-title" id="section-4-9"><span class="step-indicator">9</span> 4.9 Verify Installation</h3>
    <p>Verify that all components are working correctly by visiting the following URLs and confirming the expected behavior:</p>

    <table class="manual-table no-break">
        <thead>
            <tr><th>URL</th><th>Expected Result</th></tr>
        </thead>
        <tbody>
            <tr><td><code>http://localhost/enpharchem/</code></td><td>Login page loads without errors</td></tr>
            <tr><td><code>http://localhost/enpharchem/dashboard</code></td><td>Dashboard renders after login (redirects to login if not authenticated)</td></tr>
            <tr><td><code>http://localhost/enpharchem/control-panel</code></td><td>Control panel loads with system status indicators</td></tr>
            <tr><td><code>http://localhost/enpharchem/modules</code></td><td>Module listing shows all 15 categories with module counts</td></tr>
            <tr><td><code>http://localhost/enpharchem/settings</code></td><td>System settings page accessible to super admin</td></tr>
            <tr><td><code>http://localhost/enpharchem/users</code></td><td>User management page shows the super admin account</td></tr>
        </tbody>
    </table>

    <div class="info-box success">
        <strong>Congratulations:</strong> If all URLs respond correctly, the EnPharChem installation is complete. Proceed to Section 5 for post-installation hardening, or Section 6 to activate modules for your licensed tier.
    </div>
</div>

<!-- ==================== SECTION 5: POST-INSTALLATION CONFIGURATION ==================== -->
<div class="manual-section" id="section-5">
    <div class="section-header">
        <span class="section-number">Section 5</span>
        <div class="section-title">Post-Installation Configuration</div>
    </div>

    <p>After completing the base installation, perform the following configuration steps to harden the system, enable essential services, and prepare for production use.</p>

    <!-- 5.1 -->
    <h3 class="subsection-title" id="section-5-1">5.1 Change Default Passwords</h3>
    <p>Immediately change all default passwords to prevent unauthorized access:</p>
    <ul>
        <li><strong>MySQL root password:</strong> Set a strong password if it was left blank during XAMPP installation.</li>
        <li><strong>phpMyAdmin access:</strong> Update the <code>config.inc.php</code> authentication settings.</li>
        <li><strong>Application admin password:</strong> Change the initial super user password from the user profile page.</li>
    </ul>

    <span class="code-label">MySQL Console</span>
    <div class="code-block"><span class="comment">-- Set MySQL root password</span>
ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_strong_root_password';
FLUSH PRIVILEGES;

<span class="comment">-- Verify the new password works</span>
mysql -u root -p -P 3311 -e "SELECT 'Connection successful';"</div>

    <!-- 5.2 -->
    <h3 class="subsection-title" id="section-5-2">5.2 Configure Email Settings</h3>
    <p>EnPharChem uses email for notifications, password resets, and audit alerts. Configure the SMTP settings in the application configuration:</p>

    <span class="code-label">C:\xampp\htdocs\enpharchem\config\mail.php</span>
    <div class="code-block">&lt;?php
return [
    <span class="string">'driver'</span>     =&gt; <span class="string">'smtp'</span>,
    <span class="string">'host'</span>       =&gt; <span class="string">'smtp.your-domain.com'</span>,
    <span class="string">'port'</span>       =&gt; 587,
    <span class="string">'encryption'</span> =&gt; <span class="string">'tls'</span>,
    <span class="string">'username'</span>   =&gt; <span class="string">'noreply@your-domain.com'</span>,
    <span class="string">'password'</span>   =&gt; <span class="string">'your_smtp_password'</span>,
    <span class="string">'from'</span>       =&gt; [
        <span class="string">'address'</span> =&gt; <span class="string">'noreply@your-domain.com'</span>,
        <span class="string">'name'</span>    =&gt; <span class="string">'EnPharChem System'</span>,
    ],
];</div>

    <!-- 5.3 -->
    <h3 class="subsection-title" id="section-5-3">5.3 Set Up SSL/HTTPS</h3>
    <p>For production deployments, SSL/HTTPS is mandatory. Configure Apache with a valid SSL certificate:</p>

    <span class="code-label">C:\xampp\apache\conf\extra\httpd-ssl.conf</span>
    <div class="code-block">&lt;VirtualHost *:443&gt;
    ServerName enpharchem.your-domain.com
    DocumentRoot "C:/xampp/htdocs/enpharchem"

    SSLEngine on
    SSLCertificateFile "C:/xampp/apache/conf/ssl/server.crt"
    SSLCertificateKeyFile "C:/xampp/apache/conf/ssl/server.key"
    SSLCertificateChainFile "C:/xampp/apache/conf/ssl/ca-bundle.crt"

    <span class="comment"># Modern TLS configuration</span>
    SSLProtocol all -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite HIGH:!aNULL:!MD5:!3DES
    SSLHonorCipherOrder on

    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
&lt;/VirtualHost&gt;</div>

    <div class="info-box note">
        <strong>Tip:</strong> Use <strong>Let's Encrypt</strong> (via Certbot) for free, automated SSL certificates. For internal deployments, your organization's internal Certificate Authority (CA) certificates are also acceptable.
    </div>

    <!-- 5.4 -->
    <h3 class="subsection-title" id="section-5-4">5.4 Configure Backup Schedule</h3>
    <p>Implement automated database and file backups to protect against data loss:</p>

    <span class="code-label">backup_enpharchem.bat (Windows Task Scheduler)</span>
    <div class="code-block">@echo off
set TIMESTAMP=%date:~-4%%date:~4,2%%date:~7,2%_%time:~0,2%%time:~3,2%
set BACKUP_DIR=C:\backups\enpharchem

<span class="comment">REM Database backup</span>
C:\xampp\mysql\bin\mysqldump.exe -u enpharchem_user -p"password" ^
    -P 3311 --single-transaction --routines --triggers ^
    enpharchem_db &gt; "%BACKUP_DIR%\db_%TIMESTAMP%.sql"

<span class="comment">REM Application files backup</span>
powershell Compress-Archive -Path "C:\xampp\htdocs\enpharchem" ^
    -DestinationPath "%BACKUP_DIR%\files_%TIMESTAMP%.zip"

<span class="comment">REM Retain only the last 30 days of backups</span>
forfiles /P "%BACKUP_DIR%" /D -30 /C "cmd /c del @file"</div>

    <div class="info-box warning">
        <strong>Recommendation:</strong> Schedule this backup script to run daily during off-peak hours (e.g., 02:00 AM). Store backups on a separate physical drive or offsite storage. Test backup restoration quarterly to ensure recoverability.
    </div>

    <!-- 5.5 -->
    <h3 class="subsection-title" id="section-5-5">5.5 Enable Audit Logging</h3>
    <p>EnPharChem includes built-in audit logging for compliance and security monitoring. Enable comprehensive audit logging by navigating to:</p>

    <span class="code-label">Settings Path</span>
    <div class="code-block">Control Panel &gt; System Settings &gt; Audit &amp; Compliance &gt; Enable Audit Logging</div>

    <p>Configure the following audit log settings:</p>
    <ul>
        <li><strong>Log Level:</strong> Set to "Detailed" for production environments.</li>
        <li><strong>Log Retention:</strong> Minimum 90 days (365 days recommended for regulated industries).</li>
        <li><strong>Events to Log:</strong> User logins/logouts, data modifications, permission changes, module access, failed authentication attempts, configuration changes.</li>
        <li><strong>Export Format:</strong> CSV or JSON for integration with SIEM tools.</li>
        <li><strong>Alert Threshold:</strong> Enable email alerts for 5+ failed login attempts within 15 minutes.</li>
    </ul>
</div>

<!-- ==================== SECTION 6: MODULE ACTIVATION ==================== -->
<div class="manual-section" id="section-6">
    <div class="section-header">
        <span class="section-number">Section 6</span>
        <div class="section-title">Module Activation</div>
    </div>

    <h3 class="subsection-title" id="section-6-1">6.1 Module Categories Overview</h3>
    <p>EnPharChem ships with 15 module categories encompassing 115+ individual modules. The following table lists each category and the number of modules available:</p>

    <table class="manual-table no-break">
        <thead>
            <tr><th>#</th><th>Category</th><th>Modules</th><th>Description</th></tr>
        </thead>
        <tbody>
            <tr><td>1</td><td><strong>Accounting &amp; Finance</strong></td><td>12</td><td>General ledger, accounts payable/receivable, billing, financial reporting</td></tr>
            <tr><td>2</td><td><strong>Human Resources</strong></td><td>10</td><td>Employee management, payroll, attendance, recruitment, benefits</td></tr>
            <tr><td>3</td><td><strong>Inventory Management</strong></td><td>9</td><td>Stock tracking, warehouse management, reorder alerts, batch/lot tracking</td></tr>
            <tr><td>4</td><td><strong>Sales &amp; CRM</strong></td><td>11</td><td>Lead management, opportunity tracking, quotations, customer portal</td></tr>
            <tr><td>5</td><td><strong>Procurement</strong></td><td>8</td><td>Purchase orders, vendor management, RFQ, approval workflows</td></tr>
            <tr><td>6</td><td><strong>Manufacturing</strong></td><td>7</td><td>Production planning, BOM, work orders, quality control, batch records</td></tr>
            <tr><td>7</td><td><strong>Quality Assurance</strong></td><td>6</td><td>QC testing, CAPA, deviation management, stability studies</td></tr>
            <tr><td>8</td><td><strong>Regulatory Compliance</strong></td><td>5</td><td>Document control, SOP management, FDA/GMP compliance tracking</td></tr>
            <tr><td>9</td><td><strong>Supply Chain</strong></td><td>8</td><td>Logistics, shipping, demand forecasting, supplier scorecards</td></tr>
            <tr><td>10</td><td><strong>Project Management</strong></td><td>7</td><td>Task boards, Gantt charts, resource allocation, time tracking</td></tr>
            <tr><td>11</td><td><strong>Document Management</strong></td><td>5</td><td>Version control, electronic signatures, document workflows</td></tr>
            <tr><td>12</td><td><strong>Business Intelligence</strong></td><td>8</td><td>Dashboards, KPIs, ad-hoc reporting, data visualization</td></tr>
            <tr><td>13</td><td><strong>Laboratory (LIMS)</strong></td><td>6</td><td>Sample management, test scheduling, instrument integration, COA</td></tr>
            <tr><td>14</td><td><strong>Maintenance (CMMS)</strong></td><td>5</td><td>Asset management, preventive maintenance, work orders, spare parts</td></tr>
            <tr><td>15</td><td><strong>System Administration</strong></td><td>8</td><td>User management, roles/permissions, audit logs, system configuration</td></tr>
        </tbody>
    </table>

    <h3 class="subsection-title" id="section-6-2">6.2 License Tiers</h3>
    <p>Modules are activated based on your license tier. Each tier unlocks progressively more modules and features:</p>

    <div class="tier-cards no-break">
        <div class="tier-card">
            <h4>Trial</h4>
            <p>Up to 5 users<br>Core modules only<br>30-day evaluation<br>Community support</p>
        </div>
        <div class="tier-card">
            <h4>Standard</h4>
            <p>Up to 50 users<br>8 module categories<br>Annual license<br>Email support</p>
        </div>
        <div class="tier-card highlight">
            <h4>Professional</h4>
            <p>Up to 200 users<br>12 module categories<br>Annual license<br>Priority support</p>
        </div>
        <div class="tier-card">
            <h4>Enterprise</h4>
            <p>Unlimited users<br>All 15 categories<br>Perpetual license<br>Dedicated support &amp; SLA</p>
        </div>
    </div>

    <p>To activate modules, navigate to <strong>Control Panel &gt; License &amp; Modules</strong> and enter your license key. The system will automatically enable the modules included in your tier. Additional modules may be purchased individually as add-ons.</p>
</div>

<!-- ==================== SECTION 7: TROUBLESHOOTING ==================== -->
<div class="manual-section" id="section-7">
    <div class="section-header">
        <span class="section-number">Section 7</span>
        <div class="section-title">Troubleshooting</div>
    </div>

    <p>The following table lists common issues encountered during installation and their resolutions:</p>

    <table class="manual-table">
        <thead>
            <tr><th style="width:22%;">Problem</th><th style="width:28%;">Possible Cause</th><th style="width:50%;">Solution</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Database connection failed</strong></td>
                <td>Port mismatch between <code>my.ini</code> and <code>database.php</code>; MySQL service not running</td>
                <td>Verify that both <code>my.ini</code> and <code>config/database.php</code> specify port <strong>3311</strong>. Confirm MySQL is running in the XAMPP Control Panel. Test connectivity with: <code>mysql -u root -P 3311</code></td>
            </tr>
            <tr>
                <td><strong>404 Not Found errors</strong></td>
                <td><code>mod_rewrite</code> not enabled; <code>.htaccess</code> file missing or <code>AllowOverride</code> set to <code>None</code></td>
                <td>Enable <code>mod_rewrite</code> in <code>httpd.conf</code>. Ensure <code>AllowOverride All</code> is set for the document root. Verify <code>.htaccess</code> exists in <code>C:\xampp\htdocs\enpharchem</code>. Restart Apache.</td>
            </tr>
            <tr>
                <td><strong>Blank white page</strong></td>
                <td>PHP fatal error with display_errors disabled; missing PHP extensions</td>
                <td>Check <code>C:\xampp\php\logs\php_error_log</code> for errors. Temporarily set <code>display_errors = On</code> in <code>php.ini</code>. Verify required PHP extensions (pdo_mysql, mbstring, openssl, curl, gd, zip) are enabled.</td>
            </tr>
            <tr>
                <td><strong>Login fails (invalid credentials)</strong></td>
                <td>Password hash mismatch; super user not created properly</td>
                <td>Re-run <code>setup_superuser.php</code> to create a new admin account. If the issue persists, run <code>reset_admin.php</code> to reset the admin password. Check that the PHP <code>password_hash()</code> algorithm is supported.</td>
            </tr>
            <tr>
                <td><strong>Module not found</strong></td>
                <td>Database installation incomplete; module records missing from the <code>modules</code> table</td>
                <td>Re-run <code>install.php</code> to re-register modules. If the issue persists, drop and recreate the database, then run the full installation sequence again (Steps 4.6&ndash;4.8).</td>
            </tr>
            <tr>
                <td><strong>Permission denied errors</strong></td>
                <td>File/directory permissions too restrictive; Apache running under wrong user</td>
                <td>On Windows: ensure the <code>enpharchem</code> folder is not read-only. On Linux: set ownership to <code>daemon:daemon</code> and permissions to <code>755</code> (dirs) / <code>644</code> (files).</td>
            </tr>
            <tr>
                <td><strong>Slow page load times</strong></td>
                <td>InnoDB buffer pool too small; insufficient server resources</td>
                <td>Increase <code>innodb_buffer_pool_size</code> in <code>my.ini</code> (set to 50&ndash;70% of available RAM for dedicated DB servers). Enable PHP OPcache. Check server resource usage with Task Manager or <code>htop</code>.</td>
            </tr>
            <tr>
                <td><strong>SSL certificate errors</strong></td>
                <td>Self-signed certificate; certificate chain incomplete</td>
                <td>Install a valid SSL certificate from a trusted CA. Ensure the <code>SSLCertificateChainFile</code> directive includes the full certificate chain. For development, add the self-signed cert to the browser's trusted store.</td>
            </tr>
            <tr>
                <td><strong>Email notifications not sending</strong></td>
                <td>SMTP credentials incorrect; port blocked by firewall; TLS mismatch</td>
                <td>Verify SMTP settings in <code>config/mail.php</code>. Test SMTP connectivity with: <code>telnet smtp.your-domain.com 587</code>. Check that your email provider allows less-secure app access or has an app-specific password configured.</td>
            </tr>
            <tr>
                <td><strong>Session timeout too short</strong></td>
                <td>PHP <code>session.gc_maxlifetime</code> set too low</td>
                <td>Update <code>php.ini</code>: set <code>session.gc_maxlifetime = 7200</code> (2 hours). Restart Apache. This can also be configured in <strong>Control Panel &gt; System Settings &gt; Session Management</strong>.</td>
            </tr>
        </tbody>
    </table>

    <div class="info-box note">
        <strong>Diagnostic Tip:</strong> For any unresolved issues, collect the following files and send them to EnPharChem support:
        <ul style="margin-top:6px; margin-bottom:0;">
            <li><code>C:\xampp\php\logs\php_error_log</code></li>
            <li><code>C:\xampp\apache\logs\enpharchem-error.log</code></li>
            <li><code>C:\xampp\mysql\data\*.err</code></li>
            <li>Screenshot of the XAMPP Control Panel showing service status</li>
        </ul>
    </div>
</div>

<!-- ==================== SECTION 8: SUPPORT & CONTACT ==================== -->
<div class="manual-section" id="section-8">
    <div class="section-header">
        <span class="section-number">Section 8</span>
        <div class="section-title">Support &amp; Contact</div>
    </div>

    <h3 class="subsection-title">EnPharChem Technologies</h3>
    <table class="manual-table no-break">
        <thead>
            <tr><th>Contact Method</th><th>Details</th></tr>
        </thead>
        <tbody>
            <tr><td><strong>Company</strong></td><td>EnPharChem Technologies</td></tr>
            <tr><td><strong>Website</strong></td><td>https://www.enpharchem.com</td></tr>
            <tr><td><strong>Support Portal</strong></td><td>https://support.enpharchem.com</td></tr>
            <tr><td><strong>Email (General)</strong></td><td>info@enpharchem.com</td></tr>
            <tr><td><strong>Email (Technical Support)</strong></td><td>support@enpharchem.com</td></tr>
            <tr><td><strong>Phone</strong></td><td>+1 (800) 555-EPCH (3724)</td></tr>
            <tr><td><strong>Support Hours</strong></td><td>Monday &ndash; Friday, 8:00 AM &ndash; 8:00 PM EST</td></tr>
        </tbody>
    </table>

    <h3 class="subsection-title">Support Tiers</h3>
    <table class="manual-table no-break">
        <thead>
            <tr><th>Tier</th><th>Response Time</th><th>Channels</th><th>Availability</th><th>Included With</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Community</strong></td>
                <td>Best effort</td>
                <td>Online forums, knowledge base</td>
                <td>Self-service</td>
                <td>Trial license</td>
            </tr>
            <tr>
                <td><strong>Standard</strong></td>
                <td>Within 24 hours</td>
                <td>Email, support portal</td>
                <td>Business hours</td>
                <td>Standard license</td>
            </tr>
            <tr>
                <td><strong>Premium</strong></td>
                <td>Within 4 hours</td>
                <td>Email, phone, support portal, screen sharing</td>
                <td>Extended hours (6 AM &ndash; 10 PM)</td>
                <td>Professional license</td>
            </tr>
            <tr>
                <td><strong>Enterprise</strong></td>
                <td>Within 1 hour (P1)</td>
                <td>Dedicated account manager, all channels, on-site</td>
                <td>24/7/365</td>
                <td>Enterprise license</td>
            </tr>
        </tbody>
    </table>

    <div class="info-box note">
        <strong>Before Contacting Support:</strong> Please have the following information ready to expedite resolution:
        <ul style="margin-top:6px; margin-bottom:0;">
            <li>Your EnPharChem license key and version number</li>
            <li>Operating system and XAMPP version</li>
            <li>A description of the issue and steps to reproduce</li>
            <li>Relevant log files (see Section 7 for locations)</li>
            <li>Screenshots of error messages, if applicable</li>
        </ul>
    </div>
</div>

<!-- ==================== FOOTER ==================== -->
<div class="page-footer-screen">
    &copy; 2026 EnPharChem Technologies &mdash; Installation Manual v1.0 &mdash; Confidential
</div>

</div><!-- /.manual-container -->

<!-- Print footer (appears on every printed page) -->
<div class="page-footer-print">
    &copy; 2026 EnPharChem Technologies &mdash; Installation Manual v1.0 &mdash; Confidential
</div>

</body>
</html>