<?php
/**
 * EnPharChem - PetroSA GTL Problem Statement PDF builder
 * ------------------------------------------------------
 * Renders the customer-facing problem-statement handbook: the GTL value chain,
 * the macro problems, then one section per module category giving the problem
 * statement, the use cases, and how to perform and analyse each one.
 *
 * Built on MiniPDF; no external dependencies. Follows the house style of
 * BusinessProcessPdfBuilder.
 */
require_once __DIR__ . '/MiniPDF.php';
require_once __DIR__ . '/PetroSAProblemStatement.php';

class PetroSAProblemStatementPdfBuilder
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
    const WARN   = [154, 92, 8];
    const WARNBG = [255, 248, 232];
    const GOOD   = [15, 122, 82];

    private static function footer($pdf, $left)
    {
        $pdf->setFont('', 6.6);
        $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
        $pdf->text($pdf->marginL, $pdf->pageH - 26, $left);
        $pdf->textRight($pdf->pageW - $pdf->marginR, $pdf->pageH - 26,
            COMPANY_NAME . '  |  ' . date('j F Y'));
    }

    private static function sectionTitle($pdf, $text)
    {
        $pdf->ensureSpace(34);
        $pdf->setFont('B', 14);
        $pdf->setTextColor(self::DEEP[0], self::DEEP[1], self::DEEP[2]);
        $pdf->text($pdf->marginL, $pdf->getY(), $text);
        $pdf->moveY(18);
        $pdf->fillRect($pdf->marginL, $pdf->getY(), $pdf->contentWidth(), 1.6,
            self::PRIM[0], self::PRIM[1], self::PRIM[2]);
        $pdf->moveY(12);
    }

    /** Small uppercase label above a block. */
    private static function label($pdf, $text)
    {
        $pdf->ensureSpace(14);
        $pdf->setFont('B', 6);
        $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
        $pdf->text($pdf->marginL, $pdf->getY(), $text);
        $pdf->moveY(9);
    }

    private static function paragraph($pdf, $text, $size = 8.4, $lh = 11.5)
    {
        $pdf->setFont('', $size);
        $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
        $pdf->writeWrapped($pdf->marginL, $pdf->contentWidth(), $lh, $text);
    }

    /** Bulleted list item with a square marker. */
    private static function bullet($pdf, $x, $w, $text, $colour = null)
    {
        $colour = $colour ?: self::PRIM;
        $pdf->setFont('', 8);
        $lines = $pdf->wrapLines($text, $w - 10);
        $pdf->ensureSpace(count($lines) * 9.6 + 3);
        $y = $pdf->getY();
        $pdf->fillRect($x, $y + 2.6, 2.6, 2.6, $colour[0], $colour[1], $colour[2]);
        $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
        foreach ($lines as $k => $ln) { $pdf->text($x + 8, $y + $k * 9.6, $ln); }
        $pdf->setY($y + count($lines) * 9.6 + 3);
    }

    /** Numbered step with a circular badge. */
    private static function step($pdf, $x, $w, $n, $text)
    {
        $pdf->setFont('', 8);
        $lines = $pdf->wrapLines($text, $w - 26);
        $pdf->ensureSpace(count($lines) * 9.6 + 4);
        $y = $pdf->getY();
        $pdf->circle($x + 6, $y + 4, 4.6, [232, 240, 254]);
        $pdf->setFont('B', 5.8);
        $pdf->setTextColor(20, 80, 156);
        $pdf->textCenter($x + 6, $y + 1.8, (string) $n);
        $pdf->setFont('', 8);
        $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
        foreach ($lines as $k => $ln) { $pdf->text($x + 17, $y + $k * 9.6, $ln); }
        $pdf->setY($y + count($lines) * 9.6 + 3);
    }

    public static function build()
    {
        $pdf = new MiniPDF();
        $ctx      = PetroSAProblemStatement::context();
        $core     = PetroSAProblemStatement::coreProblems();
        $modules  = PetroSAProblemStatement::modules();
        $chain    = $ctx['chain'];

        $useCaseCount = 0;
        foreach ($modules as $m) { $useCaseCount += count($m['useCases']); }

        // ================================================================ cover
        $w = $pdf->pageW;
        $pdf->fillRect(0, 0, $w, 320, self::DEEP[0], self::DEEP[1], self::DEEP[2]);
        $pdf->fillRect(0, 268, $w, 52, self::TINT[0], self::TINT[1], self::TINT[2]);
        $pdf->setFont('B', 8);
        $pdf->setTextColor(143, 199, 240);
        $pdf->text(46, 62, 'PREPARED FOR ' . strtoupper(PetroSAProblemStatement::CLIENT)
            . '  -  ' . strtoupper(PetroSAProblemStatement::ASSET));
        $pdf->setFont('B', 27);
        $pdf->setTextColor(255, 255, 255);
        $pdf->text(46, 88, 'GTL Refinery Problem');
        $pdf->text(46, 120, 'Statement &');
        $pdf->setTextColor(111, 227, 255);
        $pdf->text(46, 152, 'Use Case Handbook');
        $pdf->setFont('', 10);
        $pdf->setTextColor(214, 232, 248);
        $pdf->setY(196);
        $pdf->writeWrapped(46, $w - 150, 14, $ctx['headline'] . '. One problem statement per module '
            . 'area, each with worked use cases and the criteria to analyse them against.');

        $stats = [
            [(string) count($modules), 'MODULE AREAS'],
            [(string) $useCaseCount,   'USE CASES'],
            [(string) count($core),    'CORE PROBLEMS'],
            [(string) count($chain),   'PLANT AREAS'],
        ];
        $sx = 46;
        foreach ($stats as $s) {
            $pdf->setFont('B', 22);
            $pdf->setTextColor(111, 227, 255);
            $pdf->text($sx, 268, $s[0]);
            $pdf->setFont('', 7);
            $pdf->setTextColor(169, 203, 232);
            $pdf->text($sx, 296, $s[1]);
            $sx += 128;
        }
        $pdf->setFont('', 8);
        $pdf->setTextColor(150, 185, 220);
        $pdf->text(46, 314, COMPANY_NAME . '  -  Platform v' . APP_VERSION
            . '  -  Generated ' . date('j F Y'));
        $pdf->setY(344);

        // ------------------------------------------------------------ situation
        self::sectionTitle($pdf, 'The situation');
        foreach ($ctx['paragraphs'] as $p) {
            self::paragraph($pdf, $p);
            $pdf->moveY(5);
        }
        self::footer($pdf, 'GTL Refinery Problem Statement');

        // ----------------------------------------------------------- disclaimer
        $pdf->addPage();
        self::sectionTitle($pdf, 'Basis and limitations of this document');
        $dis = PetroSAProblemStatement::disclaimer();
        $pdf->setFont('', 8.2);
        $lines = $pdf->wrapLines($dis, $pdf->contentWidth() - 24);
        $boxH = count($lines) * 11 + 22;
        $pdf->ensureSpace($boxH + 6);
        $by = $pdf->getY();
        $pdf->roundRectFill($pdf->marginL, $by, $pdf->contentWidth(), $boxH, 5,
            self::WARNBG[0], self::WARNBG[1], self::WARNBG[2]);
        $pdf->fillRect($pdf->marginL, $by, 3, $boxH, self::WARN[0], self::WARN[1], self::WARN[2]);
        $pdf->setTextColor(self::WARN[0], self::WARN[1], self::WARN[2]);
        $pdf->setFont('B', 6);
        $pdf->text($pdf->marginL + 14, $by + 8, 'READ FIRST');
        $pdf->setFont('', 8.2);
        $pdf->setTextColor(90, 62, 12);
        $ly = $by + 20;
        foreach ($lines as $ln) { $pdf->text($pdf->marginL + 14, $ly, $ln); $ly += 11; }
        $pdf->setY($by + $boxH + 14);

        // --------------------------------------------------------- value chain
        self::sectionTitle($pdf, 'The GTL value chain');
        self::paragraph($pdf,
            'Each module area below is anchored to one or more of these plant areas, so a reader can '
          . 'go straight to the section covering the part of the refinery they own.');
        $pdf->moveY(6);
        $cw = $pdf->contentWidth();
        foreach ($chain as $i => $c) {
            $lines = $pdf->wrapLines($c['detail'], $cw - 150);
            $rowH = max(16, count($lines) * 9.6 + 6);
            $pdf->ensureSpace($rowH);
            $y = $pdf->getY();
            $pdf->roundRectFill($pdf->marginL, $y, 128, 14, 4, 232, 240, 254);
            $pdf->setFont('B', 7.4);
            $pdf->setTextColor(20, 80, 156);
            $pdf->text($pdf->marginL + 7, $y + 3.4,
                str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) . '  ' . $c['stage']);
            $pdf->setFont('', 8);
            $pdf->setTextColor(self::BODY[0], self::BODY[1], self::BODY[2]);
            foreach ($lines as $k => $ln) { $pdf->text($pdf->marginL + 140, $y + 3 + $k * 9.6, $ln); }
            $pdf->setY($y + $rowH);
        }
        self::footer($pdf, 'Basis, limitations and value chain');

        // -------------------------------------------------------- core problems
        $pdf->addPage();
        self::sectionTitle($pdf, 'The six core problems');
        self::paragraph($pdf,
            'The problems below are structural rather than incidental, and each is addressed by more '
          . 'than one module area. The right-hand column names where in this document each is answered.');
        $pdf->moveY(8);
        $cw = $pdf->contentWidth();
        $cols = [
            ['width' => $cw * 0.24, 'align' => 'L'],
            ['width' => $cw * 0.44, 'align' => 'L'],
            ['width' => $cw * 0.32, 'align' => 'L'],
        ];
        $rows = [];
        foreach ($core as $c) {
            $rows[] = [$c['title'], $c['detail'], $c['answered']];
        }
        $pdf->table($pdf->marginL, $cols, ['Core problem', 'Why it matters here', 'Answered in'], $rows, [
            'headerBg' => self::DEEP, 'bodyFS' => 8, 'headerFS' => 8, 'rowMinH' => 16, 'padding' => 5,
        ]);
        self::footer($pdf, 'Core problems');

        // ------------------------------------------------------ summary matrix
        $pdf->addPage();
        self::sectionTitle($pdf, 'Module areas at a glance');
        $cw = $pdf->contentWidth();
        $cols = [
            ['width' => $cw * 0.06, 'align' => 'C'],
            ['width' => $cw * 0.28, 'align' => 'L'],
            ['width' => $cw * 0.52, 'align' => 'L'],
            ['width' => $cw * 0.14, 'align' => 'C'],
        ];
        $rows = [];
        foreach ($modules as $i => $m) {
            $rows[] = [
                (string) ($i + 1),
                $m['category'],
                $m['useCases'][0]['name'],
                (string) count($m['useCases']),
            ];
        }
        $pdf->table($pdf->marginL, $cols,
            ['#', 'Module area', 'Headline use case', 'Cases'], $rows, [
            'headerBg' => self::DEEP, 'bodyFS' => 8, 'headerFS' => 8, 'rowMinH' => 16, 'padding' => 5,
        ]);
        $pdf->moveY(12);
        self::paragraph($pdf,
            'Each section that follows states the problem for that module area, lists the platform '
          . 'modules involved, then gives each use case with numbered steps to perform it and explicit '
          . 'criteria to analyse the result. Acceptance figures shown are illustrative defaults.', 8);
        self::footer($pdf, 'Module areas at a glance');

        // ------------------------------------------------- per module category
        foreach ($modules as $i => $m) {
            $pdf->addPage();
            $x = $pdf->marginL;
            $cw = $pdf->contentWidth();

            // header bar
            $pdf->roundRectFill($x, $pdf->getY(), $cw, 38, 5,
                self::DEEP[0], self::DEEP[1], self::DEEP[2]);
            $hy = $pdf->getY();
            $pdf->setFont('B', 16);
            $pdf->setTextColor(111, 227, 255);
            $pdf->text($x + 12, $hy + 11, str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT));
            $pdf->setFont('B', 12);
            $pdf->setTextColor(255, 255, 255);
            $pdf->text($x + 44, $hy + 9, $pdf->wrapLines($m['category'], $cw - 60)[0]);
            $pdf->setFont('', 7);
            $pdf->setTextColor(169, 203, 232);
            $pdf->text($x + 44, $hy + 24, $pdf->wrapLines($m['chain'], $cw - 60)[0]);
            $pdf->setY($hy + 48);

            // problem statement
            self::label($pdf, 'PROBLEM STATEMENT');
            self::paragraph($pdf, $m['problem'], 8.5, 11.6);
            $pdf->moveY(8);

            // platform modules used
            self::label($pdf, 'PLATFORM MODULES APPLIED');
            $pdf->setFont('', 7.8);
            $pdf->setTextColor(self::INK[0], self::INK[1], self::INK[2]);
            $pdf->writeWrapped($x, $cw, 10.4, implode('  -  ', $m['platformModules']));
            $pdf->moveY(10);

            // use cases
            foreach ($m['useCases'] as $ui => $uc) {
                $pdf->ensureSpace(70);

                // use case heading strip
                $pdf->setFont('B', 9.6);
                $ucLines = $pdf->wrapLines($uc['name'], $cw - 34);
                $stripH = count($ucLines) * 11.4 + 12;
                $y = $pdf->getY();
                $pdf->roundRectFill($x, $y, $cw, $stripH, 4, self::SOFT[0], self::SOFT[1], self::SOFT[2]);
                $pdf->fillRect($x, $y, 3, $stripH, self::ACCENT[0], self::ACCENT[1], self::ACCENT[2]);
                $pdf->setFont('B', 7);
                $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
                $pdf->text($x + 13, $y + 5, 'USE CASE ' . ($i + 1) . '.' . ($ui + 1));
                $pdf->setFont('B', 9.6);
                $pdf->setTextColor(self::DEEP[0], self::DEEP[1], self::DEEP[2]);
                $ly = $y + 15;
                foreach ($ucLines as $ln) { $pdf->text($x + 13, $ly, $ln); $ly += 11.4; }
                $pdf->setY($y + $stripH + 8);

                // objective
                $pdf->setFont('I', 8.2);
                $pdf->setTextColor(self::MUTED[0], self::MUTED[1], self::MUTED[2]);
                $pdf->writeWrapped($x + 4, $cw - 8, 11, 'Objective: ' . $uc['objective']);
                $pdf->moveY(7);

                // how to perform
                $pdf->ensureSpace(24);
                $pdf->setFont('B', 8.4);
                $pdf->setTextColor(self::PRIM[0], self::PRIM[1], self::PRIM[2]);
                $pdf->text($x + 4, $pdf->getY(), 'How to perform it');
                $pdf->moveY(13);
                foreach ($uc['perform'] as $si => $stepText) {
                    self::step($pdf, $x + 4, $cw - 8, $si + 1, $stepText);
                }
                $pdf->moveY(5);

                // how to analyse
                $pdf->ensureSpace(24);
                $pdf->setFont('B', 8.4);
                $pdf->setTextColor(self::GOOD[0], self::GOOD[1], self::GOOD[2]);
                $pdf->text($x + 4, $pdf->getY(), 'How to analyse the result');
                $pdf->moveY(13);
                foreach ($uc['analyse'] as $aText) {
                    self::bullet($pdf, $x + 4, $cw - 8, $aText, self::GOOD);
                }
                $pdf->moveY(4);

                // success criterion
                $pdf->setFont('B', 8);
                $kLines = $pdf->wrapLines('Success criterion: ' . $uc['kpi'], $cw - 26);
                $kH = count($kLines) * 10.4 + 12;
                $pdf->ensureSpace($kH + 6);
                $ky = $pdf->getY();
                $pdf->roundRectFill($x + 4, $ky, $cw - 8, $kH, 4, 235, 246, 240);
                $pdf->setTextColor(self::GOOD[0], self::GOOD[1], self::GOOD[2]);
                $kly = $ky + 7;
                foreach ($kLines as $ln) { $pdf->text($x + 14, $kly, $ln); $kly += 10.4; }
                $pdf->setY($ky + $kH + 12);
            }

            self::footer($pdf, $m['category']);
        }

        // ------------------------------------------------------------ closing
        $pdf->addPage();
        self::sectionTitle($pdf, 'How to use this handbook');
        self::paragraph($pdf,
            'This document is a working handbook, not a brochure. The intended use is one module area '
          . 'at a time, owned by the engineer or planner accountable for that part of the refinery.');
        $pdf->moveY(8);
        foreach ([
            'Start from the problem statement, not the software. If the problem as written does not '
                . 'match the refinery\'s actual situation, correct it before any tool is opened - a '
                . 'well-executed study of the wrong problem is still waste.',
            'Replace every illustrative acceptance figure with a verified plant number, a design basis '
                . 'value or the applicable specification. The figures in this document exist to show '
                . 'the method and must not be quoted as targets.',
            'Do the analysis steps as written, including the ones that check whether the result is '
                . 'trustworthy. Most of the analysis guidance here is about establishing whether a '
                . 'result can be relied on, which is the step most often skipped.',
            'Feed each result into the next module area rather than filing it. The feed forecast bounds '
                . 'the plan; the calibrated models bound the optimiser; the reconciled data bounds '
                . 'everything. These use cases are not independent.',
            'Record what did not work. A use case that failed for a stated reason is more valuable to '
                . 'the next engineer than one that quietly produced a number nobody trusts.',
        ] as $t) {
            self::bullet($pdf, $pdf->marginL, $pdf->contentWidth(), $t);
            $pdf->moveY(2);
        }
        $pdf->moveY(10);
        self::sectionTitle($pdf, 'Document control');
        $cw = $pdf->contentWidth();
        $pdf->table($pdf->marginL, [
            ['width' => $cw * 0.28, 'align' => 'L'],
            ['width' => $cw * 0.72, 'align' => 'L'],
        ], ['Field', 'Value'], [
            ['Document',      PetroSAProblemStatement::DOCTITLE],
            ['Prepared for',  PetroSAProblemStatement::CLIENT . ' - ' . PetroSAProblemStatement::ASSET],
            ['Prepared by',   COMPANY_NAME],
            ['Platform',      APP_NAME . ' v' . APP_VERSION],
            ['Module areas',  (string) count($modules) . ' categories covering the full module set'],
            ['Use cases',     (string) $useCaseCount . ' worked cases with perform and analyse guidance'],
            ['Generated',     date('j F Y')],
            ['Status',        'Vendor solution-fit assessment - figures illustrative, not endorsed by the named company'],
        ], ['headerBg' => self::DEEP, 'bodyFS' => 8, 'headerFS' => 8, 'rowMinH' => 15, 'padding' => 5]);
        self::footer($pdf, 'How to use this handbook');

        return $pdf->output();
    }
}
