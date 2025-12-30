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

// Cleanup
$text = preg_replace('/([A-Z0-9])([.\-\/])\s+([A-Z0-9])/i', '$1$2$3', $text);
$text = preg_replace('/([A-Z]{2}\d{6,})\s+(\d{1,3})\b/i', '$1$2', $text);

echo "=== Find [20008] section ===\n";
$pos = strpos($text, '[20008]');
echo substr($text, $pos, 150) . "\n\n";

echo "=== Testing SIMPLE Patterns ===\n\n";

$patterns = [
    'Pattern A' => '/\[(\d+)\]\s+([^\d]+?)\s+([\d\.,]+)\s+Pcs\s+Pcs\s+([^\s]+)?/i',
    'Pattern B' => '/\[(\d+)\]\s+(.+?)\s+([\d\.,]+)\s+(Kg|Pcs|Ltr)\s+(Kg|Pcs|Ltr)/i',
];

foreach ($patterns as $name => $pattern) {
    if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
        echo "$name matched " . count($matches) . " items:\n";
        foreach ($matches as $match) {
            print_r($match);
            echo "\n";
        }
        break;
    } else {
        echo "$name: NO MATCH\n\n";
    }
}
