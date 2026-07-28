<?php
/**
 * EnPharChem - Module Interface Figures for PDF output
 * ----------------------------------------------------
 * Draws the same 15 category archetypes as lib/ModuleMockup.php (which emits
 * SVG for the HTML views), but using native PDF vector operators so the
 * figures stay sharp at any zoom and embed no raster images.
 *
 * Figures are deterministic: values derive from a hash of the module slug, so
 * a module renders identically in the PDF and the HTML view, and identically
 * across regenerations.
 *
 * These are representative interface illustrations, not captured screenshots.
 * The values shown are sample data; documents embedding them say so.
 */
require_once __DIR__ . '/MiniPDF.php';

class ModuleMockupPdf
{
    // Palette (dark instrument-panel look, matching the SVG figures).
    const BG      = [15, 27, 45];
    const BAR     = [22, 40, 63];
    const PANEL   = [30, 51, 80];
    const EDGE    = [61, 110, 168];
    const GRID    = [36, 64, 95];
    const TEXT    = [207, 226, 247];
    const DIM     = [127, 166, 217];
    const CYAN    = [126, 224, 255];
    const BLUE    = [74, 163, 255];
    const AMBER   = [245, 167, 66];
    const RED     = [239, 96, 96];
    const GREEN   = [16, 185, 129];
    const MUTED   = [92, 127, 168];

    private static $map = [
        'process-sim-energy'       => 'flowsheet',
        'process-sim-chemicals'    => 'flowsheet',
        'exchanger-design'         => 'exchanger',
        'concurrent-feed'          => 'gantt',
        'subsurface-science'       => 'strata',
        'energy-optimization'      => 'composite',
        'operations-support'       => 'kpi',
        'advanced-process-control' => 'trend',
        'dynamic-optimization'     => 'convergence',
        'mes'                      => 'batchgrid',
        'petroleum-supply-chain'   => 'network',
        'supply-chain-mgmt'        => 'gantt',
        'apm'                      => 'gauges',
        'industrial-data-fabric'   => 'pipeline',
        'digital-grid-mgmt'        => 'oneline',
    ];

    /** Same deterministic generator as the SVG renderer. */
    private static function num($seed, $i, $min, $max)
    {
        $h = crc32($seed . '#' . $i);
        return $min + ($h % max(1, ($max - $min + 1)));
    }

    /**
     * Draw a module figure into the given rectangle (top-origin points).
     *
     * @param MiniPDF $pdf
     * @param array   $module        module row (needs slug, name)
     * @param string  $categorySlug
     * @param string  $categoryName
     */
    public static function draw($pdf, $module, $categorySlug, $categoryName, $x, $y, $w, $h)
    {
        $slug = $module['slug'] ?? 'module';
        $name = $module['name'] ?? 'Module';

        // Window chrome
        $pdf->roundRectFill($x, $y, $w, $h, 3, self::BG[0], self::BG[1], self::BG[2]);
        $pdf->fillRect($x, $y, $w, 13, self::BAR[0], self::BAR[1], self::BAR[2]);
        $pdf->circle($x + 6,  $y + 6.5, 2, self::RED);
        $pdf->circle($x + 13, $y + 6.5, 2, self::AMBER);
        $pdf->circle($x + 20, $y + 6.5, 2, self::GREEN);

        // Title takes what it needs; the category label gets the remainder so it
        // is only ever truncated when the two genuinely collide.
        $pdf->setFont('B', 6.2);
        $pdf->setTextColor(self::TEXT[0], self::TEXT[1], self::TEXT[2]);
        $title = self::fit($pdf, $name, $w * 0.60);
        $pdf->text($x + 26, $y + 3.6, $title);
        $titleW = $pdf->stringWidth($title);

        $pdf->setFont('', 5.2);
        $pdf->setTextColor(self::DIM[0], self::DIM[1], self::DIM[2]);
        $avail = $w - 26 - $titleW - 14;
        if ($avail > 24) {
            $pdf->textRight($x + $w - 5, $y + 4.2, self::fit($pdf, $categoryName, $avail));
        }

        // Plot area
        $px = $x + 5;
        $py = $y + 17;
        $pw = $w - 10;
        $ph = $h - 17 - 14;   // leave a readout strip at the bottom

        $method = self::$map[$categorySlug] ?? 'generic';
        $read = self::$method($pdf, $slug, $px, $py, $pw, $ph);

        self::readout($pdf, $x, $y + $h - 12, $w, $read);
    }

