<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class PLANET implements BarcodeTypeInterface
{
    // PLANET patterns for digits 0-9 (5 bars: 2 full, 3 half, but opposite of POSTNET)
    private static array $patterns = [
        '0' => '00111', '1' => '11100', '2' => '11010', '3' => '11001', '4' => '10110',
        '5' => '10101', '6' => '10011', '7' => '01110', '8' => '01101', '9' => '01011',
    ];

    public function encode(string $data): Barcode
    {
        // Validate input: numeric
        if (!preg_match('/^\d+$/', $data)) {
            throw new \InvalidArgumentException('PLANET must be numeric');
        }
        // Calculate and append checksum
        $data .= self::calculateChecksum($data);
        $bars = [];
        // Start bar (full bar)
        $bars[] = [2, 'black'];
        // Encode each digit
        for ($i = 0; $i < strlen($data); $i++) {
            $pattern = self::$patterns[$data[$i]];
            for ($j = 0; $j < 5; $j++) {
                $bars[] = [$pattern[$j] === '1' ? 2 : 1, 'black'];
            }
        }
        // Stop bar (full bar)
        $bars[] = [2, 'black'];
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('PLANET', $data, $bars, $width);
    }

    public static function calculateChecksum(string $data): string
    {
        $sum = 0;
        for ($i = 0; $i < strlen($data); $i++) {
            $sum += (int)$data[$i];
        }
        $check = (10 - ($sum % 10)) % 10;
        return (string)$check;
    }

    public function validate(string $data): bool
    {
        return preg_match('/^\d+$/', $data);
    }
} 