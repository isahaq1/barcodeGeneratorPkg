#!/usr/bin/env php
<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Isahaq\Barcode\Types\Code128;
use Isahaq\Barcode\Renderers\PNGRenderer;

$options = getopt('', ['type:', 'format:', 'data:', 'output:']);
$type = $options['type'] ?? 'code128';
$format = $options['format'] ?? 'png';
$data = $options['data'] ?? '1234567890';
$output = $options['output'] ?? null;

// For demo, only Code128 and PNGRenderer
$barcodeType = new Code128();
$renderer = new PNGRenderer();
$barcode = $barcodeType->encode($data);
$result = $renderer->render($barcode);

if ($output) {
    file_put_contents($output, $result);
    echo "Barcode saved to $output\n";
} else {
    echo $result;
} 