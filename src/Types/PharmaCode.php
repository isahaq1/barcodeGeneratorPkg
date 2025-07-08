<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class PharmaCode implements BarcodeTypeInterface
{
    public function encode(string $data): Barcode
    {
        // Validate input: integer 3-131070
        if (!preg_match('/^\d+$/', $data) || (int)$data < 3 || (int)$data > 131070) {
            throw new \InvalidArgumentException('PharmaCode must be an integer between 3 and 131070');
        }
        $value = (int)$data;
        $bars = [];
        // PharmaCode: encode as sequence of narrow/wide bars (right to left, LSB first)
        while ($value > 0) {
            if ($value % 2 === 0) {
                $bars[] = [2, 'black']; // wide bar
                $value = ($value - 2) / 2;
            } else {
                $bars[] = [1, 'black']; // narrow bar
                $value = ($value - 1) / 2;
            }
            // Add white space after each bar except last
            if ($value > 0) {
                $bars[] = [1, 'white'];
            }
        }
        // Reverse to get left-to-right order
        $bars = array_reverse($bars);
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('PharmaCode', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return preg_match('/^\d+$/', $data) && (int)$data >= 3 && (int)$data <= 131070;
    }
} 