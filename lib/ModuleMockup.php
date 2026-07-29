<?php
/**
 * EnPharChem - Module Interface Mockup Renderer
 * ---------------------------------------------
 * Produces a self-contained inline SVG depiction of a module's working view,
 * chosen by category archetype and labelled with the module's own name.
 *
 * These are representative interface illustrations, not captured screenshots —
 * documents that embed them should say so. SVG is used so the artwork stays
 * sharp at A4 print resolution and adds no binary assets to the repository.
 *
 * Every figure is deterministic: the numbers come from a hash of the module
 * slug, so a given module always renders identically across regenerations.
 */

class ModuleMockup
{
    const W = 520;
    const H = 300;

    /** Category slug => archetype renderer. */
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

    /** Deterministic pseudo-random integer in [$min,$max] from a seed string. */
    private static function num($seed, $i, $min, $max)
    {
        $h = crc32($seed . '#' . $i);
        return $min + ($h % max(1, ($max - $min + 1)));
    }

    private static function esc($s)
    {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }

    /** Window chrome shared by every archetype. */
    private static function chrome($title, $subtitle)
    {
        $w = self::W;
        $t = self::esc(mb_strimwidth($title, 0, 42, '…'));
        $s = self::esc(mb_strimwidth($subtitle, 0, 34, '…'));
        return <<<SVG
<rect x="0" y="0" width="{$w}" height="300" rx="8" fill="#0f1b2d"/>
<rect x="0" y="0" width="{$w}" height="30" rx="8" fill="#16283f"/>
<rect x="0" y="22" width="{$w}" height="8" fill="#16283f"/>
<circle cx="16" cy="15" r="4.5" fill="#ef4444"/>
<circle cx="31" cy="15" r="4.5" fill="#f59e0b"/>
<circle cx="46" cy="15" r="4.5" fill="#10b981"/>
<text x="62" y="19" fill="#e6eefc" font-family="Segoe UI,sans-serif" font-size="11" font-weight="600">{$t}</text>
<text x="{$w}" y="19" text-anchor="end" dx="-12" fill="#7fa6d9" font-family="Segoe UI,sans-serif" font-size="9">{$s}</text>
SVG;
    }

    /** Small caption strip along the bottom of the figure. */
    private static function readout($pairs)
    {
        $out = '';
        $x = 16;
        foreach ($pairs as $label => $value) {
            $out .= '<text x="' . $x . '" y="288" fill="#5c7fa8" font-family="Segoe UI,sans-serif" font-size="8.5">'
                  . self::esc($label) . '</text>'
                  . '<text x="' . ($x + 2) . '" y="277" fill="#7ee0ff" font-family="Consolas,monospace" font-size="10" font-weight="600">'
                  . self::esc($value) . '</text>';
            $x += 108;
        }
        return $out;
    }

    /**
     * Render the mockup for a module.
     * @return string inline <svg> markup
     */
    public static function render($module, $categorySlug, $categoryName)
    {
        $slug   = $module['slug'] ?? 'module';
        $name   = $module['name'] ?? 'Module';
        $method = self::$map[$categorySlug] ?? 'generic';
        $body   = self::$method($slug);

        $w = self::W; $h = self::H;
        return '<svg viewBox="0 0 ' . $w . ' ' . $h . '" width="100%" preserveAspectRatio="xMidYMid meet" '
             . 'role="img" aria-label="' . self::esc($name . ' interface illustration') . '" '
             . 'xmlns="http://www.w3.org/2000/svg">'
             . self::chrome($name, $categoryName)
             . $body
             . '</svg>';
    }

    // ---------------------------------------------------------------- archetypes

