<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class MSIChecksum implements BarcodeTypeInterface
{
    // MSI patterns for digits 0-9 (bars/spaces, 4 elements each)
    private static $patterns = [
        '0' => '100100100100', '1' => '100100100110', '2' => '100100110100', '3' => '100100110110',
        '4' => '100110100100', '5' => '100110100110', '6' => '100110110100', '7' => '100110110110',
        '8' => '110100100100', '9' => '110100100110',
    ];

    public function encode(string $data): Barcode
    {
        // Validate input: numeric
        if (!preg_match('/^\d+$/', $data)) {
            throw new \InvalidArgumentException('MSIChecksum must be numeric');
        }
        // Calculate Mod 10 (Luhn) checksum
        $checksum = $this->calculateMod10($data);
        $dataWithChecksum = $data . $checksum;

        $bars = [];
        // Start bar (single black bar)
        $bars[] = [2, 'black'];
        // Encode each digit
        for ($i = 0; $i < strlen($dataWithChecksum); $i++) {
            $pattern = self::$patterns[$dataWithChecksum[$i]];
            for ($j = 0; $j < strlen($pattern); $j++) {
                $bars[] = [1, $pattern[$j] === '1' ? 'black' : 'white'];
            }
        }
        // Stop bar (single black bar)
        $bars[] = [2, 'black'];
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('MSIChecksum', $dataWithChecksum, $bars, $width);
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