<?php

require __DIR__ . '/../../vendor/autoload.php';

use Smalot\PdfParser\Parser;

$parser = new Parser();
$pdf = $parser->parseFile(__DIR__ . '/lanolin dan dex.pdf');
$rawText = $pdf->getText();

echo "=== RAW TEXT (first 1500 chars) ===\n";
echo substr($rawText, 0, 1500) . "\n\n";

// Apply same cleanup as controller
$text = preg_replace('/[\r\n\t]+/', ' ', $rawText);
$text = preg_replace('/\s{2,}/', ' ', $text);
$text = trim($text);

// Serial cleanup
$text = preg_replace('/([A-Z0-9])([.\-\/])\s+([A-Z0-9])/i', '$1$2$3', $text);
$text = preg_replace('/([A-Z]{2}\d{6,})\s+(\d{1,3})\b/i', '$1$2', $text);

// Qty split fix
$text = preg_replace('/([\d]+)\.\s+(\d{3},\d+)/', '$1.$2', $text);

echo "=== AFTER CLEANUP (search for item codes) ===\n";
// Search for [14046] dan [60014]
if (preg_match('/\[14046\]([^\[]{0,200})/', $text, $m)) {
    echo "[14046]: " . $m[1] . "\n\n";
}

if (preg_match('/\[60014\]([^\[]{0,200})/', $text, $m)) {
    echo "[60014]: " . $m[1] . "\n\n";
}

// Look for serial patterns
echo "=== SERIAL NUMBERS FOUND ===\n";
$serialPatterns = [
    '/\b(\d{2,3}\.[A-Z]\.[A-Z0-9]{6,})\b/i',
    '/\b(\d{10,})\b/',
    '/\b([0-9]{5,}[A-Z]{1,}[A-Z0-9]*|[A-Z]{1,}[0-9]{5,}[A-Z0-9]*)\b/i',
];

$allSerials = [];
foreach ($serialPatterns as $pattern) {
    if (preg_match_all($pattern, $text, $matches)) {
        $allSerials = array_merge($allSerials, $matches[1]);
    }
}
$allSerials = array_unique($allSerials);

foreach ($allSerials as $serial) {
    echo "  - $serial\n";
}
