<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Standard25 implements BarcodeTypeInterface
{
    // Patterns for digits 0-9 (bars/spaces, 5 elements each)
    private static array $patterns = [
        '0' => '00110', '1' => '10001', '2' => '01001', '3' => '11000', '4' => '00101',
        '5' => '10100', '6' => '01100', '7' => '00011', '8' => '10010', '9' => '01010',
    ];

    public function encode(string $data): Barcode
    {
        // Validate input: numeric
        if (!preg_match('/^\d+$/', $data)) {
            throw new \InvalidArgumentException('Standard 2 of 5 must be numeric');
        }
        $bars = [];
        // Start code (bar-space-bar-space, narrow-narrow-narrow-narrow)
        $bars[] = [1, 'black']; $bars[] = [1, 'white']; $bars[] = [1, 'black']; $bars[] = [1, 'white'];
        // Encode each digit
        for ($i = 0; $i < strlen($data); $i++) {
            $pattern = self::$patterns[$data[$i]];
            for ($j = 0; $j < 5; $j++) {
                $bars[] = [$pattern[$j] === '1' ? 2 : 1, $j % 2 === 0 ? 'black' : 'white'];
            }
        }
        // Stop code (bar-space-bar, wide-narrow-narrow)
        $bars[] = [1, 'black']; $bars[] = [1, 'white']; $bars[] = [2, 'black'];
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Standard25', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return preg_match('/^\d+$/', $data);
    }
} 