<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Code49 implements BarcodeTypeInterface
{
    // Code 49 character set (partial, for demo)
    private static $charset = [
        '0','1','2','3','4','5','6','7','8','9',
        'A','B','C','D','E','F','G','H','I','J',
        'K','L','M','N','O','P','Q','R','S','T',
        'U','V','W','X','Y','Z','-','.',' ','$','/','+','%'
    ];

    // Each character is encoded as a pattern of bars/spaces (for demo, use Code 39 patterns)
    private static $patterns = [
        '0' => '101001101101', '1' => '110100101011', '2' => '101100101011', '3' => '110110010101',
        '4' => '101001101011', '5' => '110100110101', '6' => '101100110101', '7' => '101001011011',
        '8' => '110100101101', '9' => '101100101101',
        'A' => '110101001011', 'B' => '101101001011', 'C' => '110110100101', 'D' => '101011001011',
        'E' => '110101100101', 'F' => '101101100101', 'G' => '101010011011', 'H' => '110101001101',
        'I' => '101101001101', 'J' => '101011001101',
        'K' => '110101010011', 'L' => '101101010011', 'M' => '110110101001', 'N' => '101011010011',
        'O' => '110101101001', 'P' => '101101101001', 'Q' => '101010110011', 'R' => '110101011001',
        'S' => '101101011001', 'T' => '101011011001',
        'U' => '110010101011', 'V' => '100110101011', 'W' => '110011010101', 'X' => '100101101011',
        'Y' => '110010110101', 'Z' => '100110110101',
        '-' => '100101011011', '.' => '110010101101', ' ' => '100110101101', '$' => '100100100101',
        '/' => '100100101001', '+' => '100101001001', '%' => '101001001001'
    ];

    public function encode(string $data, array $options = []): Barcode
    {
        $data = strtoupper($data);
        $bars = [];
        // Start pattern (for demo, use Code 39 start)
        $bars[] = [2, 'black'];
        $bars[] = [1, 'white'];
        foreach (str_split($data) as $char) {
            if (!isset(self::$patterns[$char])) {
                throw new \InvalidArgumentException("Invalid character for Code49: $char");
            }
            $pattern = self::$patterns[$char];
            for ($i = 0; $i < strlen($pattern); $i++) {
                $bars[] = [1, $pattern[$i] === '1' ? 'black' : 'white'];
            }
            $bars[] = [1, 'white']; // Inter-character gap
        }
        // Stop pattern (for demo, use Code 39 stop)
        $bars[] = [2, 'black'];
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Code49', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return preg_match('/^[0-9A-Z\-\. \$\/\+%]+$/', $data);
    }
} 