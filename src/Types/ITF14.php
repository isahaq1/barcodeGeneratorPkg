<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class ITF14 implements BarcodeTypeInterface
{
    // Patterns for digits 0-9 (bars/spaces, 5 elements each)
    private static array $patterns = [
        '0' => '00110', '1' => '10001', '2' => '01001', '3' => '11000', '4' => '00101',
        '5' => '10100', '6' => '01100', '7' => '00011', '8' => '10010', '9' => '01010',
    ];

    public function encode(string $data): Barcode
    {
        // Validate and pad/checksum
        if (!preg_match('/^\d{13,14}$/', $data)) {
            throw new \InvalidArgumentException('ITF-14 must be 13 or 14 digits');
        }
        if (strlen($data) === 13) {
            $data .= self::calculateChecksum(substr($data, 0, 13));
        }
        if (!self::validate($data)) {
            throw new \InvalidArgumentException('Invalid ITF-14 checksum');
        }
        // Must be even number of digits
        if (strlen($data) % 2 !== 0) {
            throw new \InvalidArgumentException('ITF-14 must have an even number of digits');
        }
        $bars = [];
        // Start code (bar-space-bar-space, wide-narrow-narrow-narrow)
        $bars[] = [2, 'black']; $bars[] = [1, 'white']; $bars[] = [1, 'black']; $bars[] = [1, 'white'];
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
        $bars[] = [2, 'black']; $bars[] = [1, 'white']; $bars[] = [1, 'black'];
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('ITF14', $data, $bars, $width);
    }

    public static function calculateChecksum(string $data): string
    {
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $n = (int)$data[$i];
            $sum += ($i % 2 === 0) ? $n * 3 : $n;
        }
        $check = (10 - ($sum % 10)) % 10;
        return (string)$check;
    }

    public function validate(string $data): bool
    {
        if (!preg_match('/^\d{14}$/', $data)) return false;
        return $data[13] === self::calculateChecksum(substr($data, 0, 13));
    }
} 