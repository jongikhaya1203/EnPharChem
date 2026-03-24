<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - <?= htmlspecialchars($cert['certificate_number']) ?> - EnPharChem</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: #f5f5f5;
            color: #1a1a2e;
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        /* Print bar - hidden when printing */
        .print-bar {
            background: #1a1d23;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .print-bar a, .print-bar button {
            color: #fff;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 20px;
            border-radius: 6px;
            background: transparent;
            cursor: pointer;
            font-size: 14px;
            font-family: 'Segoe UI', sans-serif;
        }
        .print-bar a:hover, .print-bar button:hover {
            background: rgba(255,255,255,0.1);
        }
        .print-bar .btn-primary { background: #0d6efd; border-color: #0d6efd; }
        .print-bar .btn-warning { background: #d4af37; border-color: #d4af37; color: #1a1a2e; }

        @media print {
            .print-bar { display: none !important; }
            body { background: white; }
            .certificate-wrapper { padding: 0 !important; }
        }

        .certificate-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 60px);
            padding: 30px;
        }

        .certificate {
            width: 1050px;
            min-height: 700px;
            background: #fffef7;
            position: relative;
            padding: 20px;
        }

        /* Outer ornate border */
        .certificate-border {
            border: 3px solid #d4af37;
            padding: 8px;
            height: 100%;
        }

        .certificate-border-inner {
            border: 1px solid #d4af37;
            padding: 6px;
        }

        .certificate-border-innermost {
            border: 2px solid #1a1a2e;
            padding: 40px 50px;
            position: relative;
        }

        /* Corner ornaments */
        .corner-ornament {
            position: absolute;
            width: 60px;
            height: 60px;
            border-color: #d4af37;
        }
        .corner-ornament.tl { top: 10px; left: 10px; border-top: 3px solid; border-left: 3px solid; }
        .corner-ornament.tr { top: 10px; right: 10px; border-top: 3px solid; border-right: 3px solid; }
        .corner-ornament.bl { bottom: 10px; left: 10px; border-bottom: 3px solid; border-left: 3px solid; }
        .corner-ornament.br { bottom: 10px; right: 10px; border-bottom: 3px solid; border-right: 3px solid; }

        /* Logo */
        .cert-logo {
            text-align: center;
            margin-bottom: 15px;
        }
        .cert-logo .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            font-family: 'Segoe UI', sans-serif;
            font-weight: 800;
            letter-spacing: -1px;
        }
        .cert-logo .company-name {
            font-family: 'Segoe UI', sans-serif;
            font-size: 14px;
            color: #555;
            margin-top: 5px;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .cert-title {
            text-align: center;
            margin-bottom: 10px;
        }
        .cert-title h1 {
            font-size: 36px;
            color: #1a1a2e;
            letter-spacing: 6px;
            text-transform: uppercase;
            font-weight: 400;
            border-bottom: 2px solid #d4af37;
            display: inline-block;
            padding-bottom: 6px;
        }

        .cert-subtitle {
            text-align: center;
            font-size: 15px;
            color: #666;
            font-style: italic;
            margin-bottom: 20px;
        }

        .cert-name {
            text-align: center;
            margin-bottom: 15px;
        }
        .cert-name h2 {
            font-size: 38px;
            color: #1a1a2e;
            font-weight: 700;
            font-family: Georgia, serif;
            border-bottom: 2px solid #d4af37;
            display: inline-block;
            padding: 0 40px 8px;
        }

        .cert-course-text {
            text-align: center;
            font-size: 15px;
            color: #666;
            font-style: italic;
            margin-bottom: 8px;
        }

        .cert-course {
            text-align: center;
            margin-bottom: 8px;
        }
        .cert-course h3 {
            font-size: 22px;
            color: #1a1a2e;
            font-weight: 600;
        }

        .cert-level {
            text-align: center;
            margin-bottom: 8px;
        }
        .cert-level .level-badge {
            display: inline-block;
            padding: 4px 20px;
            border: 2px solid #d4af37;
            border-radius: 20px;
            font-size: 13px;
            font-family: 'Segoe UI', sans-serif;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1a1a2e;
        }

        .cert-score {
            text-align: center;
            font-size: 15px;
            color: #555;
            margin-bottom: 20px;
        }
        .cert-score strong {
            color: #1a1a2e;
            font-size: 18px;
        }

        .cert-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        .cert-details .detail {
            text-align: center;
        }
        .cert-details .detail-label {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .cert-details .detail-value {
            font-size: 14px;
            color: #1a1a2e;
            font-weight: 600;
        }

        .cert-signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 15px;
        }
        .cert-signature {
            text-align: center;
            width: 220px;
        }
        .cert-signature .sig-line {
            border-top: 1px solid #1a1a2e;
            margin-bottom: 5px;
            padding-top: 5px;
        }
        .cert-signature .sig-name {
            font-size: 14px;
            color: #1a1a2e;
            font-weight: 600;
        }
        .cert-signature .sig-title {
            font-size: 11px;
            color: #777;
        }

        .cert-seal {
            text-align: center;
        }
        .cert-seal .seal-circle {
            width: 70px;
            height: 70px;
            border: 2px solid #d4af37;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #d4af37;
            font-size: 10px;
            font-family: 'Segoe UI', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }

        .cert-verification {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        .cert-verification span {
            font-size: 11px;
            color: #999;
            font-family: 'Segoe UI', sans-serif;
        }
    </style>
</head>
<body>

<!-- Print Bar -->
<div class="print-bar">
    <div>
        <a href="/enpharchem/training/my-certificates"><i class="fas fa-arrow-left me-1"></i> Back to Certificates</a>
        <a href="/enpharchem/training" style="margin-left: 10px;"><i class="fas fa-graduation-cap me-1"></i> Training</a>
    </div>
    <div>
        <button class="btn-warning" onclick="window.print()"><i class="fas fa-print me-1"></i> Print / Save as PDF</button>
    </div>
</div>

<!-- Certificate -->
<div class="certificate-wrapper">
    <div class="certificate">
        <div class="certificate-border">
            <div class="certificate-border-inner">
                <div class="certificate-border-innermost">
                    <div class="corner-ornament tl"></div>
                    <div class="corner-ornament tr"></div>
                    <div class="corner-ornament bl"></div>
                    <div class="corner-ornament br"></div>

                    <!-- Logo -->
                    <div class="cert-logo">
                        <div class="logo-icon">EP</div>
                        <div class="company-name">EnPharChem Technologies</div>
                    </div>

                    <!-- Title -->
                    <div class="cert-title">
                        <h1>Certificate of Completion</h1>
                    </div>

                    <!-- Subtitle -->
                    <div class="cert-subtitle">This is to certify that</div>

                    <!-- Name -->
                    <div class="cert-name">
                        <h2><?= htmlspecialchars(($cert['first_name'] ?? '') . ' ' . ($cert['last_name'] ?? '')) ?></h2>
                    </div>

                    <!-- Course text -->
                    <div class="cert-course-text">has successfully completed the course</div>

                    <!-- Course title -->
                    <div class="cert-course">
                        <h3><?= htmlspecialchars($cert['course_title'] ?? '') ?></h3>
                    </div>

                    <!-- Level -->
                    <div class="cert-level">
                        <span class="level-badge"><?= htmlspecialchars(ucfirst($cert['course_level'] ?? 'Beginner')) ?> Level</span>
                    </div>

                    <!-- Score -->
                    <div class="cert-score">
                        with a score of <strong><?= number_format($cert['score'] ?? 0, 1) ?>%</strong>
                    </div>

                    <!-- Details -->
                    <div class="cert-details">
                        <div class="detail">
                            <div class="detail-label">Certificate Number</div>
                            <div class="detail-value"><?= htmlspecialchars($cert['certificate_number'] ?? '') ?></div>
                        </div>
                        <div class="detail">
                            <div class="detail-label">Issue Date</div>
                            <div class="detail-value"><?= date('F d, Y', strtotime($cert['issue_date'] ?? 'now')) ?></div>
                        </div>
                        <div class="detail">
                            <div class="detail-label">Expiry Date</div>
                            <div class="detail-value"><?= !empty($cert['expiry_date']) ? date('F d, Y', strtotime($cert['expiry_date'])) : 'N/A' ?></div>
                        </div>
                    </div>

                    <!-- Signatures -->
                    <div class="cert-signatures">
                        <div class="cert-signature">
                            <div class="sig-line"></div>
                            <div class="sig-name">Dr. Sarah Chen</div>
                            <div class="sig-title">Course Director</div>
                        </div>
                        <div class="cert-seal">
                            <div class="seal-circle">
                                <div>
                                    <div style="font-size:16px;">EP</div>
                                    <div style="font-size:7px;margin-top:2px;">CERTIFIED</div>
                                </div>
                            </div>
                        </div>
                        <div class="cert-signature">
                            <div class="sig-line"></div>
                            <div class="sig-name">EnPharChem Technologies</div>
                            <div class="sig-title">Authorized Signatory</div>
                        </div>
                    </div>

                    <!-- Verification -->
                    <div class="cert-verification">
                        <span>Verify this certificate at enpharchem.com/verify | Certificate No: <?= htmlspecialchars($cert['certificate_number'] ?? '') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
