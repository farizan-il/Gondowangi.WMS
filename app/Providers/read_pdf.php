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

// Show full text
echo $text;
