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

echo "=== Testing NEW Patterns (with Pcs support) ===\n\n";

$patterns = [
    'Pattern 1' => '/\[(\d+)\]\s+([A-Za-z0-9\s]+?)\s+\d+\s+([\d\.,]+)\s+(Kg|Pcs|Ltr)\s+\4\s+([^\s]+)?/i',
    'Pattern 2' => '/\[(\d+)\]\s+([A-Za-z0-9\s]+?)\s+([\d\.,]+)\s+(Kg|Pcs|Ltr)\s+\4\s+([^\s]+)?/i',
];

foreach ($patterns as $name => $pattern) {
    if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
        echo "$name matched:\n";
        foreach ($matches as $match) {
            $qtyString = str_replace('.', '', $match[3]); // Hapus separator ribuan
            $qtyString = str_replace(',', '.', $qtyString); // Koma -> titik desimal
            $quantity = floatval($qtyString);
            
            echo sprintf("  Code: %s\n  Name: %s\n  UoM: %s\n  Qty: %s (parsed: %.2f)\n  Serial: %s\n\n",
                $match[1],
                trim($match[2]),
                $match[4],
                $match[3],
                $quantity,
                isset($match[5]) ? $match[5] : 'N/A'
            );
        }
        break; // Stop after first match
    } else {
        echo "$name: NO MATCH\n\n";
    }
}
