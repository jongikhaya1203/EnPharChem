<?php
/**
 * EnPharChem - Module Catalogue & How-To Manual PDF builders
 * ----------------------------------------------------------
 * Produces real PDF binaries with MiniPDF — no external dependencies.
 * Mirrors the HTML views in views/marketing-docs/ so both formats carry the
 * same content; the PDF is the distributable artefact.
 */
require_once __DIR__ . '/MiniPDF.php';
require_once __DIR__ . '/ModuleMockupPdf.php';
require_once __DIR__ . '/ModuleDocContent.php';

class ModuleDocsPdfBuilder
{
    const DEEP   = [10, 22, 40];
    const PRIM   = [13, 110, 253];
    const ACCENT = [13, 202, 240];
    const INK    = [22, 32, 46];
    const BODY   = [51, 66, 92];
    const MUTED  = [100, 116, 139];
    const LINE   = [219, 227, 236];
    const SOFT   = [246, 248, 251];
    const AMBER  = [122, 91, 0];
    const AMBBG  = [255, 251, 235];

    /** Strip the light markup used in step text (<strong>, <code>, entities). */
    private static function plain($s)
    {
        $s = preg_replace('/<[^>]+>/', '', (string)$s);
        $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim(preg_replace('/\s+/', ' ', $s));
    }

    /** Group modules by category slug, preserving registry order. */
    private static function group($modules)
    {
        $g = [];
        foreach ($modules as $m) { $g[$m['category_slug']][] = $m; }
        return $g;
    }

    // ---------------------------------------------------------------- chrome

    private static function coverBand($pdf, $title1, $title2, $lede, $stats, $tint)
    {
        $w = $pdf->pageW;
        $pdf->fillRect(0, 0, $w, 300, self::DEEP[0], self::DEEP[1], self::DEEP[2]);
        $pdf->fillRect(0, 250, $w, 50, $tint[0], $tint[1], $tint[2]);

        $pdf->setFont('B', 8);
        $pdf->setTextColor(143, 199, 240);
        $pdf->text(46, 70, strtoupper($title1));

        $pdf->setFont('B', 30);
        $pdf->setTextColor(255, 255, 255);
        $pdf->text(46, 96, $title2[0]);
        $pdf->setTextColor(111, 227, 255);
        $pdf->text(46, 132, $title2[1]);

        $pdf->setFont('', 10);
        $pdf->setTextColor(214, 232, 248);
        $pdf->setY(180);
        $pdf->writeWrapped(46, $w - 160, 14, $lede);

        $x = 46;
        foreach ($stats as $label => $value) {
            $pdf->setFont('B', 22);
            $pdf->setTextColor(111, 227, 255);
            $pdf->text($x, 250, (string)$value);
            $pdf->setFont('', 7);
            $pdf->setTextColor(169, 203, 232);
            $pdf->text($x, 278, strtoupper($label));
            $x += 130;
        }
        $pdf->setY(320);
    }

    private static function sectionTitle($pdf, $text)
    {
        $pdf->ensureSpace(30);
        $pdf->setFont('B', 14);
        $pdf->setTextColor(self::DEEP[0], self::DEEP[1], self::DEEP[2]);
        $pdf->text($pdf->marginL, $pdf->getY(), $text);
        $pdf->moveY(18);
        $pdf->fillRect($pdf->marginL, $pdf->getY(), $pdf->contentWidth(), 1.6,
            self::PRIM[0], self::PRIM[1], self::PRIM[2]);
        $pdf->moveY(12);
    }

    private static function categoryBar($pdf, $index, $name, $count)
    {
        $pdf->ensureSpace(42);
        $x = $pdf->marginL; $w = $pdf->contentWidth(); $y = $pdf->getY();
        $pdf->roundRectFill($x, $y, $w, 30, 4, self::DEEP[0], self::DEEP[1], self::DEEP[2]);
        $pdf->setFont('B', 15);
        $pdf->setTextColor(111, 227, 255);
        $pdf->text($x + 10, $y + 8, str_pad((string)$index, 2, '0', STR_PAD_LEFT));
        $pdf->setFont('B', 11);
        $pdf->setTextColor(255, 255, 255);
        $pdf->text($x + 36, $y + 9, $name);
        $pdf->setFont('', 8);
        $pdf->setTextColor(185, 214, 238);
        $pdf->textRight($x + $w - 10, $y + 11, $count . ' module' . ($count === 1 ? '' : 's'));
        $pdf->setY($y + 42);
    }