    /** Truncate a string to fit a width in the current font. */
    private static function fit($pdf, $s, $maxW)
    {
        if ($pdf->stringWidth($s) <= $maxW) { return $s; }
        while (strlen($s) > 1 && $pdf->stringWidth($s . '..') > $maxW) {
            $s = substr($s, 0, -1);
        }
        return rtrim($s) . '..';
    }

    /** Bottom strip of label/value readouts. */
    private static function readout($pdf, $x, $y, $w, $pairs)
    {
        $n = max(1, count($pairs));
        $colW = ($w - 10) / $n;
        $i = 0;
        foreach ($pairs as $label => $value) {
            $cx = $x + 5 + $i * $colW;
            $pdf->setFont('', 4.6);
            $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
            $pdf->text($cx, $y + 6.5, (string)$label);
            $pdf->setFont('B', 6);
            $pdf->setTextColor(self::CYAN[0], self::CYAN[1], self::CYAN[2]);
            $pdf->text($cx, $y, (string)$value);
            $i++;
        }
    }

    private static function label($pdf, $x, $y, $s, $col, $size = 5.2, $bold = false)
    {
        $pdf->setFont($bold ? 'B' : '', $size);
        $pdf->setTextColor($col[0], $col[1], $col[2]);
        $pdf->text($x, $y, $s);
    }

    // ------------------------------------------------------------ archetypes

    private static function flowsheet($pdf, $s, $x, $y, $w, $h)
    {
        $cy = $y + $h * 0.45;
        $uw = $w * 0.13; $uh = $h * 0.3;
        $x1 = $x + $w * 0.18; $x2 = $x + $w * 0.45; $x3 = $x + $w * 0.72;

        $pdf->line($x + 6, $cy, $x1, $cy, self::EDGE[0], self::EDGE[1], self::EDGE[2], 1);
        $pdf->arrowHead($x1, $cy, 'right', 3, self::EDGE[0], self::EDGE[1], self::EDGE[2]);
        $pdf->line($x1 + $uw, $cy, $x2, $cy, self::EDGE[0], self::EDGE[1], self::EDGE[2], 1);
        $pdf->arrowHead($x2, $cy, 'right', 3, self::EDGE[0], self::EDGE[1], self::EDGE[2]);
        $pdf->line($x2 + $uw, $cy, $x3 - 6, $cy, self::EDGE[0], self::EDGE[1], self::EDGE[2], 1);
        $pdf->arrowHead($x3 - 6, $cy, 'right', 3, self::EDGE[0], self::EDGE[1], self::EDGE[2]);

        $pdf->fillRect($x1, $cy - $uh / 2, $uw, $uh, self::PANEL[0], self::PANEL[1], self::PANEL[2]);
        $pdf->strokeRect($x1, $cy - $uh / 2, $uw, $uh, self::EDGE[0], self::EDGE[1], self::EDGE[2], 0.6);
        $pdf->fillRect($x2, $cy - $uh / 2, $uw, $uh, self::PANEL[0], self::PANEL[1], self::PANEL[2]);
        $pdf->strokeRect($x2, $cy - $uh / 2, $uw, $uh, self::EDGE[0], self::EDGE[1], self::EDGE[2], 0.6);
        $pdf->ellipse($x3 + 8, $cy, 9, $uh * 0.75, self::PANEL, self::EDGE, 0.6);

        // product legs
        $pdf->line($x3 + 17, $cy - $uh * 0.4, $x + $w - 20, $y + $h * 0.14, self::CYAN[0], self::CYAN[1], self::CYAN[2], 1);
        $pdf->line($x3 + 17, $cy + $uh * 0.4, $x + $w - 20, $y + $h * 0.78, self::AMBER[0], self::AMBER[1], self::AMBER[2], 1);

        self::label($pdf, $x + 4, $cy - 10, 'FEED', self::DIM, 5);
        $pdf->setFont('', 5.4); $pdf->setTextColor(self::TEXT[0], self::TEXT[1], self::TEXT[2]);
        $pdf->textCenter($x1 + $uw / 2, $cy - 2.5, 'E-101');
        $pdf->textCenter($x2 + $uw / 2, $cy - 2.5, 'R-101');
        $pdf->textCenter($x3 + 8, $cy - 2.5, 'V-101');
        self::label($pdf, $x + $w - 26, $y + $h * 0.10, 'VAPOUR', self::CYAN, 4.8);
        self::label($pdf, $x + $w - 24, $y + $h * 0.80, 'LIQUID', self::AMBER, 4.8);

        return [
            'Temperature' => self::num($s, 1, 28, 92) . ' C',
            'Pressure'    => self::num($s, 2, 180, 980) . ' kPa',
            'Duty'        => (self::num($s, 3, 4, 38) / 10) . ' MW',
            'Status'      => 'Converged',
        ];
    }

