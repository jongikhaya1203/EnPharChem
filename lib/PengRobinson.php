<?php
/**
 * EnPharChem - Peng-Robinson Equation of State
 * ---------------------------------------------
 * Independent, first-principles implementation of the Peng-Robinson (1976)
 * cubic EOS with van der Waals one-fluid mixing rules. Used by the Flowsheet
 * Simulator to compute vapour-liquid equilibrium (K-values from fugacity
 * coefficients) for a rigorous, non-ideal isothermal flash.
 *
 * This is textbook thermodynamics implemented from scratch; it contains no
 * third-party code or proprietary property data.
 *
 * SI units internally: P [Pa], T [K], R [J/mol.K].
 */

class PengRobinson
{
    const R     = 8.314;      // J/mol.K
    const SQRT2 = 1.4142135623730951;

    /** @var array name => [Tc K, Pc Pa, omega] */
    private $crit;
    /** @var array binary interaction coefficients kij (default 0) */
    private $kij;

    /**
     * @param array $crit name => [Tc(K), Pc(bar), omega]
     */
    public function __construct($crit, $kij = [])
    {
        $this->crit = [];
        foreach ($crit as $name => $c) {
            $this->crit[$name] = [$c[0], $c[1] * 1e5, $c[2]]; // bar -> Pa
        }
        $this->kij = $kij;
    }

    public function has($name) { return isset($this->crit[$name]); }
    public function hasAll($names)
    {
        foreach ($names as $n) if (!isset($this->crit[$n])) return false;
        return true;
    }

    // ---- pure-component EOS parameters ----------------------------------

    /** returns [a*alpha, b] for a component at T (SI). */
    private function abAlpha($name, $t)
    {
        [$tc, $pc, $w] = $this->crit[$name];
        $a = 0.45724 * self::R * self::R * $tc * $tc / $pc;
        $b = 0.07780 * self::R * $tc / $pc;
        $kappa = 0.37464 + 1.54226 * $w - 0.26992 * $w * $w;
        $tr = $t / $tc;
        $alpha = pow(1.0 + $kappa * (1.0 - sqrt($tr)), 2);
        return [$a * $alpha, $b];
    }

    /** returns [a*alpha, b, d(a*alpha)/dT] for a component at T (SI). */
    private function abAlphaD($name, $t)
    {
        [$tc, $pc, $w] = $this->crit[$name];
        $a0 = 0.45724 * self::R * self::R * $tc * $tc / $pc;
        $b  = 0.07780 * self::R * $tc / $pc;
        $kappa = 0.37464 + 1.54226 * $w - 0.26992 * $w * $w;
        $sqrtTr = sqrt($t / $tc);
        $m = 1.0 + $kappa * (1.0 - $sqrtTr);
        $alpha = $m * $m;
        $daAlpha = $a0 * (-$m * $kappa / sqrt($t * $tc)); // a_i * dalpha/dT
        return [$a0 * $alpha, $b, $daAlpha];
    }

    private function kijOf($i, $j)
    {
        return $this->kij[$i][$j] ?? $this->kij[$j][$i] ?? 0.0;
    }

    /**
     * Residual (departure) molar enthalpy H - H^ig for a phase, in J/mol.
     * Negative for real fluids; the liquid/vapour difference reproduces the
     * latent heat directly from the EOS.
     * @return float|null null if no valid Z root
     */
    public function departureH($x, $t, $pKpa, $phase)
    {
        $names = array_keys($x);
        if (!$this->hasAll($names)) return null;
        $p = $pKpa * 1000.0;

        $aa = []; $b = []; $daa = [];
        foreach ($names as $n) { [$aa[$n], $b[$n], $daa[$n]] = $this->abAlphaD($n, $t); }

        $amix = 0.0; $bmix = 0.0; $damix = 0.0;
        foreach ($names as $i) {
            $bmix += $x[$i] * $b[$i];
            foreach ($names as $j) {
                $k = 1.0 - $this->kijOf($i, $j);
                $root = sqrt($aa[$i] * $aa[$j]);
                $amix += $x[$i] * $x[$j] * $k * $root;
                // d/dT sqrt(aa_i aa_j) = (daa_i aa_j + aa_i daa_j)/(2 sqrt(aa_i aa_j))
                $droot = $root > 0 ? ($daa[$i] * $aa[$j] + $aa[$i] * $daa[$j]) / (2.0 * $root) : 0.0;
                $damix += $x[$i] * $x[$j] * $k * $droot;
            }
        }

        $A = $amix * $p / pow(self::R * $t, 2);
        $B = $bmix * $p / (self::R * $t);
        $roots = $this->cubicRealRoots(-(1 - $B), $A - 3 * $B * $B - 2 * $B, -($A * $B - $B * $B - $B * $B * $B));
        $valid = array_filter($roots, fn($z) => $z > $B + 1e-9);
        if (!$valid) return null;
        $Z = $phase === 'liquid' ? min($valid) : max($valid);

        $term = log(($Z + (1 + self::SQRT2) * $B) / ($Z + (1 - self::SQRT2) * $B));
        return self::R * $t * ($Z - 1)
             + (($t * $damix - $amix) / (2.0 * self::SQRT2 * $bmix)) * $term; // J/mol
    }

