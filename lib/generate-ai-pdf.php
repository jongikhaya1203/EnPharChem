<?php
/**
 * CLI helper: pre-generate the AI Use Cases PDF binary.
 * Usage: php lib/generate-ai-pdf.php
 */
require __DIR__ . '/AIUseCasesPDFBuilder.php';

$binary = AIUseCasesPDFBuilder::build();
$out = __DIR__ . '/../assets/pdfs/ai-use-cases.pdf';
if (!is_dir(dirname($out))) {
    mkdir(dirname($out), 0775, true);
}
file_put_contents($out, $binary);
echo "Wrote " . strlen($binary) . " bytes to {$out}\n";
echo "Header: " . substr($binary, 0, 8) . "\n";