    /** Process flowsheet: feed -> unit -> separator with product streams. */
    private static function flowsheet($s)
    {
        $t = self::num($s, 1, 28, 92);
        $p = self::num($s, 2, 180, 980);
        $q = self::num($s, 3, 4, 38) / 10;

        // streams
        $g  = '<path d="M40 120 H108" stroke="#3d6ea8" stroke-width="2" marker-end="url(#ar)"/>';
        $g .= '<path d="M172 120 H240" stroke="#3d6ea8" stroke-width="2" marker-end="url(#ar)"/>';
        $g .= '<path d="M304 120 H352" stroke="#3d6ea8" stroke-width="2" marker-end="url(#ar)"/>';
        $g .= '<path d="M400 104 V70 H468" stroke="#2fb2ff" stroke-width="2" marker-end="url(#ar)"/>';
        $g .= '<path d="M400 152 V196 H468" stroke="#f5a742" stroke-width="2" marker-end="url(#ar)"/>';
        // unit operations
        $g .= '<rect x="108" y="98" width="64" height="44" rx="4" fill="#1e3350" stroke="#3d6ea8"/>';
        $g .= '<rect x="240" y="98" width="64" height="44" rx="4" fill="#1e3350" stroke="#3d6ea8"/>';
        $g .= '<ellipse cx="376" cy="128" rx="26" ry="34" fill="#1e3350" stroke="#3d6ea8"/>';
        // labels
        $g .= '<text x="24" y="116" fill="#9fc4ea" font-family="Segoe UI,sans-serif" font-size="9">FEED</text>';
        $g .= '<text x="140" y="124" text-anchor="middle" fill="#cfe2f7" font-family="Segoe UI,sans-serif" font-size="10">E-101</text>';
        $g .= '<text x="272" y="124" text-anchor="middle" fill="#cfe2f7" font-family="Segoe UI,sans-serif" font-size="10">R-101</text>';
        $g .= '<text x="376" y="132" text-anchor="middle" fill="#cfe2f7" font-family="Segoe UI,sans-serif" font-size="10">V-101</text>';
        $g .= '<text x="472" y="66" fill="#7ee0ff" font-family="Segoe UI,sans-serif" font-size="9">VAPOUR</text>';
        $g .= '<text x="472" y="200" fill="#ffc978" font-family="Segoe UI,sans-serif" font-size="9">LIQUID</text>';
        $g .= '<defs><marker id="ar" markerWidth="7" markerHeight="7" refX="6" refY="3.5" orient="auto">'
            . '<path d="M0 0 L7 3.5 L0 7 z" fill="#3d6ea8"/></marker></defs>';
        $g .= self::readout(['Temperature' => $t . ' °C', 'Pressure' => $p . ' kPa', 'Duty' => $q . ' MW', 'Status' => 'Converged']);
        return $g;
    }

    /** Shell-and-tube exchanger with duty readout. */
    private static function exchanger($s)
    {
        $duty = self::num($s, 1, 240, 3800);
        $lmtd = self::num($s, 2, 12, 68);
        $area = self::num($s, 3, 40, 620);
        $g  = '<rect x="90" y="86" width="330" height="86" rx="42" fill="#1e3350" stroke="#3d6ea8" stroke-width="2"/>';
        for ($i = 0; $i < 5; $i++) {
            $y = 100 + $i * 16;
            $g .= '<path d="M104 ' . $y . ' H406" stroke="#2fb2ff" stroke-width="1.6" opacity="0.75"/>';
        }
        $g .= '<path d="M40 129 H90" stroke="#ef6060" stroke-width="2.5" marker-end="url(#ar2)"/>';
        $g .= '<path d="M420 129 H480" stroke="#4aa3ff" stroke-width="2.5" marker-end="url(#ar2)"/>';
        $g .= '<path d="M150 60 V86" stroke="#4aa3ff" stroke-width="2.5" marker-end="url(#ar2)"/>';
        $g .= '<path d="M360 172 V200" stroke="#ef6060" stroke-width="2.5" marker-end="url(#ar2)"/>';
        $g .= '<text x="34" y="120" fill="#ff9c9c" font-family="Segoe UI,sans-serif" font-size="9">HOT IN</text>';
        $g .= '<text x="424" y="120" fill="#9fd0ff" font-family="Segoe UI,sans-serif" font-size="9">HOT OUT</text>';
        $g .= '<text x="122" y="56" fill="#9fd0ff" font-family="Segoe UI,sans-serif" font-size="9">COLD IN</text>';
        $g .= '<text x="332" y="216" fill="#ff9c9c" font-family="Segoe UI,sans-serif" font-size="9">COLD OUT</text>';
        $g .= '<defs><marker id="ar2" markerWidth="7" markerHeight="7" refX="6" refY="3.5" orient="auto">'
            . '<path d="M0 0 L7 3.5 L0 7 z" fill="#7fa6d9"/></marker></defs>';
        $g .= self::readout(['Duty' => $duty . ' kW', 'LMTD' => $lmtd . ' K', 'Area' => $area . ' m²', 'Passes' => '1-2']);
        return $g;
    }

