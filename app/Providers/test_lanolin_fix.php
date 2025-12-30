<?php

require __DIR__ . '/../../vendor/autoload.php';

use Smalot\PdfParser\Parser;

$parser = new Parser();
$pdf = $parser->parseFile(__DIR__ . '/lanolin dan dex.pdf');
$rawText = $pdf->getText();

// Apply EXACT same cleanup as controller (with NEW Pattern 3)
$text = preg_replace('/[\r\n\t]+/', ' ', $rawText);
$text = preg_replace('/\s{2,}/', ' ', $text);
$text = trim($text);

// Serial cleanup - WITH NEW PATTERN 3
$text = preg_replace('/([A-Z0-9])([.\-\/])\s+([A-Z0-9])/i', '$1$2$3', $text);  // Pattern 1
$text = preg_replace('/([A-Z]{2}\d{6,})\s+(\d{1,3})\b/i', '$1$2', $text);      // Pattern 2
$text = preg_replace('/(\d{2,3}\.[A-Z]\.[A-Z0-9]+)\s+(\d{1,2})\b/i', '$1-$2', $text);  // Pattern 3 NEW!

// Qty split fix
$text = preg_replace('/([\d]+)\.\s+(\d{3},\d+)/', '$1.$2', $text);

echo "=== TESTING NEW PATTERN 3 ===\n\n";

// Look for serial patterns AFTER cleanup
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

echo "Serial numbers found AFTER cleanup:\n";
foreach ($allSerials as $serial) {
    if (strpos($serial, '28.D') !== false || strpos($serial, '27.H') !== false) {
        echo "  ✓ $serial\n";
    }
}

echo "\n=== Items extraction test ===\n";
// Test Pattern 2 (fallback)
if (preg_match_all('/\[(\d+)\]\s+([^\[]+?)(?=\[|$)/i', $text, $matches, PREG_SET_ORDER)) {
    foreach ($matches as $match) {
        if ($match[1] == '14046' || $match[1] == '60014') {
            echo "\n[{$match[1]}]:\n";
            echo "  Text: " . substr($match[2], 0, 100) . "...\n";
            
            // Extract serial from text
            if (preg_match('/([A-Z0-9]{8,})$/i', trim($match[2]), $sm)) {
                echo "  Serial: {$sm[1]}\n";
            }
        }
    }
}
