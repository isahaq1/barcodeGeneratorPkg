<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Code93 implements BarcodeTypeInterface
{
    // Code93 character set and patterns (9 modules per character)
    private static array $charset = [
        '0','1','2','3','4','5','6','7','8','9',
        'A','B','C','D','E','F','G','H','I','J',
        'K','L','M','N','O','P','Q','R','S','T',
        'U','V','W','X','Y','Z','-','.',' ','$','/','+','%','a','b','c','d'
    ];
    private static array $patterns = [
        '0' => '100010100', '1' => '101001000', '2' => '101000100', '3' => '101000010',
        '4' => '100101000', '5' => '100100100', '6' => '100100010', '7' => '101010000',
        '8' => '100010010', '9' => '100001010',
        'A' => '110101000', 'B' => '110100100', 'C' => '110100010', 'D' => '110010100',
        'E' => '110010010', 'F' => '110001010', 'G' => '101101000', 'H' => '101100100',
        'I' => '101100010', 'J' => '100110100', 'K' => '100011010', 'L' => '101011000',
        'M' => '101001100', 'N' => '101000110', 'O' => '100101100', 'P' => '100010110',
        'Q' => '110110100', 'R' => '110110010', 'S' => '110101100', 'T' => '110100110',
        'U' => '110010110', 'V' => '110011010', 'W' => '101101100', 'X' => '101100110',
        'Y' => '100110110', 'Z' => '100111010', '-' => '100101110', '.' => '111010100',
        ' ' => '111010010', '$' => '111001010', '/' => '101101110', '+' => '101110110',
        '%' => '110101110', 'a' => '100100110', 'b' => '111011010', 'c' => '111010110', 'd' => '100110010',
        '*' => '101011110', // Start/stop
    ];
    // Extended encoding not implemented (a-d for shift chars)

    public function encode(string $data): Barcode
    {
        $data = strtoupper($data);
        // Validate input
        foreach (str_split($data) as $char) {
            if (!isset(self::$patterns[$char])) {
                throw new \InvalidArgumentException("Invalid character for Code93: $char");
            }
        }
        // Calculate checksums C and K
        $data .= self::checksumC($data);
        $data .= self::checksumK($data);
        // Add start/stop '*'
        $encoded = '*' . $data . '*';
        $bars = [];
        foreach (str_split($encoded) as $char) {
            $pattern = self::$patterns[$char];
            for ($i = 0; $i < strlen($pattern); $i++) {
                $bars[] = [1, $pattern[$i] === '1' ? 'black' : 'white'];
            }
        }
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Code93', $data, $bars, $width);
    }

    // Checksum C
    public static function checksumC(string $data): string
    {
        $weight = 1;
        $sum = 0;
        for ($i = strlen($data) - 1; $i >= 0; $i--) {
            $index = array_search($data[$i], self::$charset, true);
            $sum += $index * $weight;
            $weight = $weight == 20 ? 1 : $weight + 1;
        }
        return self::$charset[$sum % 47];
    }
    // Checksum K
    public static function checksumK(string $data): string
    {
        $weight = 1;
        $sum = 0;
        for ($i = strlen($data); $i >= 0; $i--) {
            $index = array_search($i < strlen($data) ? $data[$i] : self::checksumC($data), self::$charset, true);
            $sum += $index * $weight;
            $weight = $weight == 15 ? 1 : $weight + 1;
        }
        return self::$charset[$sum % 47];
    }

    public function validate(string $data): bool
    {
        $data = strtoupper($data);
        foreach (str_split($data) as $char) {
            if (!isset(self::$patterns[$char])) return false;
        }
        return true;
    }
} 