<?php

require __DIR__ . '/../../vendor/autoload.php';

use Smalot\PdfParser\Parser;

$parser = new Parser();
$pdf = $parser->parseFile(__DIR__ . '/natamas bt dan tutup.pdf');
$rawText = $pdf->getText();

// Normalize
$text = preg_replace('/[\r\n\t]+/', ' ', $rawText);
$text = preg_replace('/\s{2,}/', ' ', $text);
$text = trim($text);

// Cleanup broken numbers
$text = preg_replace('/([A-Z0-9])([.\-\/])\s+([A-Z0-9])/i', '$1$2$3', $text);
$text = preg_replace('/([A-Z]{2}\d{6,})\s+(\d{1,3})\b/i', '$1$2', $text);

// Find section dengan [20008]
$start = strpos($text, '[20008]');
if ($start !== false) {
    echo "Found [20008] at position: $start\n\n";
    echo "Context (500 chars around):\n";
    echo substr($text, max(0, $start - 100), 600) . "\n\n";
}

// Find section dengan [20013]
$start2 = strpos($text, '[20013]');
if ($start2 !== false) {
    echo "Found [20013] at position: $start2\n\n";
    echo "Context (500 chars around):\n";
    echo substr($text, max(0, $start2 - 100), 600) . "\n\n";
}

echo "=== Searching for patterns ===\n";
// Try to extract with current patterns
$patterns = [
    'Pattern 1' => '/\[(\d+)\]\s+([A-Za-z0-9\s]+?)\s+\d+\s+([\d\.,]+)\s+Kg\s+Kg\s+([^\s]+)?/i',
    'Pattern 2' => '/\[(\d+)\]\s+([A-Za-z0-9\s]+?)\s+([\d\.,]+)\s+Kg\s+Kg\s+([^\s]+)?/i',
    'Pattern 3' => '/\[(\d+)\]\s+([A-Za-z0-9\s]+?)\s+([\d\.,]+)\s+Pcs\s+Pcs\s+([^\s]+)?/i',
];

foreach ($patterns as $name => $pattern) {
    if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
        echo "\n$name matched:\n";
        foreach ($matches as $match) {
            echo sprintf("  Code: %s, Name: %s, Qty: %s, Serial: %s\n",
                $match[1],
                trim($match[2]),
                $match[3],
                isset($match[4]) ? $match[4] : 'N/A'
            );
        }
    } else {
        echo "\n$name: NO MATCH\n";
    }
}
