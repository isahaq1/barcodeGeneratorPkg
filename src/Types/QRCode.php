<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

require_once __DIR__.'/../Utils/qrcode.php';

class QRCode implements BarcodeTypeInterface
{
    public function encode(string $data): Barcode
    {
        $qr = \QRCodeGenerator::factory();
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