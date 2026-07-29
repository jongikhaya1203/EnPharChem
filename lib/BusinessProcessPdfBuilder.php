<?php
/**
 * EnPharChem - Business Process Map PDF builder
 * ---------------------------------------------
 * Renders a real PDF that maps each business process to its use cases, the job
 * titles and platform roles that carry it out, and a worked example. Built on
 * MiniPDF; no external dependencies. Mirrors the house style of
 * ModuleDocsPdfBuilder.
 */
require_once __DIR__ . '/MiniPDF.php';
require_once __DIR__ . '/BusinessProcessMap.php';

class BusinessProcessPdfBuilder
{
    const DEEP   = [12, 22, 44];
    const TINT   = [37, 61, 110];
    const PRIM   = [13, 110, 253];
    const ACCENT = [13, 202, 240];
    const INK    = [22, 32, 46];
    const BODY   = [51, 66, 92];
    const MUTED  = [100, 116, 139];
    const LINE   = [219, 227, 236];
    const SOFT   = [246, 248, 251];

    /** Role badge colours: [fill, text]. */
    private static function roleColour($role)
    {
        switch ($role) {
            case 'superuser': return [[243, 235, 253], [91, 46, 168]];
            case 'admin':     return [[232, 240, 254], [20, 80, 156]];
            case 'engineer':  return [[231, 245, 239], [15, 122, 82]];
            case 'operator':  return [[255, 244, 224], [154, 92, 8]];
            case 'viewer':    return [[238, 243, 249], [67, 83, 107]];
            default:          return [[238, 243, 249], [67, 83, 107]];
        }
    }

    private static function footer($pdf, $left)
    {
        $pdf->setFont('', 6.6);
        $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
        $pdf->text($pdf->marginL, $pdf->pageH - 26, $left);
        $pdf->textRight($pdf->pageW - $pdf->marginR, $pdf->pageH - 26, COMPANY_NAME . '  |  ' . date('j F Y'));
    }

    private static function sectionTitle($pdf, $text)
    {
        $pdf->ensureSpace(30);
        $pdf->setFont('B', 14);
        $pdf->setTextColor(self::DEEP[0], self::DEEP[1], self::DEEP[2]);
        $pdf->text($pdf->marginL, $pdf->getY(), $text);
        $pdf->moveY(18);
        $pdf->fillRect($pdf->marginL, $pdf->getY(), $pdf->contentWidth(), 1.6, self::PRIM[0], self::PRIM[1], self::PRIM[2]);
        $pdf->moveY(12);
    }

    /** A job-title line with a coloured role badge to its right. */
    private static function actorRow($pdf, $x, $w, $title, $role)
    {
        [$bg, $fg] = self::roleColour($role);
        $pdf->setFont('', 8);
        $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
        $pdf->text($x, $pdf->getY(), $pdf->wrapLines($title, $w - 78)[0]);
        $pdf->setFont('B', 6);
        $bw = $pdf->stringWidth($role) + 12;
        $bx = $x + $w - $bw;
        $pdf->roundRectFill($bx, $pdf->getY() - 1.5, $bw, 11, 5, $bg[0], $bg[1], $bg[2]);
        $pdf->setTextColor($fg[0], $fg[1], $fg[2]);
        $pdf->text($bx + 6, $pdf->getY() + 1, $role);
        $pdf->moveY(14);
    }

