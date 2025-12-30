<?php

require __DIR__ . '/../../vendor/autoload.php';

use Smalot\PdfParser\Parser;

$parser = new Parser();
$pdf = $parser->parseFile(__DIR__ . '/natamas bt dan tutup.pdf');
$rawText = $pdf->getText();

echo "=== RAW TEXT (first 1000 chars) ===\n";
echo substr($rawText, 0, 1000) . "\n\n";

// Less aggressive normalization - keep some structure
$text = preg_replace('/[\r\n\t]+/', ' ', $rawText);
$text = preg_replace('/\s{2,}/', ' ', $text);
$text = trim($text);

// Cleanup serial numbers yang terpecah
$text = preg_replace('/([A-Z0-9])([.\-\/])\s+([A-Z0-9])/i', '$1$2$3', $text);
$text = preg_replace('/([A-Z]{2}\d{6,})\s+(\d{1,3})\b/i', '$1$2', $text);

// CRITICAL: Fix split quantities (e.g., "12. 320,0000" -> "12.320,0000")
$text = preg_replace('/([\d]+)\.\s+(\d{3},\d+)/', '$1.$2', $text);

echo "=== AFTER CLEANUP ===\n";
$pos = strpos($text, '[20008]');
if ($pos) {
    echo substr($text, $pos, 200) . "\n\n";
}

echo "=== Testing NEW Patterns ===\n";
// Pattern 1: Pcs specific
if (preg_match_all('/\[(\d+)\]\s+(.+?)\s+([\d\.,]+)\s+Pcs\s+Pcs\s+([^\s]+)?/i', $text, $matches, PREG_SET_ORDER)) {
    echo "Pcs Pattern matched:\n";
    foreach ($matches as $match) {
        $qtyString = str_replace('.', '', $match[3]);
        $qtyString = str_replace(',', '.', $qtyString);
        $quantity = floatval($qtyString);
        
        echo sprintf("  [%s] %s\n  Qty: %s (parsed: %.0f)\n  Serial: %s\n\n",
            $match[1],
            trim($match[2]),
            $match[3],
            $quantity,
            isset($match[4]) ? $match[4] : 'N/A'
        );
    }
} else {
    echo "Pcs Pattern: NO MATCH\n";
}
