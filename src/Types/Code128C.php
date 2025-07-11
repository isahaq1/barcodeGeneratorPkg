<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Code128C implements BarcodeTypeInterface
{
    // Code128C patterns for numeric pairs, plus start/stop/checksum
    private static array $patterns = [
        // 0-105: bar/space widths for each symbol (6 elements each)
        [2,1,2,2,2,2], [2,2,2,1,2,2], [2,2,2,2,2,1], [1,2,1,2,2,3], [1,2,1,3,2,2], [1,3,1,2,2,2], [1,2,2,2,1,3], [1,2,2,3,1,2], [1,3,2,2,1,2], [2,2,1,2,1,3], // 0-9
        [2,2,1,3,1,2], [2,3,1,2,1,2], [1,1,2,2,3,2], [1,2,2,1,3,2], [1,2,2,2,3,1], [1,1,3,2,2,2], [1,2,3,1,2,2], [1,2,3,2,2,1], [2,2,3,2,1,1], [2,2,1,1,3,2], // 10-19
        [2,2,1,2,3,1], [2,1,3,2,1,2], [2,2,3,1,1,2], [3,1,2,1,3,1], [3,1,1,2,2,2], [3,2,1,1,2,2], [3,2,1,2,2,1], [3,1,2,2,1,2], [3,2,2,1,1,2], [3,2,2,2,1,1], // 20-29
        [2,1,2,1,2,3], [2,1,2,3,2,1], [2,3,2,1,2,1], [1,1,1,3,2,3], [1,3,1,1,2,3], [1,3,1,3,2,1], [1,1,2,3,1,3], [1,3,2,1,1,3], [1,3,2,3,1,1], [2,1,1,3,1,3], // 30-39
        [2,3,1,1,1,3], [2,3,1,3,1,1], [1,1,2,1,3,3], [1,1,2,3,3,1], [1,3,2,1,3,1], [1,1,3,1,2,3], [1,1,3,3,2,1], [1,3,3,1,2,1], [3,1,3,1,2,1], [2,1,1,3,3,1], // 40-49
        [2,3,1,1,3,1], [2,1,3,1,1,3], [2,1,3,3,1,1], [2,1,3,1,3,1], [3,1,1,1,2,3], [3,1,1,3,2,1], [3,3,1,1,2,1], [3,1,2,1,1,3], [3,1,2,3,1,1], [3,3,2,1,1,1], // 50-59
        [3,1,4,1,1,1], [2,2,1,4,1,1], [4,3,1,1,1,1], [1,1,1,2,2,4], [1,1,1,4,2,2], [1,2,1,1,2,4], [1,2,1,4,2,1], [1,4,1,1,2,2], [1,4,1,2,2,1], [1,1,2,2,1,4], // 60-69
        [1,1,2,4,1,2], [1,2,2,1,1,4], [1,2,2,4,1,1], [1,4,2,1,1,2], [1,4,2,2,1,1], [2,4,1,2,1,1], [2,2,1,1,1,4], [4,1,3,1,1,1], [2,4,1,1,1,2], [1,3,4,1,1,1], // 70-79
        [1,1,1,2,4,2], [1,2,1,1,4,2], [1,2,1,2,4,1], [1,1,4,2,1,2], [1,2,4,1,1,2], [1,2,4,2,1,1], [4,1,1,2,1,2], [4,2,1,1,1,2], [4,2,1,2,1,1], [2,1,2,1,4,1], // 80-89
        [2,1,4,1,2,1], [4,1,2,1,2,1], [1,1,1,1,4,3], [1,1,1,3,4,1], [1,3,1,1,4,1], [1,1,4,1,1,3], [1,1,4,3,1,1], [4,1,1,1,1,3], [4,1,1,3,1,1], [1,1,3,1,4,1], // 90-99
        [1,1,4,1,3,1], [3,1,1,1,4,1], [4,1,1,1,3,1], [2,1,1,4,1,2], [2,1,1,2,1,4], [2,1,1,2,3,2], [2,3,3,1,1,1,2], [2,1,3,3,1,1,2], [2,1,1,3,3,1,2], [3,1,1,1,2,4], // 100-105
        [3,1,1,4,2,1], [3,1,4,1,2,1], [4,1,1,1,2,3], [4,1,1,3,2,1], [4,3,1,1,2,1], [2,1,1,1,4,3], [2,1,1,3,4,1], [2,3,1,1,4,1], [1,1,4,1,2,3], [1,1,4,3,2,1], // 106-115 (stop, etc)
    ];

    // Code128C: Convert numeric pairs to code values
    private static function numericPairToCode(string $pair): int
    {
        if (strlen($pair) !== 2 || !ctype_digit($pair)) {
            throw new \InvalidArgumentException("Invalid numeric pair for Code128C: $pair");
        }
        return (int)$pair;
    }

    public function encode(string $data): Barcode
    {
        // Ensure even number of digits
        if (strlen($data) % 2 !== 0) {
            $data = '0' . $data; // Pad with leading zero
        }

        $bars = [];
        $codes = [];
        // Start Code C (105)
        $codes[] = 105;
        
        // Process data in pairs
        for ($i = 0; $i < strlen($data); $i += 2) {
            $pair = substr($data, $i, 2);
            $codes[] = self::numericPairToCode($pair);
        }
        
        // Checksum
        $checksum = $codes[0];
        for ($i = 1; $i < count($codes); $i++) {
            $checksum += $codes[$i] * $i;
        }
        $checksum = $checksum % 103;
        $codes[] = $checksum;
        // Stop code (106)
        $codes[] = 106;
        
        // Convert codes to bars
        foreach ($codes as $code) {
            foreach (self::$patterns[$code] as $j => $width) {
                $bars[] = [$width, $j % 2 === 0 ? 'black' : 'white'];
            }
        }
        // Add termination bar (always 2 black modules)
        $bars[] = [2, 'black'];
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Code128C', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        // Only allow digits
        return ctype_digit($data);
    }
} 