    public static function build()
    {
        $pdf = new MiniPDF();
        $processes = BusinessProcessMap::processes();
        $legend    = BusinessProcessMap::roleLegend();

        // ---------------------------------------------------------------- cover
        $w = $pdf->pageW;
        $pdf->fillRect(0, 0, $w, 300, self::DEEP[0], self::DEEP[1], self::DEEP[2]);
        $pdf->fillRect(0, 250, $w, 50, self::TINT[0], self::TINT[1], self::TINT[2]);
        $pdf->setFont('B', 8);
        $pdf->setTextColor(143, 199, 240);
        $pdf->text(46, 70, 'BUSINESS PROCESS MAP');
        $pdf->setFont('B', 29);
        $pdf->setTextColor(255, 255, 255);
        $pdf->text(46, 98, 'Processes, use cases');
        $pdf->setTextColor(111, 227, 255);
        $pdf->text(46, 132, 'and the people who run them.');
        $pdf->setFont('', 10);
        $pdf->setTextColor(214, 232, 248);
        $pdf->setY(180);
        $pdf->writeWrapped(46, $w - 150, 14,
            'How the EnPharChem platform supports the business end to end: each process mapped to the '
            . 'use cases it serves, the job titles and platform roles that carry it out, and a worked example.');
        $pdf->setFont('B', 22);
        $pdf->setTextColor(111, 227, 255);
        $pdf->text(46, 250, (string) count($processes));
        $pdf->setFont('', 7);
        $pdf->setTextColor(169, 203, 232);
        $pdf->text(46, 278, 'BUSINESS PROCESSES');
        $pdf->setFont('B', 22);
        $pdf->setTextColor(111, 227, 255);
        $pdf->text(176, 250, (string) count($legend));
        $pdf->setFont('', 7);
        $pdf->setTextColor(169, 203, 232);
        $pdf->text(176, 278, 'PLATFORM ROLES');
        $pdf->setFont('', 8);
        $pdf->setTextColor(150, 185, 220);
        $pdf->text(46, 296, COMPANY_NAME . '  -  Platform v' . APP_VERSION . '  -  Generated ' . date('j F Y'));
        $pdf->setY(322);

        // ------------------------------------------------- how to read + legend
        self::sectionTitle($pdf, 'How to read this map');
        $pdf->setFont('', 8.4);
        $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
        $pdf->writeWrapped($pdf->marginL, $pdf->contentWidth(), 11.5,
            'Each business process below lists the use cases it serves, the actors that carry it out - '
            . 'by job title and by the platform role that grants their access - and a worked example showing '
            . 'the process in action. Job titles are the industry roles that map onto the platform\'s five '
            . 'access roles, defined here:');
        $pdf->moveY(6);

        foreach ($legend as $l) {
            [$bg, $fg] = self::roleColour($l['role']);
            $pdf->ensureSpace(16);
            $y = $pdf->getY();
            $pdf->setFont('B', 7);
            $bw = $pdf->stringWidth($l['role']) + 14;
            $pdf->roundRectFill($pdf->marginL, $y - 1, $bw, 12, 5, $bg[0], $bg[1], $bg[2]);
            $pdf->setTextColor($fg[0], $fg[1], $fg[2]);
            $pdf->text($pdf->marginL + 7, $y + 2, $l['role']);
            $pdf->setFont('', 8);
            $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
            $tx = $pdf->marginL + 76;
            $lines = $pdf->wrapLines($l['scope'], $pdf->contentWidth() - 80);
            foreach ($lines as $i => $ln) { $pdf->text($tx, $y + 2 + $i * 9.4, $ln); }
            $pdf->setY($y + max(14, count($lines) * 9.4 + 4));
        }
        self::footer($pdf, 'Business Process Map');

        // ----------------------------------------------------- summary matrix
        $pdf->addPage();
        self::sectionTitle($pdf, 'Process summary');
        $cw = $pdf->contentWidth();
        $cols = [
            ['width' => $cw * 0.06, 'align' => 'C'],
            ['width' => $cw * 0.30, 'align' => 'L'],
            ['width' => $cw * 0.34, 'align' => 'L'],
            ['width' => $cw * 0.30, 'align' => 'L'],
        ];
        $rows = [];
        foreach ($processes as $i => $p) {
            $titles = array_map(fn($a) => $a['title'], $p['actors']);
            $titles = array_slice($titles, 0, 3);
            $rows[] = [
                (string) ($i + 1),
                $p['name'],
                implode(', ', $titles),
                $p['useCases'][0],
            ];
        }
        $pdf->table($pdf->marginL, $cols, ['#', 'Business process', 'Primary job titles', 'Headline use case'], $rows, [
            'headerBg' => self::DEEP, 'bodyFS' => 8, 'headerFS' => 8, 'rowMinH' => 16, 'padding' => 5,
        ]);
        self::footer($pdf, 'Business Process Map');

        // ------------------------------------------------- per-process detail
        foreach ($processes as $i => $p) {
            $pdf->addPage();
            $x = $pdf->marginL; $cw = $pdf->contentWidth();

            // Process header bar
            $pdf->roundRectFill($x, $pdf->getY(), $cw, 34, 5, self::DEEP[0], self::DEEP[1], self::DEEP[2]);
            $hy = $pdf->getY();
            $pdf->setFont('B', 16);
            $pdf->setTextColor(111, 227, 255);
            $pdf->text($x + 12, $hy + 9, str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT));
            $pdf->setFont('B', 12);
            $pdf->setTextColor(255, 255, 255);
            $pdf->text($x + 42, $hy + 11, $pdf->wrapLines($p['name'], $cw - 60)[0]);
            $pdf->setY($hy + 42);

            // Summary
            $pdf->setFont('', 8.6);
            $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
            $pdf->writeWrapped($x, $cw, 11.5, $p['summary']);
            $pdf->moveY(4);

            // App area strip
            $pdf->setFont('B', 6);
            $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
            $pdf->text($x, $pdf->getY(), 'WHERE IN THE PLATFORM');
            $pdf->moveY(9);
            $pdf->setFont('', 7.8);
            $pdf->setTextColor(self::INK[0], self::INK[1], self::INK[2]);
            $pdf->writeWrapped($x, $cw, 10, $p['appArea']);
            $pdf->moveY(8);

            // Two columns: Use cases (left) | Actors & roles (right)
            $colGap = 16;
            $colW = ($cw - $colGap) / 2;
            $leftX = $x;
            $rightX = $x + $colW + $colGap;
            $topY = $pdf->getY();

            // Left: use cases
            $pdf->setFont('B', 8.5);
            $pdf->setTextColor(self::PRIM[0], self::PRIM[1], self::PRIM[2]);
            $pdf->text($leftX, $topY, 'Use cases');
            $pdf->setY($topY + 13);
            $pdf->setFont('', 8);
            foreach ($p['useCases'] as $uc) {
                $lines = $pdf->wrapLines($uc, $colW - 10);
                $pdf->ensureSpace(count($lines) * 9.6 + 2);
                $yy = $pdf->getY();
                $pdf->fillRect($leftX, $yy + 2.6, 2.6, 2.6, self::PRIM[0], self::PRIM[1], self::PRIM[2]);
                $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
                foreach ($lines as $k => $ln) { $pdf->text($leftX + 8, $yy + $k * 9.6, $ln); }
                $pdf->setY($yy + count($lines) * 9.6 + 3);
            }
            $leftEndY = $pdf->getY();

            // Right: actors (job title + role badge). Render in its own column.
            $pdf->setY($topY);
            $pdf->setFont('B', 8.5);
            $pdf->setTextColor(self::PRIM[0], self::PRIM[1], self::PRIM[2]);
            $pdf->text($rightX, $topY, 'Actors & roles');
            $pdf->setY($topY + 13);
            foreach ($p['actors'] as $a) {
                $pdf->ensureSpace(15);
                self::actorRow($pdf, $rightX, $colW, $a['title'], $a['role']);
            }
            $rightEndY = $pdf->getY();

            // Continue below the taller column.
            $pdf->setY(max($leftEndY, $rightEndY) + 6);

            // Worked example box
            $ex = $p['example'];
            $pdf->setFont('', 8);
            $stepLineCounts = array_map(fn($s) => count($pdf->wrapLines($s, $cw - 40)), $ex['steps']);
            $outcomeLines = $pdf->wrapLines('Outcome: ' . $ex['outcome'], $cw - 28);
            $boxH = 30 + array_sum($stepLineCounts) * 9.6 + count($ex['steps']) * 2 + count($outcomeLines) * 9.6 + 14;
            $pdf->ensureSpace($boxH + 4);
            $by = $pdf->getY();
            $pdf->roundRectFill($x, $by, $cw, $boxH, 5, self::SOFT[0], self::SOFT[1], self::SOFT[2]);
            $pdf->fillRect($x, $by, 3, $boxH, self::ACCENT[0], self::ACCENT[1], self::ACCENT[2]);
            $pdf->setFont('B', 6);
            $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
            $pdf->text($x + 12, $by + 7, 'WORKED EXAMPLE');
            $pdf->setFont('B', 9.5);
            $pdf->setTextColor(self::DEEP[0], self::DEEP[1], self::DEEP[2]);
            $pdf->text($x + 12, $by + 18, $pdf->wrapLines($ex['title'], $cw - 24)[0]);
            $pdf->setY($by + 30);
            foreach ($ex['steps'] as $si => $step) {
                $lines = $pdf->wrapLines($step, $cw - 40);
                $sy = $pdf->getY();
                $pdf->circle($x + 18, $sy + 4, 4.4, [232, 240, 254]);
                $pdf->setFont('B', 5.8);
                $pdf->setTextColor(20, 80, 156);
                $pdf->textCenter($x + 18, $sy + 1.8, (string) ($si + 1));
                $pdf->setFont('', 8);
                $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
                foreach ($lines as $k => $ln) { $pdf->text($x + 28, $sy + $k * 9.6, $ln); }
                $pdf->setY($sy + count($lines) * 9.6 + 2);
            }
            $pdf->moveY(3);
            $pdf->setFont('B', 8);
            $pdf->setTextColor(15, 122, 82);
            foreach ($outcomeLines as $k => $ln) {
                $pdf->text($x + 12, $pdf->getY(), $ln);
                $pdf->moveY(9.6);
            }

            self::footer($pdf, $p['name']);
        }

        return $pdf->output();
    }
}
