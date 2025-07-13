<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class EAN5 implements BarcodeTypeInterface
{
    // EAN-5 encoding patterns (L and G)
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

    // Parity patterns for EAN-5
    private static $parity = [
        0 => ['B', 'A', 'A', 'A', 'A'],
        1 => ['A', 'B', 'A', 'A', 'A'],
        2 => ['A', 'A', 'B', 'A', 'A'],
        3 => ['A', 'A', 'A', 'B', 'A'],
        4 => ['A', 'A', 'A', 'A', 'B'],
        5 => ['B', 'B', 'A', 'A', 'A'],
        6 => ['B', 'A', 'B', 'A', 'A'],
        7 => ['B', 'A', 'A', 'B', 'A'],
        8 => ['B', 'A', 'A', 'A', 'B'],
        9 => ['A', 'B', 'B', 'A', 'A']
    ];

    public function encode(string $data): Barcode
    {
        if (!preg_match('/^[0-9]{5}$/', $data)) {
            throw new \InvalidArgumentException('EAN5 must be exactly 5 digits');
        }

        // Calculate checksum for parity
        $checksum = (
            3 * (intval($data[0]) + intval($data[2]) + intval($data[4])) +
            9 * (intval($data[1]) + intval($data[3]))
        ) % 10;
        $parity = self::$parity[$checksum];

        // Start pattern for EAN-5
        $bars = [
            [1, 'black'], [1, 'white'], [1, 'black'], [2, 'white'],
        ];

        for ($i = 0; $i < 5; $i++) {
            $digit = $data[$i];
            $encoding = $parity[$i] === 'A' ? 'L' : 'G';
            $pattern = self::$patterns[$encoding][$digit];
            foreach (str_split($pattern) as $bit) {
                $bars[] = [1, $bit === '1' ? 'black' : 'white'];
            }
            if ($i < 4) {
                // Separator between digits
                $bars[] = [1, 'white'];
                $bars[] = [1, 'black'];
            }
        }

        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('EAN5', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return preg_match('/^[0-9]{5}$/', $data);
    }
} 