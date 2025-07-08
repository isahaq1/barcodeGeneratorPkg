<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class POSTNET implements BarcodeTypeInterface
{
    // POSTNET patterns for digits 0-9 (5 bars: 2 full, 3 half)
    private static array $patterns = [
        '0' => '11000', '1' => '00011', '2' => '00101', '3' => '00110', '4' => '01001',
        '5' => '01010', '6' => '01100', '7' => '10001', '8' => '10010', '9' => '10100',
    ];

    public function encode(string $data): Barcode
    {
        // Validate input: numeric
        if (!preg_match('/^\d+$/', $data)) {
            throw new \InvalidArgumentException('POSTNET must be numeric');
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
        return new Barcode('POSTNET', $data, $bars, $width);
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