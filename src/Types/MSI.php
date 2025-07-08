<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class MSI implements BarcodeTypeInterface
{
    // MSI patterns for digits 0-9 (bars/spaces, 4 elements each)
    private static array $patterns = [
        '0' => '100100100100', '1' => '100100100110', '2' => '100100110100', '3' => '100100110110',
        '4' => '100110100100', '5' => '100110100110', '6' => '100110110100', '7' => '100110110110',
        '8' => '110100100100', '9' => '110100100110',
    ];

    public function encode(string $data): Barcode
    {
        // Validate input: numeric
        if (!preg_match('/^\d+$/', $data)) {
            throw new \InvalidArgumentException('MSI must be numeric');
        }
        $bars = [];
        // Start bar (single black bar)
        $bars[] = [2, 'black'];
        // Encode each digit
        for ($i = 0; $i < strlen($data); $i++) {
            $pattern = self::$patterns[$data[$i]];
            for ($j = 0; $j < strlen($pattern); $j++) {
                $bars[] = [1, $pattern[$j] === '1' ? 'black' : 'white'];
            }
        }
        // Stop bar (single black bar)
        $bars[] = [2, 'black'];
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('MSI', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return preg_match('/^\d+$/', $data);
    }
} 