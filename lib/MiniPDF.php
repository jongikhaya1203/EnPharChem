<?php
/**
 * MiniPDF — self-contained PDF writer for EnPharChem reports.
 * Standard Helvetica (no font embedding). Supports text, wrapped text,
 * rectangles (filled/stroked), simple tables, and multi-page output.
 *
 * Coordinate system inside helpers: x from left, y from top of page (in points).
 */
class MiniPDF {
    // A4 portrait, in PostScript points (1 pt = 1/72 in)
    public $pageW = 595.28;
    public $pageH = 841.89;
    public $marginL = 36;
    public $marginR = 36;
    public $marginT = 40;
    public $marginB = 40;

    private $pages = [];         // array of content stream strings
    private $current = '';       // current page content stream
    private $y;                  // y cursor from top (points)
    private $fontKey = 'F1';     // F1 Helvetica, F2 Helvetica-Bold, F3 Helvetica-Oblique
    private $fontSize = 10;
    private $fontColor = '0 0 0';

    // Approximate glyph widths (Helvetica) — used for wrapping/centering
    private static $widths = [
        ' ' => 278, '!' => 278, '"' => 355, '#' => 556, '$' => 556, '%' => 889, '&' => 667, "'" => 191,
        '(' => 333, ')' => 333, '*' => 389, '+' => 584, ',' => 278, '-' => 333, '.' => 278, '/' => 278,
        '0' => 556, '1' => 556, '2' => 556, '3' => 556, '4' => 556, '5' => 556, '6' => 556, '7' => 556,
        '8' => 556, '9' => 556, ':' => 278, ';' => 278, '<' => 584, '=' => 584, '>' => 584, '?' => 556,
        '@' => 1015, 'A' => 667, 'B' => 667, 'C' => 722, 'D' => 722, 'E' => 667, 'F' => 611, 'G' => 778,
        'H' => 722, 'I' => 278, 'J' => 500, 'K' => 667, 'L' => 556, 'M' => 833, 'N' => 722, 'O' => 778,
        'P' => 667, 'Q' => 778, 'R' => 722, 'S' => 667, 'T' => 611, 'U' => 722, 'V' => 667, 'W' => 944,
        'X' => 667, 'Y' => 667, 'Z' => 611, '[' => 278, '\\' => 278, ']' => 278, '^' => 469, '_' => 556,
        '`' => 333, 'a' => 556, 'b' => 556, 'c' => 500, 'd' => 556, 'e' => 556, 'f' => 278, 'g' => 556,
        'h' => 556, 'i' => 222, 'j' => 222, 'k' => 500, 'l' => 222, 'm' => 833, 'n' => 556, 'o' => 556,
        'p' => 556, 'q' => 556, 'r' => 333, 's' => 500, 't' => 278, 'u' => 556, 'v' => 500, 'w' => 722,
        'x' => 500, 'y' => 500, 'z' => 500, '{' => 334, '|' => 260, '}' => 334, '~' => 584,
    ];

    public function __construct() {
        $this->addPage();
    }

    public function addPage() {
        if ($this->current !== '') {
            $this->pages[] = $this->current;
        }
        $this->current = '';
        $this->y = $this->marginT;
    }

    public function getY() { return $this->y; }
    public function setY($y) { $this->y = $y; }
    public function moveY($dy) { $this->y += $dy; }

    public function contentWidth() {
        return $this->pageW - $this->marginL - $this->marginR;
    }

    public function setFont($style = '', $size = 10) {
        // style: '' regular, 'B' bold, 'I' italic
        $map = ['' => 'F1', 'B' => 'F2', 'I' => 'F3'];
        $this->fontKey = $map[$style] ?? 'F1';
        $this->fontSize = $size;
    }

    public function setTextColor($r, $g, $b) {
        $this->fontColor = sprintf('%.3f %.3f %.3f', $r/255, $g/255, $b/255);
    }

