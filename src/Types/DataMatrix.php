<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class DataMatrix implements BarcodeTypeInterface
{
    public function encode(string $data, array $options = []): Barcode
    {
        // For demo: create a checkerboard pattern
        $size = 14; // Small DataMatrix for demo
        $bars = [];
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if (($x + $y) % 2 === 0) {
                    $bars[] = [$x, $y, 'black'];
                }
            }
        }
        // The Barcode class and renderer must be able to handle (x, y, color) bars for this to work.
        return new Barcode('DataMatrix', $data, $bars, $size);
    }

    public function validate(string $data): bool
    {
        return strlen($data) > 0;
    }
} 