    private static function helperBox($pdf, $x, $w, $text)
    {
        $pdf->setFont('', 7.6);
        $lines = $pdf->wrapLines($text, $w - 18);
        $h = 15 + count($lines) * 9.4;
        $pdf->ensureSpace($h + 4);
        $y = $pdf->getY();
        $pdf->fillRect($x, $y, $w, $h, self::AMBBG[0], self::AMBBG[1], self::AMBBG[2]);
        $pdf->fillRect($x, $y, 2.4, $h, 245, 158, 11);
        $pdf->setFont('B', 6);
        $pdf->setTextColor(self::AMBER[0], self::AMBER[1], self::AMBER[2]);
        $pdf->text($x + 8, $y + 4, 'HOW HELPER');
        $pdf->setFont('', 7.6);
        $pdf->setTextColor(93, 71, 8);
        $ly = $y + 13;
        foreach ($lines as $l) { $pdf->text($x + 8, $ly, $l); $ly += 9.4; }
        $pdf->setY($y + $h + 5);
    }

    private static function footer($pdf, $left)
    {
        $pdf->setFont('', 6.6);
        $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
        $pdf->text($pdf->marginL, $pdf->pageH - 26, $left);
        $pdf->textRight($pdf->pageW - $pdf->marginR, $pdf->pageH - 26,
            COMPANY_NAME . '  |  ' . date('j F Y'));
    }

    // ------------------------------------------------------------- catalogue