    // ---- fugacity coefficients for a phase ------------------------------

    /**
     * ln(phi_i) for every component in a phase of composition $x at (T,P).
     * @param string $phase 'liquid' (smallest Z) or 'vapor' (largest Z)
     * @return array|null name => ln phi_i, or null if no valid root
     */
    public function lnPhi($x, $t, $p, $phase)
    {
        $names = array_keys($x);
        $aa = []; $b = [];
        foreach ($names as $n) { [$aa[$n], $b[$n]] = $this->abAlpha($n, $t); }

        // mixing rules
        $amix = 0.0; $bmix = 0.0;
        $aij = [];
        foreach ($names as $i) {
            $bmix += $x[$i] * $b[$i];
            foreach ($names as $j) {
                $v = sqrt($aa[$i] * $aa[$j]) * (1.0 - $this->kijOf($i, $j));
                $aij[$i][$j] = $v;
                $amix += $x[$i] * $x[$j] * $v;
            }
        }

        $A = $amix * $p / pow(self::R * $t, 2);
        $B = $bmix * $p / (self::R * $t);

        $roots = $this->cubicRealRoots(-(1 - $B), $A - 3 * $B * $B - 2 * $B, -($A * $B - $B * $B - $B * $B * $B));
        $valid = array_filter($roots, fn($z) => $z > $B + 1e-9);
        if (!$valid) return null;
        $Z = $phase === 'liquid' ? min($valid) : max($valid);

        $lnphi = [];
        $c1 = $A / (2.0 * self::SQRT2 * $B);
        $c2 = log(($Z + (1 + self::SQRT2) * $B) / ($Z + (1 - self::SQRT2) * $B));
        foreach ($names as $i) {
            $sumAij = 0.0;
            foreach ($names as $j) $sumAij += $x[$j] * $aij[$i][$j];
            $lnphi[$i] = ($b[$i] / $bmix) * ($Z - 1)
                       - log($Z - $B)
                       - $c1 * (2.0 * $sumAij / $amix - $b[$i] / $bmix) * $c2;
        }
        return $lnphi;
    }

    // ---- isothermal flash via successive substitution -------------------