    /**
     * Approx width of a string in current font size (points).
     */
    public function stringWidth($s) {
        $w = 0;
        $len = strlen($s);
        for ($i = 0; $i < $len; $i++) {
            $ch = $s[$i];
            $w += self::$widths[$ch] ?? 500;
        }
        return $w * $this->fontSize / 1000;
    }

    /**
     * Draws a single line of text at (x_from_left, y_from_top).
     */
    public function text($x, $y, $string) {
        $py = $this->pageH - $y;          // convert top-origin to PDF bottom-origin
        $esc = $this->escape($string);
        $this->current .= "BT\n";
        $this->current .= "{$this->fontColor} rg\n";
        $this->current .= "/{$this->fontKey} {$this->fontSize} Tf\n";
        $this->current .= sprintf("%.2f %.2f Td\n", $x, $py - $this->fontSize); // baseline below top
        $this->current .= "({$esc}) Tj\n";
        $this->current .= "ET\n";
    }

    /**
     * Writes a paragraph at current Y, wrapping inside the given width.
     * Advances Y. Returns final Y.
     */
    public function writeWrapped($x, $maxWidth, $lineHeight, $string) {
        $words = preg_split('/\s+/', trim($string));
        $line = '';
        foreach ($words as $w) {
            $candidate = $line === '' ? $w : ($line . ' ' . $w);
            if ($this->stringWidth($candidate) <= $maxWidth) {
                $line = $candidate;
            } else {
                if ($line !== '') {
                    $this->ensureSpace($lineHeight);
                    $this->text($x, $this->y, $line);
                    $this->y += $lineHeight;
                }
                $line = $w;
            }
        }
        if ($line !== '') {
            $this->ensureSpace($lineHeight);
            $this->text($x, $this->y, $line);
            $this->y += $lineHeight;
        }
        return $this->y;
    }

    /**
     * Wraps a string into an array of lines fitting maxWidth (current font).
     */
    public function wrapLines($string, $maxWidth) {
        $words = preg_split('/\s+/', trim((string)$string));
        $lines = [];
        $line = '';
        foreach ($words as $w) {
            $candidate = $line === '' ? $w : ($line . ' ' . $w);
            if ($this->stringWidth($candidate) <= $maxWidth) {
                $line = $candidate;
            } else {
                if ($line !== '') $lines[] = $line;
                // hard-break any single word longer than maxWidth
                while ($this->stringWidth($w) > $maxWidth && strlen($w) > 1) {
                    $cut = strlen($w);
                    while ($cut > 1 && $this->stringWidth(substr($w, 0, $cut)) > $maxWidth) $cut--;
                    $lines[] = substr($w, 0, $cut);
                    $w = substr($w, $cut);
                }
                $line = $w;
            }
        }
        if ($line !== '') $lines[] = $line;
        return $lines;
    }

    /**
     * Filled rectangle in mm of room: (x, y from top, width, height) plus 0..255 RGB.
     */
    public function fillRect($x, $y, $w, $h, $r, $g, $b) {
        $py = $this->pageH - $y - $h;
        $this->current .= sprintf("%.3f %.3f %.3f rg\n", $r/255, $g/255, $b/255);
        $this->current .= sprintf("%.2f %.2f %.2f %.2f re f\n", $x, $py, $w, $h);
    }

    public function strokeRect($x, $y, $w, $h, $r, $g, $b, $lineWidth = 0.5) {
        $py = $this->pageH - $y - $h;
        $this->current .= sprintf("%.3f %.3f %.3f RG\n", $r/255, $g/255, $b/255);
        $this->current .= sprintf("%.2f w\n", $lineWidth);
        $this->current .= sprintf("%.2f %.2f %.2f %.2f re S\n", $x, $py, $w, $h);
    }

