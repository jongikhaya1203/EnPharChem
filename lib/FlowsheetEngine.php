<?php
/**
 * EnPharChem - Flowsheet Process Simulator Engine
 * ------------------------------------------------
 * A genuine (if simplified) steady-state, sequential-modular process
 * simulator. This is the EnPharChem analog of a chemical process
 * simulator's compute core: it performs real mass and energy balances,
 * ideal VLE flash (Raoult's law with a Clausius-Clapeyron vapour-pressure
 * model), conversion reactors and shortcut distillation splits.
 *
 * It is NOT a copy of any third-party product; it is an independent,
 * first-principles implementation used to make EnPharChem a working
 * process-simulation platform.
 *
 * Units used internally:
 *   flow      kmol/h (molar), kg/h (mass, derived)
 *   T         K
 *   P         kPa
 *   enthalpy  kJ/h        (reference: liquid at TREF = 298.15 K)
 *   duty/work kW
 */

class FlowsheetEngine
{
    const R    = 8.314;      // kJ/kmol.K
    const TREF = 298.15;     // K, enthalpy reference
    const PATM = 101.325;    // kPa

    /** @var array component thermophysical database */
    private $db;

    /** @var string active property package: 'peng-robinson' | 'ideal' */
    private $model = 'peng-robinson';
    /** @var PengRobinson|null */
    private $pr = null;

    public function __construct($model = 'peng-robinson')
    {
        // name => [formula, MW g/mol, Tb K, Hvap kJ/kmol, Cp_liq, Cp_gas,  Tc K, Pc bar, omega]
        $this->db = [
            'Hydrogen' => ['H2',   2.02,  20.4,   900,   20.0, 28.8,   33.2,  13.0, -0.216],
            'Nitrogen' => ['N2',  28.01,  77.4,  5570,   56.0, 29.1,  126.2,  33.9,  0.037],
            'Oxygen'   => ['O2',  32.00,  90.2,  6820,   54.0, 29.4,  154.6,  50.4,  0.022],
            'Methane'  => ['CH4', 16.04, 111.7,  8170,   55.0, 35.7,  190.6,  46.0,  0.011],
            'CO2'      => ['CO2', 44.01, 194.7, 17170,   37.0, 37.0,  304.2,  73.8,  0.224],
            'Ammonia'  => ['NH3', 17.03, 239.8, 23350,   80.0, 35.6,  405.5, 113.5,  0.250],
            'Ethane'   => ['C2H6',30.07, 184.6, 14700,   68.0, 52.5,  305.3,  48.7,  0.099],
            'Propane'  => ['C3H8',44.10, 231.1, 19040,   98.0, 73.5,  369.8,  42.5,  0.152],
            'i-Butane' => ['C4H10',58.12,261.4, 21300,  129.0, 96.0,  408.1,  36.5,  0.176],
            'n-Butane' => ['C4H10',58.12,272.7, 22440,  130.0, 98.0,  425.1,  38.0,  0.200],
            'n-Pentane'=> ['C5H12',72.15,309.2, 25790,  167.0,120.0,  469.7,  33.7,  0.251],
            'Methanol' => ['CH4O',32.04, 337.7, 35210,   81.0, 44.0,  512.6,  80.9,  0.565],
            'Ethanol'  => ['C2H6O',46.07,351.4, 38560,  112.0, 65.0,  513.9,  61.4,  0.644],
            'Benzene'  => ['C6H6',78.11, 353.2, 30720,  136.0, 82.0,  562.2,  48.9,  0.210],
            'Water'    => ['H2O', 18.02, 373.2, 40650,   75.3, 33.6,  647.1, 220.6,  0.345],
            'Toluene'  => ['C7H8',92.14, 383.8, 33180,  157.0,104.0,  591.8,  41.1,  0.264],
        ];

        $this->model = ($model === 'ideal') ? 'ideal' : 'peng-robinson';
        if ($this->model === 'peng-robinson') {
            require_once __DIR__ . '/PengRobinson.php';
            $crit = [];
            foreach ($this->db as $n => $p) $crit[$n] = [$p[6], $p[7], $p[8]]; // Tc, Pc(bar), omega
            $this->pr = new PengRobinson($crit);
        }
    }