    private static function exchanger($pdf, $s, $x, $y, $w, $h)
    {
        $cy = $y + $h * 0.45;
        $sx = $x + $w * 0.16; $sw = $w * 0.66; $sh = $h * 0.42;
        $pdf->roundRectFill($sx, $cy - $sh / 2, $sw, $sh, $sh / 2, self::PANEL[0], self::PANEL[1], self::PANEL[2]);
        for ($i = 0; $i < 4; $i++) {
            $ty = $cy - $sh / 2 + $sh * ($i + 1) / 5;
            $pdf->line($sx + 6, $ty, $sx + $sw - 6, $ty, self::BLUE[0], self::BLUE[1], self::BLUE[2], 0.7);
        }
        $pdf->line($x + 4, $cy, $sx, $cy, self::RED[0], self::RED[1], self::RED[2], 1.2);
        $pdf->arrowHead($sx, $cy, 'right', 3, self::RED[0], self::RED[1], self::RED[2]);
        $pdf->line($sx + $sw, $cy, $x + $w - 4, $cy, self::BLUE[0], self::BLUE[1], self::BLUE[2], 1.2);
        $pdf->arrowHead($x + $w - 4, $cy, 'right', 3, self::BLUE[0], self::BLUE[1], self::BLUE[2]);
        $pdf->line($sx + $sw * 0.2, $y + 4, $sx + $sw * 0.2, $cy - $sh / 2, self::BLUE[0], self::BLUE[1], self::BLUE[2], 1.2);
        $pdf->line($sx + $sw * 0.8, $cy + $sh / 2, $sx + $sw * 0.8, $y + $h - 4, self::RED[0], self::RED[1], self::RED[2], 1.2);

        self::label($pdf, $x + 2, $cy - 9, 'HOT IN', self::RED, 4.8);
        self::label($pdf, $x + $w - 26, $cy - 9, 'HOT OUT', self::BLUE, 4.8);
        self::label($pdf, $sx + $sw * 0.2 - 8, $y, 'COLD IN', self::BLUE, 4.8);
        self::label($pdf, $sx + $sw * 0.8 - 10, $y + $h - 4, 'COLD OUT', self::RED, 4.8);

        return [
            'Duty'   => self::num($s, 1, 240, 3800) . ' kW',
            'LMTD'   => self::num($s, 2, 12, 68) . ' K',
            'Area'   => self::num($s, 3, 40, 620) . ' m2',
            'Passes' => '1-2',
        ];
    }