    /**
     * @param array $categories  module_categories rows
     * @param array $modules     module rows with category_name / category_slug
     * @param array $featureMap  module id => string[] feature bullets
     */
    public static function catalogue($categories, $modules, $featureMap)
    {
        $pdf = new MiniPDF();
        $grouped = self::group($modules);
        $catMeta = [];
        foreach ($categories as $c) { $catMeta[$c['slug']] = $c; }

        self::coverBand($pdf, 'Product Catalogue', ['Every module,', 'in one catalogue.'],
            'The complete EnPharChem engineering suite - process simulation, exchanger design, '
            . 'subsurface science, advanced process control, manufacturing execution, supply chain, '
            . 'asset performance and digital grid management - documented module by module.',
            ['Modules' => count($modules), 'Categories' => count($grouped), 'Licence tiers' => 3],
            [11, 110, 168]);

        // Index
        self::sectionTitle($pdf, 'Catalogue index');
        $colW = $pdf->contentWidth() / 2 - 10;
        $i = 0;
        $startY = $pdf->getY();
        $rowH = 13;
        $rows = count($grouped);
        $perCol = (int)ceil($rows / 2);
        $n = 0;
        foreach ($grouped as $slug => $mods) {
            $n++;
            $col = $n > $perCol ? 1 : 0;
            $row = $col ? $n - $perCol - 1 : $n - 1;
            $x = $pdf->marginL + $col * ($colW + 20);
            $y = $startY + $row * $rowH;
            $pdf->setFont('', 8);
            $pdf->setTextColor(self::INK[0], self::INK[1], self::INK[2]);
            $label = str_pad((string)$n, 2, '0', STR_PAD_LEFT) . '   '
                   . ($catMeta[$slug]['name'] ?? $mods[0]['category_name']);
            $pdf->text($x, $y, $pdf->wrapLines($label, $colW - 30)[0]);
            $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
            $pdf->textRight($x + $colW, $y, (string)count($mods));
            $pdf->line($x, $y + 10, $x + $colW, $y + 10, self::LINE[0], self::LINE[1], self::LINE[2], 0.3);
        }
        $pdf->setY($startY + $perCol * $rowH + 16);

        // Disclosure about the figures
        $note = 'About the interface views. Each module is shown with a representative illustration '
              . 'of its working view, drawn as vector artwork so it stays sharp at any zoom. The figures '
              . 'depict the layout and readouts a user works with; the values shown are illustrative '
              . 'sample data, not output from a specific production run.';
        $pdf->setFont('', 7.6);
        $lines = $pdf->wrapLines($note, $pdf->contentWidth() - 16);
        $h = 12 + count($lines) * 9.4;
        $y = $pdf->getY();
        $pdf->fillRect($pdf->marginL, $y, $pdf->contentWidth(), $h, self::SOFT[0], self::SOFT[1], self::SOFT[2]);
        $pdf->fillRect($pdf->marginL, $y, 2.4, $h, self::ACCENT[0], self::ACCENT[1], self::ACCENT[2]);
        $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
        $ly = $y + 7;
        foreach ($lines as $l) { $pdf->text($pdf->marginL + 8, $ly, $l); $ly += 9.4; }
        self::footer($pdf, 'Module Catalogue');

        // Category sections
        $tierLabel = ['standard' => 'Standard', 'professional' => 'Professional', 'enterprise' => 'Enterprise'];
        $n = 0;
        foreach ($grouped as $slug => $mods) {
            $n++;
            $pdf->addPage();
            $catName = $catMeta[$slug]['name'] ?? $mods[0]['category_name'];
            self::categoryBar($pdf, $n, $catName, count($mods));

            foreach ($mods as $mod) {
                $cardH = 132;
                $pdf->ensureSpace($cardH + 8);
                $y = $pdf->getY();
                $x = $pdf->marginL;
                $w = $pdf->contentWidth();

                $pdf->strokeRect($x, $y, $w, $cardH, self::LINE[0], self::LINE[1], self::LINE[2], 0.6);

                // Figure (left half)
                $figW = $w * 0.46;
                ModuleMockupPdf::draw($pdf, $mod, $slug, $catName, $x + 8, $y + 8, $figW, 100);
                $pdf->setFont('I', 6.2);
                $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
                $pdf->text($x + 8, $y + 112, 'Representative working view');

                // Details (right half)
                $tx = $x + $figW + 20;
                $tw = $w - $figW - 30;
                $pdf->setFont('B', 10);
                $pdf->setTextColor(self::DEEP[0], self::DEEP[1], self::DEEP[2]);
                $nameLines = $pdf->wrapLines($mod['name'], $tw);
                $ty = $y + 12;
                foreach (array_slice($nameLines, 0, 2) as $nl) { $pdf->text($tx, $ty, $nl); $ty += 12; }

                $pdf->setFont('', 6.8);
                $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
                $pdf->text($tx, $ty, $catName);
                $ty += 12;

                $pdf->setFont('', 7.2);
                $feats = $featureMap[$mod['id']] ?? [];
                foreach (array_slice($feats, 0, 5) as $f) {
                    $fl = $pdf->wrapLines(self::plain($f), $tw - 10);
                    $fl = array_slice($fl, 0, 2);
                    $pdf->fillRect($tx, $ty + 2.6, 2.4, 2.4, self::PRIM[0], self::PRIM[1], self::PRIM[2]);
                    $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
                    foreach ($fl as $k => $l) {
                        if ($ty > $y + $cardH - 22) { break 2; }
                        $pdf->text($tx + 8, $ty, $l);
                        $ty += 8.8;
                    }
                    $ty += 1.4;
                }

                // Badges
                $tier = $mod['license_required'] ?? 'standard';
                $by = $y + $cardH - 16;
                $pdf->setFont('B', 6.2);
                $vTxt = 'v' . ($mod['version'] ?? '1.0');
                $vW = $pdf->stringWidth($vTxt) + 12;
                $pdf->roundRectFill($tx, $by, $vW, 11, 5, 238, 243, 249);
                $pdf->setTextColor(67, 83, 107);
                $pdf->text($tx + 6, $by + 2.6, $vTxt);

                $lTxt = ($tierLabel[$tier] ?? ucfirst($tier)) . ' licence';
                $lW = $pdf->stringWidth($lTxt) + 12;
                $pdf->roundRectFill($tx + $vW + 6, $by, $lW, 11, 5, 232, 240, 254);
                $pdf->setTextColor(20, 80, 156);
                $pdf->text($tx + $vW + 12, $by + 2.6, $lTxt);

                $pdf->setY($y + $cardH + 8);
            }
            self::footer($pdf, $catName);
        }

        return $pdf->output();
    }