    public function model() { return $this->model; }

    public function components()
    {
        $out = [];
        foreach ($this->db as $name => $p) {
            $out[] = ['name' => $name, 'formula' => $p[0], 'mw' => $p[1], 'tb' => $p[2]];
        }
        return $out;
    }

    // ---- component property helpers -------------------------------------

    private function prop($name)
    {
        return $this->db[$name] ?? ['?', 50.0, 300.0, 25000, 120.0, 80.0, 500.0, 40.0, 0.2];
    }
    private function mw($n)     { return $this->prop($n)[1]; }
    private function tb($n)     { return $this->prop($n)[2]; }
    private function latentHeat($n)   { return $this->prop($n)[3]; }
    private function cpLiq($n)  { return $this->prop($n)[4]; }
    private function cpGas($n)  { return $this->prop($n)[5]; }

    /** Saturation pressure [kPa] via Clausius-Clapeyron anchored at normal Tb. */
    private function psat($n, $t)
    {
        $tb = $this->tb($n);
        $hv = $this->latentHeat($n);
        return self::PATM * exp(($hv / self::R) * (1.0 / $tb - 1.0 / max($t, 1.0)));
    }

    private function hLiq($n, $t) { return $this->cpLiq($n) * ($t - self::TREF); }
    private function hVap($n, $t)
    {
        $tb = $this->tb($n);
        return $this->cpLiq($n) * ($tb - self::TREF) + $this->latentHeat($n) + $this->cpGas($n) * ($t - $tb);
    }

    // ---- stream mechanics ------------------------------------------------

    /**
     * Build a stream from component molar flows and flash it at (T,P).
     * Returns a normalised stream array with phase split + enthalpy.
     */
    private function makeStream($comp, $t, $p)
    {
        $comp = array_filter($comp, fn($v) => $v > 1e-12);
        $F = array_sum($comp);
        $stream = [
            'comp' => $comp, 'T' => $t, 'P' => $p,
            'F' => $F, 'V' => 0.0, 'L' => $F,
            'y' => [], 'x' => [], 'vfrac' => 0.0,
            'nV' => [], 'nL' => [],
        ];
        if ($F <= 1e-12) {
            $stream['phase'] = 'empty';
            $stream['H'] = 0.0; $stream['mass'] = 0.0;
            return $stream;
        }
        $this->flashInto($stream, $t, $p);
        $stream['mass'] = $this->massFlow($comp);
        return $stream;
    }

    private function massFlow($comp)
    {
        $m = 0.0;
        foreach ($comp as $n => $f) $m += $f * $this->mw($n);
        return $m; // kg/h
    }

    /** Isothermal Rachford-Rice flash; mutates stream with V/L/x/y/H. */
    private function flashInto(&$stream, $t, $p)
    {
        $comp = $stream['comp'];
        $F = array_sum($comp);
        $z = [];
        foreach ($comp as $n => $f) $z[$n] = $f / $F;

        $beta = null; $x = []; $y = []; $used = 'ideal';

        // --- rigorous VLE via Peng-Robinson (K from fugacity coefficients) ---
        if ($this->model === 'peng-robinson' && $this->pr && $this->pr->hasAll(array_keys($z))) {
            $res = $this->pr->flash($z, $t, $p);
            if ($res !== null) {
                $beta = $res['beta']; $x = $res['x']; $y = $res['y']; $used = 'peng-robinson';
            }
        }

        // --- ideal Raoult fallback (Clausius-Clapeyron K) --------------------
        if ($beta === null) {
            $K = [];
            foreach ($z as $n => $zi) $K[$n] = $this->psat($n, $t) / $p;
            $sumZK = 0.0; $sumZoverK = 0.0;
            foreach ($z as $n => $zi) { $sumZK += $zi * $K[$n]; $sumZoverK += $zi / $K[$n]; }
            if ($sumZK <= 1.0)          $beta = 0.0;
            elseif ($sumZoverK <= 1.0)  $beta = 1.0;
            else                        $beta = $this->rachfordRice($z, $K);
            foreach ($z as $n => $zi) {
                $den = 1.0 + $beta * ($K[$n] - 1.0);
                $x[$n] = $zi / $den;
                $y[$n] = $K[$n] * $x[$n];
            }
        }

        $nV = []; $nL = [];
        $V = $beta * $F; $L = (1.0 - $beta) * $F;
        foreach ($z as $n => $zi) {
            $nV[$n] = $V * ($y[$n] ?? 0.0);
            $nL[$n] = $L * ($x[$n] ?? 0.0);
        }
        $stream['V'] = $V; $stream['L'] = $L; $stream['vfrac'] = $beta;
        $stream['x'] = $x; $stream['y'] = $y; $stream['model'] = $used;
        $stream['nV'] = $nV; $stream['nL'] = $nL;
        $stream['T'] = $t; $stream['P'] = $p;
        $stream['phase'] = $beta <= 1e-6 ? 'liquid' : ($beta >= 1 - 1e-6 ? 'vapor' : 'two-phase');
        $stream['H'] = $this->streamEnthalpy($nL, $nV, $x, $y, $t, $p);
    }