    private static function gantt($pdf, $s, $x, $y, $w, $h)
    {
        $rows = ['Scope', 'Equipment', 'Estimate', 'Schedule', 'Review'];
        $labW = $w * 0.28;
        $trackX = $x + $labW;
        $trackW = $w - $labW - 4;
        $rowH = min(9, ($h - 12) / count($rows));
        foreach ($rows as $i => $lab) {
            $ry = $y + 2 + $i * ($rowH + 2.5);
            self::label($pdf, $x + 2, $ry + 1.5, $lab, self::DIM, 5);
            $pdf->fillRect($trackX, $ry, $trackW, $rowH, self::BAR[0], self::BAR[1], self::BAR[2]);
            $off = $trackW * (self::num($s, $i, 0, 30) / 100);
            $bw  = $trackW * (0.28 + self::num($s, $i + 10, 5, 45) / 100);
            if ($off + $bw > $trackW) { $bw = $trackW - $off; }
            $shade = 0.55 + $i * 0.08;
            $pdf->fillRect($trackX + $off, $ry, max(4, $bw), $rowH,
                (int)(47 * $shade + 30), (int)(127 * $shade + 40), (int)(212 * $shade + 30));
        }
        for ($i = 0; $i <= 4; $i++) {
            $gx = $trackX + $trackW * $i / 4;
            $pdf->line($gx, $y, $gx, $y + $h - 8, self::GRID[0], self::GRID[1], self::GRID[2], 0.3);
        }
        return [
            'Activities' => count($rows),
            'Duration'   => self::num($s, 5, 12, 40) . ' wks',
            'Status'     => 'On track',
        ];
    }

    private static function strata($pdf, $s, $x, $y, $w, $h)
    {
        $cols = [[43, 74, 107], [53, 96, 138], [192, 139, 74], [138, 106, 61], [74, 107, 138]];
        $secW = $w * 0.62;
        $ty = $y;
        foreach ($cols as $i => $c) {
            $bh = ($h - 6) / count($cols) + self::num($s, $i, -3, 3);
            if ($ty + $bh > $y + $h - 4) { $bh = $y + $h - 4 - $ty; }
            if ($bh <= 0) { break; }
            $pdf->fillRect($x, $ty, $secW, $bh, $c[0], $c[1], $c[2]);
            $ty += $bh;
        }
        $wx = $x + $secW * 0.42;
        $pdf->line($wx, $y, $wx, $ty, 245, 247, 250, 1.4);
        $pdf->circle($wx, $y + 1.5, 2, self::CYAN);
        self::label($pdf, $wx + 3, $y - 1, 'WELL-01', self::TEXT, 4.8);

        // gamma-ray style log trace
        $lx = $x + $secW + 8;
        $lw = $w - $secW - 12;
        $pts = [];
        for ($i = 0; $i <= 16; $i++) {
            $pts[] = [$lx + ($lw * self::num($s, $i + 20, 10, 95) / 100), $y + ($h - 6) * $i / 16];
        }
        $pdf->polyline($pts, self::CYAN[0], self::CYAN[1], self::CYAN[2], 0.7);
        self::label($pdf, $lx, $y + $h - 4, 'GR log', self::MUTED, 4.6);

        return [
            'Net pay'  => self::num($s, 1, 8, 60) . ' m',
            'Porosity' => (self::num($s, 2, 80, 280) / 10) . ' %',
            'Sw'       => (self::num($s, 3, 150, 480) / 10) . ' %',
        ];
    }