    /**
     * @param array $z feed mole fractions (name => z_i, sum ~1)
     * @return array|null ['beta','x','y','K','phase'] or null on failure
     */
    public function flash($z, $t, $pKpa)
    {
        $names = array_keys($z);
        if (!$this->hasAll($names)) return null;
        $p = $pKpa * 1000.0; // kPa -> Pa

        // Wilson initial K
        $K = [];
        foreach ($names as $i) {
            [$tc, $pc, $w] = $this->crit[$i];
            $K[$i] = ($pc / $p) * exp(5.373 * (1.0 + $w) * (1.0 - $tc / $t));
        }

        $beta = 0.5;
        for ($iter = 0; $iter < 60; $iter++) {
            [$phase, $beta] = $this->solveBeta($z, $K);
            if ($phase === 'liquid') return $this->singlePhase($z, $t, $p, 'liquid', $K);
            if ($phase === 'vapor')  return $this->singlePhase($z, $t, $p, 'vapor', $K);

            $x = []; $y = [];
            foreach ($names as $i) {
                $den = 1.0 + $beta * ($K[$i] - 1.0);
                $x[$i] = $z[$i] / $den;
                $y[$i] = $K[$i] * $x[$i];
            }
            $x = $this->normalise($x);
            $y = $this->normalise($y);

            $lnL = $this->lnPhi($x, $t, $p, 'liquid');
            $lnV = $this->lnPhi($y, $t, $p, 'vapor');
            if ($lnL === null || $lnV === null) return null;

            $err = 0.0; $Knew = [];
            foreach ($names as $i) {
                $Knew[$i] = exp($lnL[$i] - $lnV[$i]);
                $err += pow(log($Knew[$i] / $K[$i]), 2);
            }
            $K = $Knew;
            if ($err < 1e-10) {
                [$phase, $beta] = $this->solveBeta($z, $K);
                if ($phase === 'two-phase') {
                    return ['beta' => $beta, 'x' => $x, 'y' => $y, 'K' => $K, 'phase' => 'two-phase'];
                }
                return $this->singlePhase($z, $t, $p, $phase === 'vapor' ? 'vapor' : 'liquid', $K);
            }
        }
        return null; // did not converge -> caller falls back to ideal
    }

    private function singlePhase($z, $t, $p, $phase, $K)
    {
        return ['beta' => $phase === 'vapor' ? 1.0 : 0.0,
                'x' => $z, 'y' => $z, 'K' => $K, 'phase' => $phase];
    }

    /** Decide phase + solve Rachford-Rice for beta. */
    private function solveBeta($z, $K)
    {
        $f = function ($beta) use ($z, $K) {
            $s = 0.0;
            foreach ($z as $i => $zi) $s += $zi * ($K[$i] - 1.0) / (1.0 + $beta * ($K[$i] - 1.0));
            return $s;
        };
        $f0 = $f(0.0); $f1 = $f(1.0);
        if ($f0 < 0) return ['liquid', 0.0];  // bubble check: subcooled liquid
        if ($f1 > 0) return ['vapor', 1.0];    // dew check: superheated vapour
        $lo = 0.0; $hi = 1.0; $flo = $f0;
        for ($i = 0; $i < 80; $i++) {
            $mid = 0.5 * ($lo + $hi);
            $fm = $f($mid);
            if (abs($fm) < 1e-12) return ['two-phase', $mid];
            if ($flo * $fm < 0) $hi = $mid; else { $lo = $mid; $flo = $fm; }
        }
        return ['two-phase', 0.5 * ($lo + $hi)];
    }

    private function normalise($v)
    {
        $s = array_sum($v);
        if ($s <= 0) return $v;
        foreach ($v as $k => $x) $v[$k] = $x / $s;
        return $v;
    }

    // ---- cubic solver: Z^3 + a2 Z^2 + a1 Z + a0 = 0 ---------------------

    private function cubicRealRoots($a2, $a1, $a0)
    {
        $p = $a1 - $a2 * $a2 / 3.0;
        $q = 2.0 * pow($a2, 3) / 27.0 - $a2 * $a1 / 3.0 + $a0;
        $shift = -$a2 / 3.0;
        $disc = $q * $q / 4.0 + $p * $p * $p / 27.0;

        if ($disc > 1e-14) {
            $sq = sqrt($disc);
            $u = $this->cbrt(-$q / 2.0 + $sq);
            $v = $this->cbrt(-$q / 2.0 - $sq);
            return [$u + $v + $shift];
        }
        // three real roots (trigonometric form)
        if (abs($p) < 1e-14) return [$shift, $shift, $shift];
        $r3 = sqrt(-pow($p, 3) / 27.0);
        $arg = max(-1.0, min(1.0, (-$q / 2.0) / $r3));
        $phi = acos($arg);
        $m = 2.0 * sqrt(-$p / 3.0);
        $roots = [];
        for ($k = 0; $k < 3; $k++) {
            $roots[] = $m * cos(($phi + 2.0 * M_PI * $k) / 3.0) + $shift;
        }
        return $roots;
    }

    private function cbrt($x)
    {
        return $x < 0 ? -pow(-$x, 1.0 / 3.0) : pow($x, 1.0 / 3.0);
    }
}
