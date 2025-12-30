#!/usr/bin/env php
<?php

/**
 * Quick PDF Test Script
 * Test all PDF samples and display extracted data
 */

require __DIR__ . '/../../vendor/autoload.php';

use Smalot\PdfParser\Parser;

$pdfs = [
    '14063 Propylene Glycol USP.pdf',
    'dus satuan 112222.pdf',
    'lanolin dan dex.pdf',
    'natamas bt dan tutup.pdf'
];

$parser = new Parser();

foreach ($pdfs as $pdfFile) {
    $filePath = __DIR__ . '/' . $pdfFile;
    
    echo "\n" . str_repeat('=', 80) . "\n";
    echo "Testing: {$pdfFile}\n";
    echo str_repeat('=', 80) . "\n";
    
    try {
        $pdf = $parser->parseFile($filePath);
        $rawText = $pdf->getText();
        
        // Normalize
        $text = preg_replace('/[\r\n\t]+/', ' ', $rawText);
        $text = preg_replace('/\s{2,}/', ' ', $text);
        $text = trim($text);
        
        // Cleanup serial numbers yang terpecah
        $text = preg_replace('/([A-Z0-9])([.\-\/])\s+([A-Z0-9])/i', '$1$2$3', $text);
        $text = preg_replace('/([A-Z]{2}\d{6,})\s+(\d{1,3})\b/i', '$1$2', $text);
        
        // Extract supplier
        if (preg_match('/Supplier\s+([A-Za-z0-9\s,\.]+?)(?=\s+Invoice|\s+Stock|\s+Truck|\s+PO)/i', $text, $match)) {
            echo "Supplier: " . trim($match[1]) . "\n";
        }
        
        // Extract PO
        if (preg_match('/\b(PO\d+)\b/i', $text, $match)) {
            echo "PO Number: {$match[1]}\n";
        }
        
        // Extract serial numbers
        echo "\nSerial Numbers Found:\n";
        $serialPatterns = [
            '/\b(\d{2,3}\.[A-Z]\.[A-Z0-9]{6,})\b/i',
            '/\b(\d{10,})\b/',
            '/\b([0-9]{5,}[A-Z]{1,}[A-Z0-9]*|[A-Z]{1,}[0-9]{5,}[A-Z0-9]*)\b/i',
        ];
        
        $serialNumbers = [];
        foreach ($serialPatterns as $pattern) {
            if (preg_match_all($pattern, $text, $matches)) {
                foreach ($matches[1] as $serial) {
                    if (!preg_match('/[\d,\.]{4,}\s+(Kg|Pcs|Ltr)/i', $serial)) {
                        $serialNumbers[] = trim($serial);
                    }
                }
            }
        }
        
        $serialNumbers = array_unique($serialNumbers);
        
        // Apply blacklist filter
        $filtered = [];
        foreach ($serialNumbers as $serial) {
            if (preg_match('/^PO\d+$/i', $serial)) {
                echo "  [FILTERED] PO: {$serial}\n";
                continue;
            }
            if (preg_match('/^IN[\/]?\d+$/i', $serial)) {
                echo "  [FILTERED] IN: {$serial}\n";
                continue;
            }
            if (preg_match('/^\d{8}$/', $serial)) {
                echo "  [FILTERED] Date: {$serial}\n";
                continue;
            }
            if (preg_match('/,\d{2,}/', $serial)) {
                echo "  [FILTERED] Qty: {$serial}\n";
                continue;
            }
            
            $filtered[] = $serial;
            echo "  ✓ VALID: {$serial}\n";
        }
        
        echo "\nValid Serial Count: " . count($filtered) . "\n";
        
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "Test Complete!\n";
echo str_repeat('=', 80) . "\n";