    private static function composite($pdf, $s, $x, $y, $w, $h)
    {
        $ax = $x + 14; $ay = $y + $h - 8; $aw = $w - 20; $ah = $h - 14;
        $pdf->line($ax, $ay, $ax + $aw, $ay, self::EDGE[0], self::EDGE[1], self::EDGE[2], 0.5);
        $pdf->line($ax, $ay - $ah, $ax, $ay, self::EDGE[0], self::EDGE[1], self::EDGE[2], 0.5);
        $hot = []; $cold = [];
        for ($i = 0; $i <= 8; $i++) {
            $hot[]  = [$ax + 4 + $aw * 0.9 * $i / 8, $ay - $ah * (0.08 + 0.09 * $i) - self::num($s, $i, 0, 4)];
            $cold[] = [$ax + 12 + $aw * 0.85 * $i / 8, $ay - $ah * (0.03 + 0.085 * $i) - self::num($s, $i + 8, 0, 3)];
        }
        $pdf->polyline($hot, self::RED[0], self::RED[1], self::RED[2], 1.2);
        $pdf->polyline($cold, self::BLUE[0], self::BLUE[1], self::BLUE[2], 1.2);
        $pinch = $hot[4];
        $pdf->circle($pinch[0], $pinch[1], 2.6, null, [255, 209, 102], 0.9);
        self::label($pdf, $pinch[0] + 4, $pinch[1] - 3, 'PINCH', [255, 209, 102], 4.8);
        self::label($pdf, $ax + 3, $y, 'Hot composite', self::RED, 4.8);
        self::label($pdf, $ax + 3, $y + 6, 'Cold composite', self::BLUE, 4.8);
        return [
            'dTmin'       => self::num($s, 1, 5, 25) . ' K',
            'Hot utility' => self::num($s, 2, 200, 2400) . ' kW',
            'Cold util.'  => self::num($s, 3, 150, 2000) . ' kW',
        ];
    }

    private static function kpi($pdf, $s, $x, $y, $w, $h)
    {
        $tiles = ['Availability' => '%', 'Throughput' => ' t/h', 'Energy' => ' GJ/t', 'On-spec' => '%'];
        $tw = ($w - 9) / 4;
        $i = 0;
        foreach ($tiles as $lab => $unit) {
            $tx = $x + $i * ($tw + 3);
            $pdf->roundRectFill($tx, $y, $tw, $h * 0.42, 2, self::BAR[0], self::BAR[1], self::BAR[2]);
            self::label($pdf, $tx + 3, $y + 3, $lab, self::DIM, 4.6);
            $pdf->setFont('B', 11);
            $pdf->setTextColor(self::CYAN[0], self::CYAN[1], self::CYAN[2]);
            $pdf->text($tx + 3, $y + 10, self::num($s, $i, 62, 99) . $unit);
            $i++;
        }
        $alarms = ['TI-2041 high', 'PC-1180 deviation', 'FI-3302 low flow'];
        $cols = [self::RED, self::AMBER, self::GREEN];
        foreach ($alarms as $j => $a) {
            $ry = $y + $h * 0.5 + $j * ($h * 0.16);
            $pdf->fillRect($x, $ry, $w, $h * 0.13, self::BAR[0], self::BAR[1], self::BAR[2]);
            $pdf->circle($x + 5, $ry + $h * 0.065, 1.8, $cols[$j]);
            self::label($pdf, $x + 10, $ry + $h * 0.03, $a, self::TEXT, 5);
            $pdf->setFont('', 4.8);
            $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
            $pdf->textRight($x + $w - 3, $ry + $h * 0.03, self::num($s, $j, 1, 59) . ' min');
        }
        return ['Active alarms' => count($alarms), 'Shift' => 'A', 'Status' => 'Monitoring'];
    }

    private static function trend($pdf, $s, $x, $y, $w, $h)
    {
        $ax = $x + 12; $ay = $y + $h - 6; $aw = $w - 18; $ah = $h - 12;
        $pdf->line($ax, $ay, $ax + $aw, $ay, self::EDGE[0], self::EDGE[1], self::EDGE[2], 0.5);
        $pdf->line($ax, $ay - $ah, $ax, $ay, self::EDGE[0], self::EDGE[1], self::EDGE[2], 0.5);
        $spY = $ay - $ah * 0.62;
        $pdf->dashLine($ax, $spY, $ax + $aw, $spY, 255, 209, 102, 0.6);
        $pv = []; $op = [];
        for ($i = 0; $i <= 22; $i++) {
            $base = ($i > 8) ? $spY : $spY + $ah * 0.22;
            $pv[] = [$ax + $aw * $i / 22, $base + self::num($s, $i, -4, 4)];
            $op[] = [$ax + $aw * $i / 22, $ay - 3 - $ah * (self::num($s, $i + 30, 5, 40) / 100)];
        }
        $pdf->polyline($pv, self::BLUE[0], self::BLUE[1], self::BLUE[2], 1.1);
        $pdf->polyline($op, self::GREEN[0], self::GREEN[1], self::GREEN[2], 0.8);
        self::label($pdf, $ax + 2, $y, 'SP', [255, 209, 102], 4.8);
        self::label($pdf, $ax + 14, $y, 'PV', self::BLUE, 4.8);
        self::label($pdf, $ax + 26, $y, 'OP', self::GREEN, 4.8);
        return ['Service factor' => self::num($s, 1, 88, 99) . ' %', 'Mode' => 'Auto', 'Constraint' => 'Inactive'];
    }

