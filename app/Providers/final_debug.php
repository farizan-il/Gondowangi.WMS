<?php

require __DIR__ . '/../../vendor/autoload.php';

use Smalot\PdfParser\Parser;

$parser = new Parser();
$pdf = $parser->parseFile(__DIR__ . '/natamas bt dan tutup.pdf');
$rawText = $pdf->getText();

// EXACT SAME AS CONTROLLER
$text = preg_replace('/[\r\n\t]+/', ' ', $rawText);
$text = preg_replace('/\s{2,}/', ' ', $text);
$text = trim($text);
$text = preg_replace('/([A-Z0-9])([.\-\/])\s+([A-Z0-9])/i', '$1$2$3', $text);
$text = preg_replace('/([A-Z]{2}\d{6,})\s+(\d{1,3})\b/i', '$1$2', $text);
$text = preg_replace('/([\d]+)\.\s+(\d{3},\d+)/', '$1.$2', $text); // Qty fix

echo "=== TEXT AROUND [20008] ===\n";
$pos = strpos($text, '[20008]');
if ($pos) {
    echo substr($text, max(0, $pos-50), 300) . "\n\n";
}

echo "=== PATTERN TEST ===\n";
// Pattern dari controller
$pattern = '/\[(\d+)\]\s+(.+?)\s+([\d\.,]+)\s+Pcs\s+Pcs\s+([^\s]+)?/i';

if (preg_match($pattern, $text, $match)) {
    echo "MATCH! Result:\n";
    print_r($match);
} else {
    echo "NO MATCH!\n\n";
    
    // Try simpler patterns
    echo "Testing simpler patterns:\n";
    
    if (preg_match('/\[20008\]\s+(.{0,100})Pcs/i', $text, $m)) {
        echo "Found Pcs after [20008]:\n$m[1]Pcs\n\n";
    }
    
    if (preg_match('/\[20008\]([^\[]+)/', $text, $m)) {
        echo "Full text between [20008] and next bracket:\n$m[1]\n";
    }
}