    /**
     * Stream enthalpy [kJ/h]. In Peng-Robinson mode this is the ideal-gas
     * enthalpy plus the PR residual (departure) enthalpy of each phase, so
     * latent heat emerges from the EOS. In ideal mode it uses the
     * Cp/latent-heat correlation. A single run uses one reference throughout.
     */
    private function streamEnthalpy($nL, $nV, $x, $y, $t, $p)
    {
        if ($this->model === 'peng-robinson' && $this->pr) {
            $Hig = 0.0;
            foreach ($nL as $n => $f) $Hig += $f * $this->cpGas($n) * ($t - self::TREF);
            foreach ($nV as $n => $f) $Hig += $f * $this->cpGas($n) * ($t - self::TREF);
            $L = array_sum($nL); $V = array_sum($nV);
            $Hdep = 0.0;
            if ($V > 1e-12) {
                $d = $this->pr->departureH($this->fractions($nV), $t, $p, 'vapor');
                if ($d !== null) $Hdep += $V * $d; // kJ/kmol * kmol/h
            }
            if ($L > 1e-12) {
                $d = $this->pr->departureH($this->fractions($nL), $t, $p, 'liquid');
                if ($d !== null) $Hdep += $L * $d;
            }
            return $Hig + $Hdep;
        }
        return $this->enthalpy($nL, $nV, $t);
    }

    private function fractions($flows)
    {
        $s = array_sum($flows);
        if ($s <= 0) return $flows;
        $out = [];
        foreach ($flows as $n => $f) $out[$n] = $f / $s;
        return $out;
    }

    private function rachfordRice($z, $K)
    {
        $f = function ($beta) use ($z, $K) {
            $s = 0.0;
            foreach ($z as $n => $zi) {
                $s += $zi * ($K[$n] - 1.0) / (1.0 + $beta * ($K[$n] - 1.0));
            }
            return $s;
        };
        $lo = 1e-9; $hi = 1.0 - 1e-9;
        $flo = $f($lo); $fhi = $f($hi);
        if ($flo * $fhi > 0) return $flo > 0 ? 1.0 : 0.0;
        for ($i = 0; $i < 80; $i++) {
            $mid = 0.5 * ($lo + $hi);
            $fm = $f($mid);
            if (abs($fm) < 1e-10) return $mid;
            if ($flo * $fm < 0) { $hi = $mid; $fhi = $fm; }
            else { $lo = $mid; $flo = $fm; }
        }
        return 0.5 * ($lo + $hi);
    }

    private function enthalpy($nL, $nV, $t)
    {
        $h = 0.0;
        foreach ($nL as $n => $f) $h += $f * $this->hLiq($n, $t);
        foreach ($nV as $n => $f) $h += $f * $this->hVap($n, $t);
        return $h; // kJ/h
    }

    /** Total enthalpy of a raw composition flashed at (T,P). */
    private function enthalpyOf($comp, $t, $p)
    {
        $s = $this->makeStream($comp, $t, $p);
        return $s['H'];
    }