    private static function convergence($pdf, $s, $x, $y, $w, $h)
    {
        $ax = $x + 12; $ay = $y + $h - 6; $aw = $w - 18; $ah = $h - 12;
        $pdf->line($ax, $ay, $ax + $aw, $ay, self::EDGE[0], self::EDGE[1], self::EDGE[2], 0.5);
        $pdf->line($ax, $ay - $ah, $ax, $ay, self::EDGE[0], self::EDGE[1], self::EDGE[2], 0.5);
        $pts = [];
        for ($i = 0; $i <= 20; $i++) {
            $v = 1 - exp(-$i / 4.5);
            $pts[] = [$ax + $aw * $i / 20, $ay - $ah + $ah * $v * 0.92 - self::num($s, $i, 0, 3)];
        }
        $pdf->polyline($pts, self::CYAN[0], self::CYAN[1], self::CYAN[2], 1.2);
        for ($i = 0; $i <= 20; $i += 4) {
            $pdf->circle($pts[$i][0], $pts[$i][1], 1.4, self::CYAN);
        }
        self::label($pdf, $ax + 2, $y, 'Objective value', self::DIM, 4.8);
        self::label($pdf, $ax + $aw - 22, $ay + 4, 'Iteration', self::MUTED, 4.6);
        return [
            'Iterations' => self::num($s, 1, 12, 80),
            'Objective'  => self::num($s, 2, 100, 9000),
            'Status'     => 'Optimal',
        ];
    }

    private static function batchgrid($pdf, $s, $x, $y, $w, $h)
    {
        $heads = ['Batch', 'Product', 'Qty', 'Yield', 'Status'];
        $cw = $w / 5;
        $pdf->fillRect($x, $y, $w, 8, self::PANEL[0], self::PANEL[1], self::PANEL[2]);
        foreach ($heads as $i => $hd) {
            self::label($pdf, $x + 2 + $i * $cw, $y + 1.5, $hd, self::DIM, 4.8, true);
        }
        $rowH = min(9, ($h - 12) / 5);
        for ($r = 0; $r < 5; $r++) {
            $ry = $y + 9 + $r * ($rowH + 1);
            if ($ry + $rowH > $y + $h) { break; }
            if ($r % 2) { $pdf->fillRect($x, $ry, $w, $rowH, self::BAR[0], self::BAR[1], self::BAR[2]); }
            $cells = [
                'B-' . (2400 + self::num($s, $r, 1, 99)),
                'PRD-' . self::num($s, $r + 5, 100, 999),
                self::num($s, $r + 10, 20, 400) . ' kg',
                self::num($s, $r + 15, 88, 99) . ' %',
            ];
            foreach ($cells as $i => $c) {
                self::label($pdf, $x + 2 + $i * $cw, $ry + 1.5, $c, self::TEXT, 4.8);
            }
            $ok = $r % 3 !== 1;
            self::label($pdf, $x + 2 + 4 * $cw, $ry + 1.5, $ok ? 'Complete' : 'Running',
                $ok ? [94, 233, 181] : [255, 201, 120], 4.8);
        }
        return ['Batches' => 5, 'Avg yield' => self::num($s, 2, 90, 98) . ' %', 'OEE' => self::num($s, 3, 70, 92) . ' %'];
    }

