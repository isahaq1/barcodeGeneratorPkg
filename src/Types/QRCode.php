<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

require_once __DIR__.'/../Utils/qrcode.php';

class QRCode implements BarcodeTypeInterface
{
    /**
     * Options:
     * - 'version': QR version (1-10, default 1)
     * - 'ecc': Error correction level ('L', 'M', 'Q', 'H', default 'L')
     * - 'margin': Margin in modules (default 4)
     */
    public function encode(string $data, array $options = []): Barcode
    {
        $version = isset($options['version']) ? (int)$options['version'] : 1;
        $eccMap = ['L' => 1, 'M' => 0, 'Q' => 3, 'H' => 2];
        $ecc = isset($options['ecc']) ? strtoupper($options['ecc']) : 'L';
        $eccLevel = $eccMap[$ecc] ?? 1;
        $margin = isset($options['margin']) ? (int)$options['margin'] : 4;

        $qr = \QRCodeGenerator::factory();
        // Set typeNumber (version) via reflection
        $ref = new \ReflectionClass($qr);
        $propType = $ref->getProperty('typeNumber');
        $propType->setAccessible(true);
        $propType->setValue($qr, $version);
        // Set errorCorrectLevel via reflection
        $propEcc = $ref->getProperty('errorCorrectLevel');
        $propEcc->setAccessible(true);
        $propEcc->setValue($qr, $eccLevel);
        $qr->margin = $margin;
        $qr->addData($data);
        $qr->make();
        $size = $qr->getModuleCount();
        $bars = [];
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $bars[] = [1, $qr->isDark($x, $y) ? 'black' : 'white'];
            }
        }
        $width = $size * $size;
        return new Barcode('QRCode', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return is_string($data) && strlen($data) > 0;
    }
} 