    /** Solve outlet T for a target enthalpy (adiabatic/duty specs). */
    private function solveT($comp, $p, $Htarget, $tLo = 100.0, $tHi = 1200.0)
    {
        $f = fn($t) => $this->enthalpyOf($comp, $t, $p) - $Htarget;
        $flo = $f($tLo); $fhi = $f($tHi);
        if ($flo * $fhi > 0) return $flo > 0 ? $tLo : $tHi;
        for ($i = 0; $i < 100; $i++) {
            $mid = 0.5 * ($tLo + $tHi);
            $fm = $f($mid);
            if (abs($fm) < 1e-3) return $mid;
            if ($flo * $fm < 0) { $tHi = $mid; }
            else { $tLo = $mid; $flo = $fm; }
        }
        return 0.5 * ($tLo + $tHi);
    }

    // ---- graph solve -----------------------------------------------------

    /**
     * Run a flowsheet.
     * @param array $fs ['units'=>[...], 'streams'=>[...]]
     * @return array results
     */
    public function run($fs)
    {
        $units   = [];
        foreach (($fs['units'] ?? []) as $u) $units[$u['id']] = $u;
        $streams = $fs['streams'] ?? [];

        // adjacency: inbound streams per unit port, outbound per unit port
        $inbound = [];   // unitId => [portIndex => streamId]
        $outbound = [];  // unitId => [portIndex => [streamId,...]]
        foreach ($streams as $s) {
            $inbound[$s['to']['unit']][$s['to']['port']] = $s['id'];
            $outbound[$s['from']['unit']][$s['from']['port']][] = $s['id'];
        }

        // Kahn topological ordering over units (edges = streams)
        $indeg = [];
        foreach ($units as $id => $u) $indeg[$id] = 0;
        foreach ($streams as $s) {
            if (isset($indeg[$s['to']['unit']])) $indeg[$s['to']['unit']]++;
        }
        $queue = [];
        foreach ($indeg as $id => $d) if ($d === 0) $queue[] = $id;
        $order = [];
        $localIndeg = $indeg;
        while ($queue) {
            $id = array_shift($queue);
            $order[] = $id;
            foreach (($outbound[$id] ?? []) as $port => $sids) {
                foreach ($sids as $sid) {
                    $target = null;
                    foreach ($streams as $s) if ($s['id'] === $sid) { $target = $s['to']['unit']; break; }
                    if ($target !== null && isset($localIndeg[$target])) {
                        $localIndeg[$target]--;
                        if ($localIndeg[$target] === 0) $queue[] = $target;
                    }
                }
            }
        }

        $recycle = count($order) < count($units);
        $warnings = [];
        if ($recycle) {
            $warnings[] = 'Recycle loop detected. This build solves acyclic (feed-forward) flowsheets; '
                        . 'recycle convergence (Wegstein/direct substitution) is on the roadmap. '
                        . 'Downstream-of-recycle units were skipped.';
        }

        $streamResults = []; // streamId => computed stream
        $unitResults   = []; // unitId => diagnostics (duty/work/etc)

        foreach ($order as $uid) {
            $u = $units[$uid];
            $ins = [];
            foreach (($inbound[$uid] ?? []) as $port => $sid) {
                if (isset($streamResults[$sid])) $ins[$port] = $streamResults[$sid];
            }
            [$outs, $diag] = $this->solveUnit($u, $ins);
            $unitResults[$uid] = $diag;
            foreach (($outbound[$uid] ?? []) as $port => $sids) {
                if (!isset($outs[$port])) continue;
                foreach ($sids as $sid) $streamResults[$sid] = $outs[$port];
            }
        }

        // overall balances across FEED and PRODUCT/sink units
        $massIn = 0.0; $massOut = 0.0;
        foreach ($units as $uid => $u) {
            if ($u['type'] === 'feed' && isset($unitResults[$uid]['out'])) {
                $massIn += $unitResults[$uid]['out']['mass'];
            }
        }
        // sinks = product units OR any inbound stream to a product
        foreach ($units as $uid => $u) {
            if ($u['type'] === 'product') {
                foreach (($inbound[$uid] ?? []) as $port => $sid) {
                    if (isset($streamResults[$sid])) $massOut += $streamResults[$sid]['mass'];
                }
            }
        }

        return [
            'ok'        => true,
            'order'     => $order,
            'streams'   => $this->serialiseStreams($streamResults, $streams),
            'units'     => $unitResults,
            'balance'   => [
                'mass_in'  => round($massIn, 3),
                'mass_out' => round($massOut, 3),
                'closure'  => $massIn > 0 ? round(100.0 * $massOut / $massIn, 2) : 0.0,
            ],
            'warnings'  => $warnings,
        ];
    }

