<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Code93 implements BarcodeTypeInterface
{
    // Code 93 character set and patterns
    private static array $patterns = [
        // 0-47: bar/space widths for each symbol (9 elements each)
        [1,1,1,1,2,2,2,2,1], [1,1,1,2,2,1,1,2,2], [1,1,1,2,2,2,2,1,1], [1,1,2,2,1,1,1,2,2], // 0-3
        [1,1,2,2,2,2,1,1,1], [1,2,1,1,1,1,2,2,2], [1,2,1,2,2,2,1,1,1], [1,2,2,1,1,1,2,2,1], // 4-7
        [1,2,2,2,2,1,1,1,1], [2,1,1,1,1,2,2,2,1], [2,1,1,2,2,1,1,2,1], [2,1,1,2,2,2,2,1,1], // 8-11
        [2,1,2,1,1,1,2,2,2], [2,1,2,2,2,2,1,1,1], [2,2,1,1,1,1,2,2,1], [2,2,1,2,2,2,1,1,1], // 12-15
        [2,2,2,1,1,1,2,2,1], [2,2,2,2,2,1,1,1,1], [1,1,1,1,1,2,2,2,2], [1,1,1,2,2,1,1,2,2], // 16-19
        [1,1,1,2,2,2,2,1,1], [1,1,2,2,1,1,1,2,2], [1,1,2,2,2,2,1,1,1], [1,2,1,1,1,1,2,2,2], // 20-23
        [1,2,1,2,2,2,1,1,1], [1,2,2,1,1,1,2,2,1], [1,2,2,2,2,1,1,1,1], [2,1,1,1,1,2,2,2,1], // 24-27
        [2,1,1,2,2,1,1,2,1], [2,1,1,2,2,2,2,1,1], [2,1,2,1,1,1,2,2,2], [2,1,2,2,2,2,1,1,1], // 28-31
        [2,2,1,1,1,1,2,2,1], [2,2,1,2,2,2,1,1,1], [2,2,2,1,1,1,2,2,1], [2,2,2,2,2,1,1,1,1], // 32-35
        [1,1,1,1,1,2,2,2,2], [1,1,1,2,2,1,1,2,2], [1,1,1,2,2,2,2,1,1], [1,1,2,2,1,1,1,2,2], // 36-39
        [1,1,2,2,2,2,1,1,1], [1,2,1,1,1,1,2,2,2], [1,2,1,2,2,2,1,1,1], [1,2,2,1,1,1,2,2,1], // 40-43
        [1,2,2,2,2,1,1,1,1], [2,1,1,1,1,2,2,2,1], [2,1,1,2,2,1,1,2,1], [2,1,1,2,2,2,2,1,1], // 44-47
    ];

    private static array $charMap = [
        '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7,
        '8' => 8, '9' => 9, 'A' => 10, 'B' => 11, 'C' => 12, 'D' => 13, 'E' => 14, 'F' => 15,
        'G' => 16, 'H' => 17, 'I' => 18, 'J' => 19, 'K' => 20, 'L' => 21, 'M' => 22, 'N' => 23,
        'O' => 24, 'P' => 25, 'Q' => 26, 'R' => 27, 'S' => 28, 'T' => 29, 'U' => 30, 'V' => 31,
        'W' => 32, 'X' => 33, 'Y' => 34, 'Z' => 35, '-' => 36, '.' => 37, ' ' => 38, '$' => 39,
        '/' => 40, '+' => 41, '%' => 42, '#' => 43, '&' => 44, '=' => 45, '@' => 46, '!' => 47,
    ];

    public function encode(string $data): Barcode
    {
        $bars = [];
        $codes = [];
        
        // Start character (*)
        $codes[] = 47;
        
        // Data characters
        for ($i = 0; $i < strlen($data); $i++) {
            $char = strtoupper($data[$i]);
            if (!isset(self::$charMap[$char])) {
                throw new \InvalidArgumentException("Invalid character for Code93: $char");
            }
            $codes[] = self::$charMap[$char];
        }
        
        // Calculate checksum C
        $sumC = 0;
        for ($i = 0; $i < count($codes); $i++) {
            $sumC += $codes[$i] * (count($codes) - $i);
        }
        $checksumC = $sumC % 47;
        $codes[] = $checksumC;
        
        // Calculate checksum K
        $sumK = 0;
        for ($i = 0; $i < count($codes); $i++) {
            $sumK += $codes[$i] * (count($codes) - $i);
        }
        $checksumK = $sumK % 47;
        $codes[] = $checksumK;
        
        // Stop character (*)
        $codes[] = 47;
        
        // Convert codes to bars
        foreach ($codes as $code) {
            foreach (self::$patterns[$code] as $j => $width) {
                $bars[] = [$width, $j % 2 === 0 ? 'black' : 'white'];
            }
        }
        
        // Add termination bar
        $bars[] = [1, 'black'];
        
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Code93', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        // Check if all characters are valid for Code93
        for ($i = 0; $i < strlen($data); $i++) {
            $char = strtoupper($data[$i]);
            if (!isset(self::$charMap[$char])) {
                return false;
            }
        }
        return true;
    }
} 