    private static function network($pdf, $s, $x, $y, $w, $h)
    {
        $nodes = [
            [$x + $w * 0.10, $y + $h * 0.40, 'FIELD'],
            [$x + $w * 0.36, $y + $h * 0.16, 'TERMINAL'],
            [$x + $w * 0.36, $y + $h * 0.72, 'REFINERY'],
            [$x + $w * 0.66, $y + $h * 0.42, 'DEPOT'],
            [$x + $w * 0.90, $y + $h * 0.74, 'MARKET'],
        ];
        foreach ([[0,1],[0,2],[1,3],[2,3],[3,4],[2,4]] as $k => $l) {
            $a = $nodes[$l[0]]; $b = $nodes[$l[1]];
            $pdf->line($a[0], $a[1], $b[0], $b[1], 47, 127, 212, 0.4 + self::num($s, $k, 0, 10) / 10);
        }
        foreach ($nodes as $i => $n) {
            $pdf->circle($n[0], $n[1], 7, self::PANEL, self::BLUE, 0.8);
            $pdf->setFont('B', 5);
            $pdf->setTextColor(self::CYAN[0], self::CYAN[1], self::CYAN[2]);
            $pdf->textCenter($n[0], $n[1] - 2.5, (string)self::num($s, $i + 3, 10, 99));
            $pdf->setFont('', 4.4);
            $pdf->setTextColor(self::DIM[0], self::DIM[1], self::DIM[2]);
            $pdf->textCenter($n[0], $n[1] + 8, $n[2]);
        }
        return [
            'Nodes'      => count($nodes),
            'Throughput' => self::num($s, 1, 20, 180) . ' kbd',
            'Margin'     => '$' . self::num($s, 2, 2, 19) . '/bbl',
        ];
    }

    private static function gauges($pdf, $s, $x, $y, $w, $h)
    {
        $labels = ['Pump P-101', 'Compressor K-201', 'Exchanger E-301'];
        $gw = $w / 3;
        foreach ($labels as $i => $lab) {
            $cx = $x + $gw * $i + $gw / 2;
            $cy = $y + $h * 0.62;
            $rad = min($gw * 0.32, $h * 0.34);
            $v = self::num($s, $i, 42, 98);
            $col = $v > 80 ? self::GREEN : ($v > 60 ? self::AMBER : self::RED);
            $pdf->arc($cx, $cy, $rad, 180, 0, self::GRID[0], self::GRID[1], self::GRID[2], 3.2, 24);
            $pdf->arc($cx, $cy, $rad, 180, 180 - 180 * $v / 100, $col[0], $col[1], $col[2], 2.6, 24);
            $pdf->setFont('B', 10);
            $pdf->setTextColor($col[0], $col[1], $col[2]);
            $pdf->textCenter($cx, $cy - 11, (string)$v);
            $pdf->setFont('', 4.6);
            $pdf->setTextColor(self::DIM[0], self::DIM[1], self::DIM[2]);
            $pdf->textCenter($cx, $cy + 3, $lab);
        }
        return ['Assets' => 3, 'At risk' => 1, 'MTBF' => self::num($s, 4, 300, 2400) . ' h'];
    }

