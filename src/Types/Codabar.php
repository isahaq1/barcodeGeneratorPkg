<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Codabar implements BarcodeTypeInterface
{
    // Codabar patterns for 0-9, -$:/.+ABCD (bars/spaces, 7 elements each)
    private static array $patterns = [
        '0' => '101010011', '1' => '101011001', '2' => '101001011', '3' => '110010101',
        '4' => '101101001', '5' => '110101001', '6' => '100101011', '7' => '100101101',
        '8' => '100110101', '9' => '110100101',
        '-' => '101001101', '$' => '101100101', ':' => '1101011011', '/' => '1101101011',
        '.' => '1101101101', '+' => '101100110011',
        'A' => '1011001001', 'B' => '1001001011', 'C' => '1010010011', 'D' => '1010011001',
    ];
    private static array $validChars = ['0','1','2','3','4','5','6','7','8','9','-','$',':','/','.','+','A','B','C','D'];

    public function encode(string $data): Barcode
    {
        $data = strtoupper($data);
        // Must start and end with A, B, C, or D
        if (!preg_match('/^[ABCD].*[ABCD]$/', $data)) {
            throw new \InvalidArgumentException('Codabar must start and end with A, B, C, or D');
        }
        // Validate all characters
        foreach (str_split($data) as $char) {
            if (!in_array($char, self::$validChars, true)) {
                throw new \InvalidArgumentException("Invalid character for Codabar: $char");
            }
        }
        $bars = [];
        foreach (str_split($data) as $i => $char) {
            $pattern = self::$patterns[$char];
            for ($j = 0; $j < strlen($pattern); $j++) {
                $bars[] = [($pattern[$j] === '1' ? 2 : 1), $j % 2 === 0 ? 'black' : 'white'];
            }
            // Inter-character gap (narrow white bar, except after last char)
            if ($i < strlen($data) - 1) {
                $bars[] = [1, 'white'];
            }
        }
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Codabar', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        $data = strtoupper($data);
        if (!preg_match('/^[ABCD].*[ABCD]$/', $data)) return false;
        foreach (str_split($data) as $char) {
            if (!in_array($char, self::$validChars, true)) return false;
        }
        return true;
    }
} 