    /** Schedule bars for FEED / supply-chain planning. */
    private static function gantt($s)
    {
        $rows = ['Scope definition', 'Equipment list', 'Cost estimate', 'Schedule', 'Review'];
        $g = '';
        foreach ($rows as $i => $label) {
            $y = 54 + $i * 34;
            $x = 150 + self::num($s, $i, 0, 60);
            $w = 90 + self::num($s, $i + 10, 20, 190);
            if ($x + $w > 494) { $w = 494 - $x; }
            $g .= '<text x="16" y="' . ($y + 12) . '" fill="#9fc4ea" font-family="Segoe UI,sans-serif" font-size="9.5">'
                . self::esc($label) . '</text>';
            $g .= '<rect x="140" y="' . $y . '" width="360" height="18" rx="3" fill="#16283f"/>';
            $g .= '<rect x="' . $x . '" y="' . $y . '" width="' . $w . '" height="18" rx="3" fill="#2f7fd4" opacity="'
                . (0.55 + $i * 0.09) . '"/>';
        }
        for ($i = 0; $i <= 4; $i++) {
            $x = 140 + $i * 90;
            $g .= '<path d="M' . $x . ' 46 V226" stroke="#24405f" stroke-width="1"/>';
            $g .= '<text x="' . $x . '" y="240" fill="#5c7fa8" font-family="Segoe UI,sans-serif" font-size="8">W' . ($i * 4) . '</text>';
        }
        $g .= self::readout(['Activities' => count($rows), 'Duration' => self::num($s, 5, 12, 40) . ' wks', 'Status' => 'On track']);
        return $g;
    }

    /** Subsurface strata with a well track. */
    private static function strata($s)
    {
        $cols = ['#2b4a6b', '#35608a', '#c08b4a', '#8a6a3d', '#4a6b8a'];
        $g = '';
        $y = 46;
        foreach ($cols as $i => $c) {
            $h = 26 + self::num($s, $i, 0, 22);
            $g .= '<rect x="16" y="' . $y . '" width="360" height="' . $h . '" fill="' . $c . '" opacity="0.85"/>';
            $y += $h;
            if ($y > 210) { break; }
        }
        $g .= '<path d="M150 40 V' . min($y, 224) . '" stroke="#f5f7fa" stroke-width="3"/>';
        $g .= '<circle cx="150" cy="40" r="5" fill="#7ee0ff"/>';
        $g .= '<text x="160" y="38" fill="#cfe2f7" font-family="Segoe UI,sans-serif" font-size="9">WELL-01</text>';
        // log curve
        $path = 'M400 46';
        for ($i = 1; $i <= 18; $i++) {
            $path .= ' L' . (400 + self::num($s, $i + 20, 0, 60)) . ' ' . (46 + $i * 10);
        }
        $g .= '<path d="' . $path . '" stroke="#7ee0ff" stroke-width="1.6" fill="none"/>';
        $g .= '<text x="400" y="240" fill="#5c7fa8" font-family="Segoe UI,sans-serif" font-size="8">GR log</text>';
        $g .= self::readout(['Net pay' => self::num($s, 1, 8, 60) . ' m', 'Porosity' => (self::num($s, 2, 80, 280) / 10) . ' %', 'Sw' => (self::num($s, 3, 150, 480) / 10) . ' %']);
        return $g;
    }