    private static function pipeline($pdf, $s, $x, $y, $w, $h)
    {
        $stages = ['SOURCE', 'MAP', 'TRANSFORM', 'QUALITY', 'SERVE'];
        $bw = ($w - 4 * 6) / 5;
        $by = $y + $h * 0.32;
        $bh = $h * 0.36;
        foreach ($stages as $i => $st) {
            $bx = $x + $i * ($bw + 6);
            $pdf->roundRectFill($bx, $by, $bw, $bh, 2, self::PANEL[0], self::PANEL[1], self::PANEL[2]);
            $pdf->strokeRect($bx, $by, $bw, $bh, self::EDGE[0], self::EDGE[1], self::EDGE[2], 0.5);
            $pdf->setFont('B', 4.6);
            $pdf->setTextColor(self::TEXT[0], self::TEXT[1], self::TEXT[2]);
            $pdf->textCenter($bx + $bw / 2, $by + 3, $st);
            $pdf->setFont('', 5);
            $pdf->setTextColor(self::CYAN[0], self::CYAN[1], self::CYAN[2]);
            $pdf->textCenter($bx + $bw / 2, $by + 10, (string)self::num($s, $i, 100, 9999));
            if ($i < 4) {
                $ax = $bx + $bw;
                $pdf->line($ax, $by + $bh / 2, $ax + 6, $by + $bh / 2, 47, 127, 212, 0.9);
                $pdf->arrowHead($ax + 6, $by + $bh / 2, 'right', 2.6, 47, 127, 212);
            }
        }
        self::label($pdf, $x, $y + 3, 'Tags mapped to asset model', self::DIM, 4.8);
        return [
            'Tags'    => self::num($s, 1, 400, 9000),
            'Quality' => self::num($s, 2, 95, 100) . ' %',
            'Latency' => self::num($s, 3, 1, 9) . ' s',
        ];
    }

    private static function oneline($pdf, $s, $x, $y, $w, $h)
    {
        $topY = $y + $h * 0.18; $botY = $y + $h * 0.82;
        $pdf->line($x + 2, $topY, $x + $w - 2, $topY, self::BLUE[0], self::BLUE[1], self::BLUE[2], 1.6);
        $pdf->line($x + 2, $botY, $x + $w - 2, $botY, self::BLUE[0], self::BLUE[1], self::BLUE[2], 1.6);
        self::label($pdf, $x + 2, $topY - 7, 'BUS 132 kV', self::DIM, 4.8);
        self::label($pdf, $x + 2, $botY + 2, 'BUS 33 kV', self::DIM, 4.8);
        for ($i = 0; $i < 4; $i++) {
            $tx = $x + $w * (0.16 + $i * 0.23);
            $midA = $y + $h * 0.42; $midB = $y + $h * 0.58;
            $pdf->line($tx, $topY, $tx, $midA - 4, self::EDGE[0], self::EDGE[1], self::EDGE[2], 0.8);
            $pdf->circle($tx, $midA, 4, null, self::CYAN, 0.8);
            $pdf->circle($tx, $midB, 4, null, self::CYAN, 0.8);
            $pdf->line($tx, $midB + 4, $tx, $botY, self::EDGE[0], self::EDGE[1], self::EDGE[2], 0.8);
            self::label($pdf, $tx + 6, $midA - 3, 'T' . ($i + 1), self::TEXT, 4.6);
            self::label($pdf, $tx + 6, $midB - 1, self::num($s, $i, 10, 98) . '%', [94, 233, 181], 4.4);
        }
        return [
            'Load'   => self::num($s, 1, 40, 320) . ' MW',
            'V min'  => (self::num($s, 2, 94, 102) / 100) . ' pu',
            'Status' => 'Converged',
        ];
    }

    private static function generic($pdf, $s, $x, $y, $w, $h)
    {
        $ax = $x + 12; $ay = $y + $h - 6; $aw = $w - 18; $ah = $h - 12;
        $pdf->line($ax, $ay, $ax + $aw, $ay, self::EDGE[0], self::EDGE[1], self::EDGE[2], 0.5);
        $pdf->line($ax, $ay - $ah, $ax, $ay, self::EDGE[0], self::EDGE[1], self::EDGE[2], 0.5);
        $bw = $aw / 12;
        for ($i = 0; $i < 10; $i++) {
            $bh = $ah * (self::num($s, $i, 15, 95) / 100);
            $pdf->fillRect($ax + 4 + $i * ($bw + 2), $ay - $bh, $bw, $bh, 47, 127, 212);
        }
        self::label($pdf, $ax + 2, $y, 'Result profile', self::DIM, 4.8);
        return ['Cases' => 10, 'Status' => 'Complete'];
    }
}