    // ---------------------------------------------------------------- manual

    /**
     * @param array $taskMap module id => task arrays
     */
    public static function manual($categories, $modules, $taskMap)
    {
        $pdf = new MiniPDF();
        $grouped = self::group($modules);
        $catMeta = [];
        foreach ($categories as $c) { $catMeta[$c['slug']] = $c; }
        $totalTasks = 0;
        foreach ($taskMap as $ts) { $totalTasks += count($ts); }

        self::coverBand($pdf, 'User Documentation', ['How to run', 'every module.'],
            'A task-by-task operating manual for the EnPharChem platform. For each module it sets out '
            . 'what you can do, the inputs each task needs, the steps to follow in the module workspace, '
            . 'and what the result should tell you.',
            ['Modules' => count($modules), 'Documented tasks' => $totalTasks, 'Categories' => count($grouped)],
            [14, 107, 107]);

        self::sectionTitle($pdf, 'How to use this manual');
        $pdf->setFont('', 8.2);
        $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
        $pdf->writeWrapped($pdf->marginL, $pdf->contentWidth(), 11.5,
            'Every task in this manual follows the same shape: an Objective stating what it achieves, '
            . 'the Inputs to have ready before starting, the Expected output, a numbered sequence of '
            . 'Steps to follow on screen, and a How Helper - the practical hint that saves the rework.');
        $pdf->moveY(6);
        $pdf->writeWrapped($pdf->marginL, $pdf->contentWidth(), 11.5,
            'Every module opens on the same five-tab workspace, so once you have run one module you can '
            . 'run any of them: Overview for scope and version, Simulation to create and run a case, '
            . 'Configuration for units, tolerance and iteration limits, Results for the output charts, '
            . 'and Documentation for the method notes.');
        $pdf->moveY(8);

        self::helperBox($pdf, $pdf->marginL, $pdf->contentWidth(),
            'Before starting any task, create the Project that will own the work (Projects > New Project). '
            . 'Every simulation is attached to a project, and runs created without one are difficult to '
            . 'find and impossible to compare later.');
        self::helperBox($pdf, $pdf->marginL, $pdf->contentWidth(),
            'Modules are governed per licence tier. If a module reports that a licence is required, request '
            . 'one through the Licensing Portal - an administrator grants it against your account, after '
            . 'which the module runs without further action.');
        self::footer($pdf, 'How-To Manual');

        $n = 0;
        foreach ($grouped as $slug => $mods) {
            $n++;
            $pdf->addPage();
            $catName = $catMeta[$slug]['name'] ?? $mods[0]['category_name'];
            self::categoryBar($pdf, $n, $catName, count($mods));

            foreach ($mods as $mod) {
                $tasks = $taskMap[$mod['id']] ?? [];
                $x = $pdf->marginL; $w = $pdf->contentWidth();

                // Module header
                $pdf->ensureSpace(46);
                $y = $pdf->getY();
                $pdf->fillRect($x, $y, $w, 30, self::SOFT[0], self::SOFT[1], self::SOFT[2]);
                $pdf->fillRect($x, $y, 3, 30, self::PRIM[0], self::PRIM[1], self::PRIM[2]);
                $pdf->setFont('B', 11);
                $pdf->setTextColor(self::DEEP[0], self::DEEP[1], self::DEEP[2]);
                $pdf->text($x + 10, $y + 5, $pdf->wrapLines($mod['name'], $w - 110)[0]);
                $pdf->setFont('', 6.6);
                $pdf->setTextColor(20, 80, 156);
                $pdf->textRight($x + $w - 8, $y + 7, ucfirst($mod['license_required'] ?? 'standard'));
                if (!empty($mod['description'])) {
                    $pdf->setFont('', 7);
                    $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
                    $pdf->text($x + 10, $y + 19, $pdf->wrapLines(self::plain($mod['description']), $w - 20)[0]);
                }
                $pdf->setY($y + 38);

                foreach ($tasks as $ti => $t) {
                    $pdf->ensureSpace(70);
                    $ty = $pdf->getY();

                    // Task heading
                    $pdf->setFont('B', 6.4);
                    $tag = 'TASK ' . ($ti + 1);
                    $tagW = $pdf->stringWidth($tag) + 12;
                    $pdf->roundRectFill($x, $ty, $tagW, 11, 2, self::PRIM[0], self::PRIM[1], self::PRIM[2]);
                    $pdf->setTextColor(255, 255, 255);
                    $pdf->text($x + 6, $ty + 2.6, $tag);

                    $pdf->setFont('B', 9);
                    $pdf->setTextColor(self::DEEP[0], self::DEEP[1], self::DEEP[2]);
                    $titleLines = $pdf->wrapLines($t['title'], $w - $tagW - 12);
                    $lyy = $ty + 1;
                    foreach ($titleLines as $tl) { $pdf->text($x + $tagW + 8, $lyy, $tl); $lyy += 11; }
                    $pdf->setY(max($ty + 15, $lyy + 2));

                    // Objective
                    $pdf->setFont('', 7.6);
                    $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
                    $pdf->writeWrapped($x, $w, 9.6, self::plain($t['objective']));
                    $pdf->moveY(3);

                    // Inputs / outputs
                    $halfW = $w / 2 - 4;
                    $pdf->setFont('', 7.2);
                    $inL  = $pdf->wrapLines(self::plain($t['inputs']), $halfW - 12);
                    $outL = $pdf->wrapLines(self::plain($t['outputs']), $halfW - 12);
                    $ioH  = 14 + max(count($inL), count($outL)) * 8.8;
                    $pdf->ensureSpace($ioH + 4);
                    $iy = $pdf->getY();
                    $pdf->fillRect($x, $iy, $halfW, $ioH, self::SOFT[0], self::SOFT[1], self::SOFT[2]);
                    $pdf->fillRect($x + $halfW + 8, $iy, $halfW, $ioH, self::SOFT[0], self::SOFT[1], self::SOFT[2]);
                    $pdf->setFont('B', 5.6);
                    $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
                    $pdf->text($x + 6, $iy + 4, 'INPUTS REQUIRED');
                    $pdf->text($x + $halfW + 14, $iy + 4, 'EXPECTED OUTPUT');
                    $pdf->setFont('', 7.2);
                    $pdf->setTextColor(43, 58, 82);
                    $ly = $iy + 12;
                    foreach ($inL as $l) { $pdf->text($x + 6, $ly, $l); $ly += 8.8; }
                    $ly = $iy + 12;
                    foreach ($outL as $l) { $pdf->text($x + $halfW + 14, $ly, $l); $ly += 8.8; }
                    $pdf->setY($iy + $ioH + 6);

                    // Steps
                    foreach ($t['steps'] as $si => $step) {
                        $pdf->setFont('', 7.6);
                        $lines = $pdf->wrapLines(self::plain($step), $w - 20);
                        $pdf->ensureSpace(count($lines) * 9.6 + 4);
                        $sy = $pdf->getY();
                        $pdf->circle($x + 5, $sy + 4, 4.4, [232, 240, 254]);
                        $pdf->setFont('B', 5.8);
                        $pdf->setTextColor(20, 80, 156);
                        $pdf->textCenter($x + 5, $sy + 1.6, (string)($si + 1));
                        $pdf->setFont('', 7.6);
                        $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
                        $ly = $sy;
                        foreach ($lines as $l) { $pdf->text($x + 16, $ly, $l); $ly += 9.6; }
                        $pdf->setY($ly + 1.6);
                    }
                    $pdf->moveY(3);

                    if (!empty($t['tip'])) {
                        self::helperBox($pdf, $x, $w, self::plain($t['tip']));
                    }
                    $pdf->moveY(4);
                }
            }
            self::footer($pdf, $catName);
        }

        return $pdf->output();
    }
}
