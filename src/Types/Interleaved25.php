<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Interleaved25 implements BarcodeTypeInterface
{
    // Patterns for digits 0-9 (bars/spaces, 5 elements each)
    private static array $patterns = [
        '0' => '00110', '1' => '10001', '2' => '01001', '3' => '11000', '4' => '00101',
        '5' => '10100', '6' => '01100', '7' => '00011', '8' => '10010', '9' => '01010',
    ];

    public function encode(string $data): Barcode
    {
        // Validate input: even number of digits, numeric
        if (!preg_match('/^\d+$/', $data) || strlen($data) % 2 !== 0) {
            throw new \InvalidArgumentException('Interleaved 2 of 5 must have an even number of digits');
        }
        $bars = [];
        // Start code (bar-space-bar-space, narrow-narrow-narrow-narrow)
        $bars[] = [1, 'black']; $bars[] = [1, 'white']; $bars[] = [1, 'black']; $bars[] = [1, 'white'];
        // Encode digits in pairs
        for ($i = 0; $i < strlen($data); $i += 2) {
            $a = self::$patterns[$data[$i]];
            $b = self::$patterns[$data[$i+1]];
            for ($j = 0; $j < 5; $j++) {
                // Bar from first digit, space from second
                $bars[] = [$a[$j] === '1' ? 2 : 1, 'black'];
                $bars[] = [$b[$j] === '1' ? 2 : 1, 'white'];
            }
        }
        // Stop code (bar-space-bar, wide-narrow-narrow)
        $bars[] = [1, 'black']; $bars[] = [1, 'white']; $bars[] = [2, 'black'];
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Interleaved25', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return preg_match('/^\d+$/', $data) && strlen($data) % 2 === 0;
    }
} 