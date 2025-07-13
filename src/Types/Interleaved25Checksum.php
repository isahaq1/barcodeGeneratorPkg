<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Interleaved25Checksum implements BarcodeTypeInterface
{
    // Patterns for digits 0-9 (bars/spaces, 5 elements each)
    private static $patterns = [
        '0' => '00110', '1' => '10001', '2' => '01001', '3' => '11000',
        '4' => '00101', '5' => '10100', '6' => '01100', '7' => '00011',
        '8' => '10010', '9' => '01010'
    ];

    public function encode(string $data): Barcode
    {
        // Validate input: numeric
        if (!preg_match('/^\d+$/', $data)) {
            throw new \InvalidArgumentException('Interleaved25Checksum must be numeric');
        }
        // If odd length, prepend a zero
        if (strlen($data) % 2 !== 0) {
            $data = '0' . $data;
        }
        // Calculate Mod 10 checksum
        $checksum = $this->calculateMod10($data);
        $dataWithChecksum = $data . $checksum;
        if (strlen($dataWithChecksum) % 2 !== 0) {
            $dataWithChecksum = '0' . $dataWithChecksum;
        }

        // Start pattern: narrow bar, narrow space, narrow bar, narrow space
        $bars = [
            [1, 'black'], [1, 'white'], [1, 'black'], [1, 'white']
        ];

        // Encode digits in pairs
        for ($i = 0; $i < strlen($dataWithChecksum); $i += 2) {
            $a = self::$patterns[$dataWithChecksum[$i]];
            $b = self::$patterns[$dataWithChecksum[$i + 1]];
            for ($j = 0; $j < 5; $j++) {
                // Bar
                $bars[] = [$a[$j] === '1' ? 3 : 1, 'black'];
                // Space
                $bars[] = [$b[$j] === '1' ? 3 : 1, 'white'];
            }
        }

        // Stop pattern: wide bar, narrow space, narrow bar
        $bars[] = [3, 'black'];
        $bars[] = [1, 'white'];
        $bars[] = [1, 'black'];

        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Interleaved25Checksum', $dataWithChecksum, $bars, $width);
    }

    private function calculateMod10($data)
    {
        $sum = 0;
        $double = true;
        for ($i = strlen($data) - 1; $i >= 0; $i--) {
            $digit = intval($data[$i]);
            if ($double) {
                $digit *= 2;
                if ($digit > 9) $digit -= 9;
            }
            $sum += $digit;
            $double = !$double;
        }
        $mod = $sum % 10;
        return $mod === 0 ? 0 : 10 - $mod;
    }

    public function validate(string $data): bool
    {
        return preg_match('/^\d+$/', $data);
    }
} 