    /** Pinch composite curves. */
    private static function composite($s)
    {
        $g = '<path d="M50 220 H500" stroke="#3d6ea8" stroke-width="1"/><path d="M50 40 V220" stroke="#3d6ea8" stroke-width="1"/>';
        $hot = 'M70 200'; $cold = 'M110 210';
        for ($i = 1; $i <= 8; $i++) {
            $hot  .= ' L' . (70 + $i * 46) . ' ' . (200 - $i * 17 - self::num($s, $i, 0, 12));
            $cold .= ' L' . (110 + $i * 46) . ' ' . (210 - $i * 15 - self::num($s, $i + 8, 0, 10));
        }
        $g .= '<path d="' . $hot . '" stroke="#ef6060" stroke-width="2.4" fill="none"/>';
        $g .= '<path d="' . $cold . '" stroke="#4aa3ff" stroke-width="2.4" fill="none"/>';
        $g .= '<circle cx="248" cy="120" r="5" fill="none" stroke="#ffd166" stroke-width="2"/>';
        $g .= '<text x="258" y="116" fill="#ffd166" font-family="Segoe UI,sans-serif" font-size="9">PINCH</text>';
        $g .= '<text x="60" y="52" fill="#ef6060" font-family="Segoe UI,sans-serif" font-size="9">Hot composite</text>';
        $g .= '<text x="60" y="66" fill="#4aa3ff" font-family="Segoe UI,sans-serif" font-size="9">Cold composite</text>';
        $g .= '<text x="470" y="236" fill="#5c7fa8" font-family="Segoe UI,sans-serif" font-size="8">Enthalpy</text>';
        $g .= self::readout(['ΔTmin' => self::num($s, 1, 5, 25) . ' K', 'Hot utility' => self::num($s, 2, 200, 2400) . ' kW', 'Cold utility' => self::num($s, 3, 150, 2000) . ' kW']);
        return $g;
    }

    /** KPI tiles plus an alarm list. */
    private static function kpi($s)
    {
        $tiles = ['Availability' => '%', 'Throughput' => ' t/h', 'Energy' => ' GJ/t', 'On-spec' => '%'];
        $g = ''; $i = 0;
        foreach ($tiles as $label => $unit) {
            $x = 16 + ($i % 4) * 126;
            $v = self::num($s, $i, 62, 99);
            $g .= '<rect x="' . $x . '" y="46" width="114" height="58" rx="6" fill="#16283f" stroke="#24405f"/>';
            $g .= '<text x="' . ($x + 10) . '" y="66" fill="#7fa6d9" font-family="Segoe UI,sans-serif" font-size="8.5">' . self::esc($label) . '</text>';
            $g .= '<text x="' . ($x + 10) . '" y="92" fill="#7ee0ff" font-family="Segoe UI,sans-serif" font-size="20" font-weight="700">'
                . $v . '<tspan font-size="10">' . self::esc($unit) . '</tspan></text>';
            $i++;
        }
        $rows = ['TI-2041 high', 'PC-1180 deviation', 'FI-3302 low flow'];
        foreach ($rows as $j => $r) {
            $y = 124 + $j * 30;
            $g .= '<rect x="16" y="' . $y . '" width="488" height="24" rx="4" fill="#16283f"/>';
            $g .= '<circle cx="30" cy="' . ($y + 12) . '" r="4" fill="' . ($j === 0 ? '#ef4444' : ($j === 1 ? '#f59e0b' : '#10b981')) . '"/>';
            $g .= '<text x="44" y="' . ($y + 16) . '" fill="#cfe2f7" font-family="Segoe UI,sans-serif" font-size="9.5">' . self::esc($r) . '</text>';
            $g .= '<text x="492" y="' . ($y + 16) . '" text-anchor="end" fill="#5c7fa8" font-family="Consolas,monospace" font-size="9">'
                . self::num($s, $j, 1, 59) . ' min</text>';
        }
        $g .= self::readout(['Active alarms' => count($rows), 'Shift' => 'A', 'Status' => 'Monitoring']);
        return $g;
    }

