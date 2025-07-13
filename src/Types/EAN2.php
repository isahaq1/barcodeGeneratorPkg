<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class EAN2 implements BarcodeTypeInterface
{
    // EAN-2 encoding patterns (L and G)
    private static $patterns = [
        'L' => [
            '0' => '0001101', '1' => '0011001', '2' => '0010011', '3' => '0111101',
            '4' => '0100011', '5' => '0110001', '6' => '0101111', '7' => '0111011',
            '8' => '0110111', '9' => '0001011'
        ],
        'G' => [
            '0' => '0100111', '1' => '0110011', '2' => '0011011', '3' => '0100001',
            '4' => '0011101', '5' => '0111001', '6' => '0000101', '7' => '0010001',
            '8' => '0001001', '9' => '0010111'
        ]
    ];

    // Parity patterns for EAN-2
    private static $parity = [
        0 => ['G', 'G'],
        1 => ['G', 'L'],
        2 => ['L', 'G'],
        3 => ['L', 'L'],
    ];

    public function encode(string $data): Barcode
    {
        if (!preg_match('/^[0-9]{2}$/', $data)) {
            throw new \InvalidArgumentException('EAN2 must be exactly 2 digits');
        }
        $parityIndex = intval($data) % 4;
        $parity = self::$parity[$parityIndex];

        // Start pattern for EAN-2
        $bars = [
            [1, 'black'], [1, 'white'], [1, 'black'], [2, 'white'],
        ];

        for ($i = 0; $i < 2; $i++) {
            $digit = $data[$i];
            $pattern = self::$patterns[$parity[$i]][$digit];
            foreach (str_split($pattern) as $bit) {
                $bars[] = [1, $bit === '1' ? 'black' : 'white'];
            }
            if ($i === 0) {
                // Separator between digits
                $bars[] = [1, 'white'];
                $bars[] = [1, 'black'];
            }
        }

        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('EAN2', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return preg_match('/^[0-9]{2}$/', $data);
    }
} 