    private function serialiseStreams($streamResults, $streams)
    {
        $out = [];
        foreach ($streams as $s) {
            $sid = $s['id'];
            if (!isset($streamResults[$sid])) {
                $out[] = ['id' => $sid, 'label' => $s['label'] ?? $sid, 'computed' => false];
                continue;
            }
            $st = $streamResults[$sid];
            $frac = [];
            if ($st['F'] > 1e-12) {
                foreach ($st['comp'] as $n => $f) $frac[$n] = round($f / $st['F'], 5);
            }
            $out[] = [
                'id'       => $sid,
                'label'    => $s['label'] ?? $sid,
                'from'     => $s['from'], 'to' => $s['to'],
                'computed' => true,
                'T'        => round($st['T'], 2),
                'Tc'       => round($st['T'] - 273.15, 2),
                'P'        => round($st['P'], 2),
                'phase'    => $st['phase'],
                'vfrac'    => round($st['vfrac'], 4),
                'molar'    => round($st['F'], 4),
                'mass'     => round($st['mass'], 3),
                'comp'     => array_map(fn($v) => round($v, 5), $st['comp']),
                'frac'     => $frac,
            ];
        }
        return $out;
    }

    // ---- unit operation models ------------------------------------------

    /** @return array [outputsByPort, diagnostics] */
    private function solveUnit($u, $ins)
    {
        $type = $u['type'];
        $p = $u['params'] ?? [];
        switch ($type) {

            case 'feed': {
                $comp = [];
                foreach (($p['components'] ?? []) as $n => $f) $comp[$n] = (float)$f;
                $T = isset($p['Tc']) ? ((float)$p['Tc'] + 273.15) : (float)($p['T'] ?? 298.15);
                $P = (float)($p['P'] ?? 101.325);
                $s = $this->makeStream($comp, $T, $P);
                return [[0 => $s], ['type' => 'feed', 'out' => ['mass' => $s['mass'], 'molar' => $s['F']]]];
            }

            case 'mixer': {
                $comp = [];
                $Hin = 0.0; $Pmin = INF;
                foreach ($ins as $s) {
                    foreach ($s['comp'] as $n => $f) $comp[$n] = ($comp[$n] ?? 0) + $f;
                    $Hin += $s['H'];
                    $Pmin = min($Pmin, $s['P']);
                }
                if (!$comp) return [[], ['type' => 'mixer', 'note' => 'no inlet']];
                $P = is_finite($Pmin) ? $Pmin : 101.325;
                $T = $this->solveT($comp, $P, $Hin);
                $s = $this->makeStream($comp, $T, $P);
                return [[0 => $s], ['type' => 'mixer', 'T_out' => round($T, 2), 'inlets' => count($ins)]];
            }

            case 'heater':
            case 'cooler': {
                $s0 = $ins[0] ?? null;
                if (!$s0) return [[], ['type' => $type, 'note' => 'no inlet']];
                $dP = (float)($p['dP'] ?? 0);
                $P  = max(1.0, $s0['P'] - $dP);
                $comp = $s0['comp'];
                if (($p['mode'] ?? 'outletT') === 'duty') {
                    $Q = (float)($p['duty'] ?? 0) * 3600.0; // kW -> kJ/h
                    $T = $this->solveT($comp, $P, $s0['H'] + $Q);
                } else {
                    $T = isset($p['Tc']) ? ((float)$p['Tc'] + 273.15) : (float)($p['T'] ?? $s0['T']);
                }
                $s = $this->makeStream($comp, $T, $P);
                $duty = ($s['H'] - $s0['H']) / 3600.0; // kW
                return [[0 => $s], ['type' => $type, 'duty_kW' => round($duty, 3), 'T_out' => round($T, 2)]];
            }

            case 'pump': {
                $s0 = $ins[0] ?? null;
                if (!$s0) return [[], ['type' => 'pump', 'note' => 'no inlet']];
                $Pout = (float)($p['P'] ?? $s0['P']);
                $eff  = max(0.05, (float)($p['eff'] ?? 0.75));
                // hydraulic power ~ Vdot * dP ; Vdot from liquid density approx (kg/h / 1000 kg/m3)
                $rho = 800.0; // kg/m3 nominal liquid
                $Vdot = ($s0['mass'] / $rho) / 3600.0; // m3/s
                $W = $Vdot * ($Pout - $s0['P']) * 1000.0 / $eff / 1000.0; // kW
                $s = $this->makeStream($s0['comp'], $s0['T'], $Pout);
                return [[0 => $s], ['type' => 'pump', 'work_kW' => round(max(0, $W), 4), 'P_out' => $Pout]];
            }

            case 'compressor': {
                $s0 = $ins[0] ?? null;
                if (!$s0) return [[], ['type' => 'compressor', 'note' => 'no inlet']];
                $Pout = (float)($p['P'] ?? $s0['P']);
                $eff  = max(0.05, (float)($p['eff'] ?? 0.75));
                $g    = (float)($p['gamma'] ?? 1.3);
                $ratio = max(1.0, $Pout / max(1e-6, $s0['P']));
                $Tout = $s0['T'] * (1.0 + (pow($ratio, ($g - 1) / $g) - 1.0) / $eff);
                $s = $this->makeStream($s0['comp'], $Tout, $Pout);
                $W = ($s['H'] - $s0['H']) / 3600.0; // kW
                return [[0 => $s], ['type' => 'compressor', 'work_kW' => round($W, 3), 'T_out' => round($Tout, 2), 'P_out' => $Pout]];
            }

            case 'splitter': {
                $s0 = $ins[0] ?? null;
                if (!$s0) return [[], ['type' => 'splitter', 'note' => 'no inlet']];
                $frac = min(1.0, max(0.0, (float)($p['split'] ?? 0.5)));
                $c1 = []; $c2 = [];
                foreach ($s0['comp'] as $n => $f) { $c1[$n] = $f * (1 - $frac); $c2[$n] = $f * $frac; }
                $sa = $this->makeStream($c1, $s0['T'], $s0['P']);
                $sb = $this->makeStream($c2, $s0['T'], $s0['P']);
                return [[0 => $sa, 1 => $sb], ['type' => 'splitter', 'split' => $frac]];
            }

            case 'flash': {
                $s0 = $ins[0] ?? null;
                if (!$s0) return [[], ['type' => 'flash', 'note' => 'no inlet']];
                $T = isset($p['Tc']) ? ((float)$p['Tc'] + 273.15) : (float)($p['T'] ?? $s0['T']);
                $P = (float)($p['P'] ?? $s0['P']);
                $s = $s0; $this->flashInto($s, $T, $P);
                $vap = $this->makeStream($s['nV'], $T, $P);
                $liq = $this->makeStream($s['nL'], $T, $P);
                $duty = (($vap['H'] + $liq['H']) - $s0['H']) / 3600.0; // kW to reach flash T
                return [[0 => $vap, 1 => $liq],
                        ['type' => 'flash', 'vfrac' => round($s['vfrac'], 4), 'duty_kW' => round($duty, 3),
                         'T' => round($T, 2), 'P' => round($P, 2)]];
            }

            case 'reactor': {
                $s0 = $ins[0] ?? null;
                if (!$s0) return [[], ['type' => 'reactor', 'note' => 'no inlet']];
                $comp = $s0['comp'];
                $extents = [];
                foreach (($p['reactions'] ?? []) as $ri => $rx) {
                    $key = $rx['key'] ?? null;
                    $conv = (float)($rx['conversion'] ?? 0);
                    $stoich = $rx['stoich'] ?? [];
                    if (!$key || !isset($stoich[$key]) || abs($stoich[$key]) < 1e-9) continue;
                    $nKey = $comp[$key] ?? 0;
                    $extent = $conv * $nKey / abs($stoich[$key]);
                    $extents[$ri] = $extent;
                    foreach ($stoich as $n => $coeff) {
                        $comp[$n] = max(0.0, ($comp[$n] ?? 0) + $coeff * $extent);
                    }
                }
                $P = (float)($p['P'] ?? $s0['P']);
                if (($p['mode'] ?? 'isothermal') === 'isothermal') {
                    $T = isset($p['Tc']) ? ((float)$p['Tc'] + 273.15) : $s0['T'];
                } else { // adiabatic: conserve enthalpy of feed (heat of reaction ignored unless given)
                    $Hrxn = 0.0;
                    foreach (($p['reactions'] ?? []) as $ri => $rx) {
                        $Hrxn += ($extents[$ri] ?? 0) * (float)($rx['Hrxn'] ?? 0); // kJ/h (neg=exo)
                    }
                    $T = $this->solveT($comp, $P, $s0['H'] - $Hrxn);
                }
                $s = $this->makeStream($comp, $T, $P);
                $duty = ($s['H'] - $s0['H']) / 3600.0;
                return [[0 => $s], ['type' => 'reactor', 'T_out' => round($T, 2),
                                    'duty_kW' => round($duty, 3), 'extents' => $extents]];
            }

            case 'column': {
                // Shortcut distillation: sharp split by relative volatility (Tb order)
                $s0 = $ins[0] ?? null;
                if (!$s0) return [[], ['type' => 'column', 'note' => 'no inlet']];
                $lk = $p['lightKey'] ?? null;
                $hk = $p['heavyKey'] ?? null;
                $recLK = (float)($p['recLK'] ?? 0.98); // LK recovery to distillate
                $recHK = (float)($p['recHK'] ?? 0.98); // HK recovery to bottoms
                $tbLK = $lk ? $this->tb($lk) : null;
                $tbHK = $hk ? $this->tb($hk) : null;
                $dist = []; $bot = [];
                foreach ($s0['comp'] as $n => $f) {
                    $tb = $this->tb($n);
                    if ($lk && $n === $lk)      { $dist[$n] = $f * $recLK;        $bot[$n] = $f * (1 - $recLK); }
                    elseif ($hk && $n === $hk)  { $dist[$n] = $f * (1 - $recHK);  $bot[$n] = $f * $recHK; }
                    elseif ($tbLK !== null && $tb <= $tbLK) { $dist[$n] = $f; $bot[$n] = 0.0; }   // lighter
                    elseif ($tbHK !== null && $tb >= $tbHK) { $dist[$n] = 0.0; $bot[$n] = $f; }   // heavier
                    else { $dist[$n] = $f * 0.5; $bot[$n] = $f * 0.5; }
                }
                $P = (float)($p['P'] ?? $s0['P']);
                // distillate at its bubble point (liquid), bottoms at bubble point
                $Td = $this->bubbleT($dist, $P);
                $Tb = $this->bubbleT($bot, $P);
                $d = $this->makeStream($dist, $Td, $P);
                $b = $this->makeStream($bot, $Tb, $P);
                // crude duty estimate: condenser ~ vaporise distillate, reboiler ~ vaporise bottoms
                $qCond = 0.0; foreach ($dist as $n => $f) $qCond += $f * $this->latentHeat($n);
                $qReb  = 0.0; foreach ($bot as $n => $f)  $qReb  += $f * $this->latentHeat($n);
                return [[0 => $d, 1 => $b],
                        ['type' => 'column', 'T_dist' => round($Td, 2), 'T_bot' => round($Tb, 2),
                         'condenser_kW' => round($qCond / 3600.0, 2),
                         'reboiler_kW'  => round($qReb / 3600.0, 2)]];
            }

            case 'product':
            default:
                return [[], ['type' => $type]];
        }
    }

    /** Bubble-point temperature at P for a liquid composition. */
    private function bubbleT($comp, $p)
    {
        $comp = array_filter($comp, fn($v) => $v > 1e-12);
        if (!$comp) return 298.15;
        $f = function ($t) use ($comp, $p) {
            $F = array_sum($comp); $s = 0.0;
            foreach ($comp as $n => $fl) $s += ($fl / $F) * $this->psat($n, $t);
            return $s - $p;
        };
        $lo = 100.0; $hi = 900.0;
        $flo = $f($lo);
        for ($i = 0; $i < 100; $i++) {
            $mid = 0.5 * ($lo + $hi);
            $fm = $f($mid);
            if (abs($fm) < 1e-3) return $mid;
            if ($flo * $fm < 0) $hi = $mid; else { $lo = $mid; $flo = $fm; }
        }
        return 0.5 * ($lo + $hi);
    }
}