    /** SP / PV / OP controller trend. */
    private static function trend($s)
    {
        $g = '<path d="M50 220 H500" stroke="#3d6ea8" stroke-width="1"/><path d="M50 40 V220" stroke="#3d6ea8" stroke-width="1"/>';
        $g .= '<path d="M50 118 H500" stroke="#ffd166" stroke-width="1.4" stroke-dasharray="5 4"/>';
        $pv = 'M50 170';
        for ($i = 1; $i <= 22; $i++) {
            $target = 118 + (($i > 8) ? 0 : 40);
            $pv .= ' L' . (50 + $i * 20) . ' ' . ($target + self::num($s, $i, -9, 9));
        }
        $g .= '<path d="' . $pv . '" stroke="#4aa3ff" stroke-width="2" fill="none"/>';
        $op = 'M50 200';
        for ($i = 1; $i <= 22; $i++) {
            $op .= ' L' . (50 + $i * 20) . ' ' . (196 - self::num($s, $i + 30, 0, 34));
        }
        $g .= '<path d="' . $op . '" stroke="#10b981" stroke-width="1.6" fill="none" opacity="0.85"/>';
        $g .= '<text x="58" y="52" fill="#ffd166" font-family="Segoe UI,sans-serif" font-size="9">SP</text>';
        $g .= '<text x="88" y="52" fill="#4aa3ff" font-family="Segoe UI,sans-serif" font-size="9">PV</text>';
        $g .= '<text x="118" y="52" fill="#10b981" font-family="Segoe UI,sans-serif" font-size="9">OP</text>';
        $g .= self::readout(['Service factor' => self::num($s, 1, 88, 99) . ' %', 'Mode' => 'Auto', 'Constraint' => 'Inactive']);
        return $g;
    }

    /** Optimiser convergence history. */
    private static function convergence($s)
    {
        $g = '<path d="M50 220 H500" stroke="#3d6ea8" stroke-width="1"/><path d="M50 40 V220" stroke="#3d6ea8" stroke-width="1"/>';
        $p = 'M56 60'; $y = 60;
        for ($i = 1; $i <= 20; $i++) {
            $y = 60 + (160 * (1 - exp(-$i / 4.5))) - self::num($s, $i, 0, 6);
            $p .= ' L' . (56 + $i * 22) . ' ' . round($y, 1);
        }
        $g .= '<path d="' . $p . '" stroke="#7ee0ff" stroke-width="2.2" fill="none"/>';
        for ($i = 1; $i <= 20; $i += 4) {
            $yy = 60 + (160 * (1 - exp(-$i / 4.5)));
            $g .= '<circle cx="' . (56 + $i * 22) . '" cy="' . round($yy, 1) . '" r="3" fill="#7ee0ff"/>';
        }
        $g .= '<text x="58" y="52" fill="#9fc4ea" font-family="Segoe UI,sans-serif" font-size="9">Objective value</text>';
        $g .= '<text x="470" y="238" fill="#5c7fa8" font-family="Segoe UI,sans-serif" font-size="8">Iteration</text>';
        $g .= self::readout(['Iterations' => self::num($s, 1, 12, 80), 'Objective' => self::num($s, 2, 100, 9000), 'Status' => 'Optimal']);
        return $g;
    }

    /** Batch execution grid. */
    private static function batchgrid($s)
    {
        $g = '<rect x="16" y="44" width="488" height="20" rx="3" fill="#1e3350"/>';
        foreach (['Batch', 'Product', 'Qty', 'Yield', 'Status'] as $i => $h) {
            $g .= '<text x="' . (26 + $i * 98) . '" y="58" fill="#9fc4ea" font-family="Segoe UI,sans-serif" font-size="9" font-weight="600">'
                . self::esc($h) . '</text>';
        }
        for ($r = 0; $r < 5; $r++) {
            $y = 68 + $r * 30;
            $g .= '<rect x="16" y="' . $y . '" width="488" height="26" rx="3" fill="' . ($r % 2 ? '#16283f' : '#132437') . '"/>';
            $cells = [
                'B-' . (2400 + self::num($s, $r, 1, 99)),
                'PRD-' . self::num($s, $r + 5, 100, 999),
                self::num($s, $r + 10, 20, 400) . ' kg',
                self::num($s, $r + 15, 88, 99) . ' %',
            ];
            foreach ($cells as $i => $c) {
                $g .= '<text x="' . (26 + $i * 98) . '" y="' . ($y + 17) . '" fill="#cfe2f7" font-family="Consolas,monospace" font-size="9">'
                    . self::esc($c) . '</text>';
            }
            $ok = $r % 3 !== 1;
            $g .= '<rect x="' . (26 + 4 * 98) . '" y="' . ($y + 6) . '" width="56" height="15" rx="7" fill="' . ($ok ? '#10b98133' : '#f59e0b33') . '"/>';
            $g .= '<text x="' . (30 + 4 * 98) . '" y="' . ($y + 17) . '" fill="' . ($ok ? '#5ee9b5' : '#ffc978') . '" font-family="Segoe UI,sans-serif" font-size="8">'
                . ($ok ? 'Complete' : 'Running') . '</text>';
        }
        $g .= self::readout(['Batches' => 5, 'Avg yield' => self::num($s, 2, 90, 98) . ' %', 'OEE' => self::num($s, 3, 70, 92) . ' %']);
        return $g;
    }

