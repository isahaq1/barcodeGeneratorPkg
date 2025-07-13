<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Aztec implements BarcodeTypeInterface
{
    public function encode(string $data, array $options = []): Barcode
    {
        // For demo: create a bullseye in the center and a checkerboard grid
        $size = 19; // Small Aztec for demo
        $bars = [];
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $dx = $x - intdiv($size, 2);
                $dy = $y - intdiv($size, 2);
                $dist = sqrt($dx * $dx + $dy * $dy);
                // Draw bullseye in center
                if (abs($dist - 2) < 1 || abs($dist - 4) < 1 || abs($dist - 6) < 1) {
                    $bars[] = [$x, $y, 'black'];
                } elseif (($x + $y) % 2 === 0 && $dist > 6) {
                    $bars[] = [$x, $y, 'black'];
                }
            }
        }
        // The Barcode class and renderer must be able to handle (x, y, color) bars for this to work.
        return new Barcode('Aztec', $data, $bars, $size);
    }

    public function validate(string $data): bool
    {
        return strlen($data) > 0;
    }
} 