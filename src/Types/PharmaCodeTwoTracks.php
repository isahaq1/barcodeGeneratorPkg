<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class PharmaCodeTwoTracks implements BarcodeTypeInterface
{
    public function encode(string $data): Barcode
    {
        // Validate input: integer 4-64570080
        if (!preg_match('/^\d+$/', $data) || (int)$data < 4 || (int)$data > 64570080) {
            throw new \InvalidArgumentException('PharmaCode Two-Track must be an integer between 4 and 64570080');
        }
        $value = (int)$data;
        $bars = [];
        // Two-track: encode as sequence of bar pairs (top, bottom, both, or none)
        // This is a simplified version for demonstration
        while ($value > 0) {
            $pair = $value % 4;
            if ($pair === 0) {
                $bars[] = [1, 'white']; // none
            } elseif ($pair === 1) {
                $bars[] = [1, 'black']; // top
            } elseif ($pair === 2) {
                $bars[] = [2, 'black']; // bottom
            } else {
                $bars[] = [3, 'black']; // both
            }
            $value = intdiv($value, 4);
            if ($value > 0) {
                $bars[] = [1, 'white'];
            }
        }
        $bars = array_reverse($bars);
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('PharmaCodeTwoTracks', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return preg_match('/^\d+$/', $data) && (int)$data >= 4 && (int)$data <= 64570080;
    }
} 