    /** Supply chain node network. */
    private static function network($s)
    {
        $nodes = [[70, 90, 'FIELD'], [200, 60, 'TERMINAL'], [200, 150, 'REFINERY'], [350, 100, 'DEPOT'], [460, 160, 'MARKET']];
        $g = '';
        $links = [[0, 1], [0, 2], [1, 3], [2, 3], [3, 4], [2, 4]];
        foreach ($links as $k => $l) {
            $a = $nodes[$l[0]]; $b = $nodes[$l[1]];
            $g .= '<path d="M' . $a[0] . ' ' . $a[1] . ' L' . $b[0] . ' ' . $b[1] . '" stroke="#2f7fd4" stroke-width="'
                . (1 + self::num($s, $k, 0, 3)) . '" opacity="0.7"/>';
        }
        foreach ($nodes as $i => $n) {
            $g .= '<circle cx="' . $n[0] . '" cy="' . $n[1] . '" r="17" fill="#1e3350" stroke="#4aa3ff" stroke-width="2"/>';
            $g .= '<text x="' . $n[0] . '" y="' . ($n[1] + 33) . '" text-anchor="middle" fill="#9fc4ea" font-family="Segoe UI,sans-serif" font-size="8.5">'
                . self::esc($n[2]) . '</text>';
            $g .= '<text x="' . $n[0] . '" y="' . ($n[1] + 4) . '" text-anchor="middle" fill="#7ee0ff" font-family="Consolas,monospace" font-size="9">'
                . self::num($s, $i + 3, 10, 99) . '</text>';
        }
        $g .= self::readout(['Nodes' => count($nodes), 'Throughput' => self::num($s, 1, 20, 180) . ' kbd', 'Margin' => '$' . self::num($s, 2, 2, 19) . '/bbl']);
        return $g;
    }

    /** Asset health gauges. */
    private static function gauges($s)
    {
        $labels = ['Pump P-101', 'Compressor K-201', 'Exchanger E-301'];
        $g = '';
        foreach ($labels as $i => $lab) {
            $cx = 100 + $i * 160; $cy = 130;
            $v  = self::num($s, $i, 42, 98);
            $ang = M_PI * (1 - $v / 100);
            $x2 = $cx + cos($ang) * 46; $y2 = $cy - sin($ang) * 46;
            $col = $v > 80 ? '#10b981' : ($v > 60 ? '#f59e0b' : '#ef4444');
            $g .= '<path d="M' . ($cx - 52) . ' ' . $cy . ' A52 52 0 0 1 ' . ($cx + 52) . ' ' . $cy . '" fill="none" stroke="#24405f" stroke-width="10"/>';
            $g .= '<path d="M' . ($cx - 46) . ' ' . $cy . ' A46 46 0 0 1 ' . round($x2, 1) . ' ' . round($y2, 1) . '" fill="none" stroke="' . $col . '" stroke-width="8"/>';
            $g .= '<text x="' . $cx . '" y="' . ($cy - 6) . '" text-anchor="middle" fill="' . $col . '" font-family="Segoe UI,sans-serif" font-size="19" font-weight="700">' . $v . '</text>';
            $g .= '<text x="' . $cx . '" y="' . ($cy + 26) . '" text-anchor="middle" fill="#9fc4ea" font-family="Segoe UI,sans-serif" font-size="9">' . self::esc($lab) . '</text>';
        }
        $g .= self::readout(['Assets' => 3, 'At risk' => 1, 'MTBF' => self::num($s, 4, 300, 2400) . ' h']);
        return $g;
    }