    public function line($x1, $y1, $x2, $y2, $r = 0, $g = 0, $b = 0, $lineWidth = 0.5) {
        $py1 = $this->pageH - $y1;
        $py2 = $this->pageH - $y2;
        $this->current .= sprintf("%.3f %.3f %.3f RG\n", $r/255, $g/255, $b/255);
        $this->current .= sprintf("%.2f w\n", $lineWidth);
        $this->current .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $x1, $py1, $x2, $py2);
    }

    /**
     * Ensures there's room for `needed` height before drawing; otherwise adds a page.
     */
    public function ensureSpace($needed) {
        if ($this->y + $needed > $this->pageH - $this->marginB) {
            $this->addPage();
        }
    }

    /**
     * Draws a table. $columns = [['width'=>pt, 'align'=>'L'/'C'/'R'], ...]
     * $headers = [string, ...]; $rows = [[string,...], ...]
     */
    public function table($x, $columns, $headers, $rows, $opts = []) {
        $headerBg     = $opts['headerBg']     ?? [13, 40, 71];     // dark blue
        $headerFg     = $opts['headerFg']     ?? [255, 255, 255];
        $rowAltBg     = $opts['rowAltBg']     ?? [248, 250, 252];
        $borderColor  = $opts['borderColor']  ?? [226, 232, 240];
        $padding      = $opts['padding']      ?? 4.0;
        $rowMinH      = $opts['rowMinH']      ?? 14.0;
        $headerFS     = $opts['headerFS']     ?? 9;
        $bodyFS       = $opts['bodyFS']       ?? 9;
        $textColors   = $opts['textColors']   ?? null; // optional per-column [r,g,b]

        // --- header row ---
        $this->setFont('B', $headerFS);
        $headerH = $rowMinH;
        $this->ensureSpace($headerH);
        // background
        $totalW = 0; foreach ($columns as $c) $totalW += $c['width'];
        $this->fillRect($x, $this->y, $totalW, $headerH, $headerBg[0], $headerBg[1], $headerBg[2]);
        // text
        $this->setTextColor($headerFg[0], $headerFg[1], $headerFg[2]);
        $cx = $x;
        foreach ($columns as $i => $col) {
            $txt = $headers[$i] ?? '';
            $tw = $this->stringWidth($txt);
            $align = $col['align'] ?? 'L';
            if ($align === 'C')      $tx = $cx + ($col['width'] - $tw) / 2;
            else if ($align === 'R') $tx = $cx + $col['width'] - $tw - $padding;
            else                     $tx = $cx + $padding;
            $this->text($tx, $this->y + ($headerH - $headerFS) / 2 + 1, $txt);
            $cx += $col['width'];
        }
        $this->y += $headerH;

        // --- body rows ---
        $this->setFont('', $bodyFS);
        $alt = false;
        foreach ($rows as $row) {
            // compute wrapped lines per cell
            $wrapped = [];
            $maxLines = 1;
            foreach ($columns as $i => $col) {
                $cellW = $col['width'] - $padding * 2;
                $lines = $this->wrapLines((string)($row[$i] ?? ''), $cellW);
                if (empty($lines)) $lines = [''];
                $wrapped[$i] = $lines;
                if (count($lines) > $maxLines) $maxLines = count($lines);
            }
            $rowH = max($rowMinH, $maxLines * ($bodyFS + 2) + $padding * 1.5);
            $this->ensureSpace($rowH);

            // alternate row background
            if ($alt) {
                $this->fillRect($x, $this->y, $totalW, $rowH, $rowAltBg[0], $rowAltBg[1], $rowAltBg[2]);
            }
            // bottom border
            $this->line($x, $this->y + $rowH, $x + $totalW, $this->y + $rowH,
                        $borderColor[0], $borderColor[1], $borderColor[2], 0.4);

            // cell text
            $cx = $x;
            foreach ($columns as $i => $col) {
                $lines = $wrapped[$i];
                $align = $col['align'] ?? 'L';
                if ($textColors && isset($textColors[$i])) {
                    $tc = $textColors[$i];
                    $this->setTextColor($tc[0], $tc[1], $tc[2]);
                } else {
                    $this->setTextColor(30, 41, 59);
                }
                $ly = $this->y + $padding;
                foreach ($lines as $line) {
                    $tw = $this->stringWidth($line);
                    if ($align === 'C')      $tx = $cx + ($col['width'] - $tw) / 2;
                    else if ($align === 'R') $tx = $cx + $col['width'] - $tw - $padding;
                    else                     $tx = $cx + $padding;
                    $this->text($tx, $ly, $line);
                    $ly += $bodyFS + 2;
                }
                $cx += $col['width'];
            }
            $this->y += $rowH;
            $alt = !$alt;
        }
        $this->setTextColor(0, 0, 0);
    }

    /**
     * Escapes a string for inclusion in a PDF literal.
     */
    private function escape($s) {
        // Encode to WinAnsi-ish (latin-1). Replace common smart quotes / dashes / bullets.
        $repl = [
            "\xE2\x80\x90" => '-', "\xE2\x80\x91" => '-',
            "\xE2\x80\x92" => '-', "\xE2\x80\x93" => '-', "\xE2\x80\x94" => '-',
            "\xE2\x80\x98" => "'", "\xE2\x80\x99" => "'",
            "\xE2\x80\x9C" => '"', "\xE2\x80\x9D" => '"',
            "\xE2\x80\xA2" => '*', "\xE2\x80\xA6" => '...',
            "\xC2\xA0" => ' ',
            "\xC2\xB7" => '*',
        ];
        $s = strtr($s, $repl);
        // Try to convert any remaining UTF-8 to Windows-1252; fall back to ASCII
        if (function_exists('mb_convert_encoding')) {
            $s = @mb_convert_encoding($s, 'Windows-1252', 'UTF-8');
        } else {
            $s = @iconv('UTF-8', 'Windows-1252//TRANSLIT', $s) ?: $s;
        }
        $s = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);
        // Strip any remaining control chars
        $s = preg_replace('/[\x00-\x08\x0B-\x1F]/', '', $s);
        return $s;
    }

    /**
     * Finalize and return the binary PDF document.
     */
    public function output() {
        // Flush current page
        if ($this->current !== '') {
            $this->pages[] = $this->current;
            $this->current = '';
        }

        $objects = [];
        $add = function($body) use (&$objects) {
            $id = count($objects) + 1;
            $objects[$id] = $body;
            return $id;
        };

        // Reserve catalog + pages first
        $catalogId = $add('');     // placeholder
        $pagesId   = $add('');     // placeholder

        // Fonts
        $f1 = $add("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>");
        $f2 = $add("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>");
        $f3 = $add("<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Oblique /Encoding /WinAnsiEncoding >>");

        // Pages
        $pageIds = [];
        foreach ($this->pages as $stream) {
            $contentId = $add("<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream");
            $pageBody = "<< /Type /Page /Parent {$pagesId} 0 R "
                      . "/MediaBox [0 0 " . sprintf('%.2f %.2f', $this->pageW, $this->pageH) . "] "
                      . "/Resources << /Font << /F1 {$f1} 0 R /F2 {$f2} 0 R /F3 {$f3} 0 R >> >> "
                      . "/Contents {$contentId} 0 R >>";
            $pageIds[] = $add($pageBody);
        }

        $kids = implode(' ', array_map(fn($id) => "$id 0 R", $pageIds));
        $objects[$pagesId] = "<< /Type /Pages /Count " . count($pageIds) . " /Kids [{$kids}] >>";
        $objects[$catalogId] = "<< /Type /Catalog /Pages {$pagesId} 0 R >>";

        // Assemble
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];
        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$body}\nendobj\n";
        }
        $xrefOffset = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 {$count}\n0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size {$count} /Root {$catalogId} 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }
}