    /** Data pipeline stages. */
    private static function pipeline($s)
    {
        $stages = ['SOURCE', 'MAP', 'TRANSFORM', 'QUALITY', 'SERVE'];
        $g = '';
        foreach ($stages as $i => $st) {
            $x = 20 + $i * 98;
            $g .= '<rect x="' . $x . '" y="100" width="80" height="52" rx="6" fill="#1e3350" stroke="#3d6ea8"/>';
            $g .= '<text x="' . ($x + 40) . '" y="122" text-anchor="middle" fill="#cfe2f7" font-family="Segoe UI,sans-serif" font-size="9" font-weight="600">'
                . self::esc($st) . '</text>';
            $g .= '<text x="' . ($x + 40) . '" y="140" text-anchor="middle" fill="#7ee0ff" font-family="Consolas,monospace" font-size="9">'
                . self::num($s, $i, 100, 9999) . '</text>';
            if ($i < count($stages) - 1) {
                $g .= '<path d="M' . ($x + 80) . ' 126 H' . ($x + 98) . '" stroke="#2f7fd4" stroke-width="2" marker-end="url(#ar3)"/>';
            }
        }
        $g .= '<defs><marker id="ar3" markerWidth="7" markerHeight="7" refX="6" refY="3.5" orient="auto">'
            . '<path d="M0 0 L7 3.5 L0 7 z" fill="#2f7fd4"/></marker></defs>';
        $g .= '<text x="20" y="80" fill="#9fc4ea" font-family="Segoe UI,sans-serif" font-size="9">Tags mapped to asset model</text>';
        $g .= self::readout(['Tags' => self::num($s, 1, 400, 9000), 'Quality' => self::num($s, 2, 95, 100) . ' %', 'Latency' => self::num($s, 3, 1, 9) . ' s']);
        return $g;
    }

    /** Grid one-line diagram. */
    private static function oneline($s)
    {
        $g = '<path d="M40 70 H480" stroke="#4aa3ff" stroke-width="3"/>';
        $g .= '<path d="M40 190 H480" stroke="#4aa3ff" stroke-width="3"/>';
        $g .= '<text x="40" y="60" fill="#9fc4ea" font-family="Segoe UI,sans-serif" font-size="9">BUS 132 kV</text>';
        $g .= '<text x="40" y="212" fill="#9fc4ea" font-family="Segoe UI,sans-serif" font-size="9">BUS 33 kV</text>';
        for ($i = 0; $i < 4; $i++) {
            $x = 100 + $i * 105;
            $g .= '<path d="M' . $x . ' 70 V110" stroke="#3d6ea8" stroke-width="2"/>';
            $g .= '<circle cx="' . $x . '" cy="122" r="11" fill="none" stroke="#7ee0ff" stroke-width="2"/>';
            $g .= '<circle cx="' . $x . '" cy="134" r="11" fill="none" stroke="#7ee0ff" stroke-width="2"/>';
            $g .= '<path d="M' . $x . ' 146 V190" stroke="#3d6ea8" stroke-width="2"/>';
            $g .= '<text x="' . ($x + 16) . '" y="130" fill="#cfe2f7" font-family="Segoe UI,sans-serif" font-size="8">T' . ($i + 1) . '</text>';
            $g .= '<text x="' . ($x + 16) . '" y="142" fill="#5ee9b5" font-family="Consolas,monospace" font-size="8">'
                . self::num($s, $i, 10, 98) . '%</text>';
        }
        $g .= self::readout(['Load' => self::num($s, 1, 40, 320) . ' MW', 'V min' => (self::num($s, 2, 94, 102) / 100) . ' pu', 'Status' => 'Converged']);
        return $g;
    }

    /** Fallback: generic result chart. */
    private static function generic($s)
    {
        $g = '<path d="M50 220 H500" stroke="#3d6ea8" stroke-width="1"/><path d="M50 40 V220" stroke="#3d6ea8" stroke-width="1"/>';
        for ($i = 0; $i < 10; $i++) {
            $h = 20 + self::num($s, $i, 10, 150);
            $g .= '<rect x="' . (66 + $i * 43) . '" y="' . (220 - $h) . '" width="26" height="' . $h . '" rx="3" fill="#2f7fd4" opacity="'
                . (0.5 + $i * 0.045) . '"/>';
        }
        $g .= '<text x="58" y="52" fill="#9fc4ea" font-family="Segoe UI,sans-serif" font-size="9">Result profile</text>';
        $g .= self::readout(['Cases' => 10, 'Status' => 'Complete']);
        return $g;
    }
}
