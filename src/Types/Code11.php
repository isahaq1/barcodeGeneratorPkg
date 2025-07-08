<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Code11 implements BarcodeTypeInterface
{
    // Code11 patterns for 0-9 and dash (bars/spaces, 5 elements each)
    private static array $patterns = [
        '0' => '101011', '1' => '1101011', '2' => '1001011', '3' => '1100101', '4' => '1011011',
        '5' => '1101101', '6' => '1001101', '7' => '1010011', '8' => '1101001', '9' => '110101',
        '-' => '101101',
    ];
    private static array $validChars = ['0','1','2','3','4','5','6','7','8','9','-'];

    public function encode(string $data): Barcode
    {
        $data = strtoupper($data);
        // Validate input
        foreach (str_split($data) as $char) {
            if (!in_array($char, self::$validChars, true)) {
                throw new \InvalidArgumentException("Invalid character for Code11: $char");
            }
        }
        // Calculate and append checksum C (and K if length > 10)
        $data .= self::checksumC($data);
        if (strlen($data) > 10) {
            $data .= self::checksumK($data);
        }
        // Start/stop bar (always same as dash)
        $bars = [];
        $bars = array_merge($bars, self::patternToBars(self::$patterns['-']));
        // Inter-character gap (always 1 white)
        $bars[] = [1, 'white'];
        foreach (str_split($data) as $i => $char) {
            $bars = array_merge($bars, self::patternToBars(self::$patterns[$char]));
            if ($i < strlen($data) - 1) {
                $bars[] = [1, 'white'];
            }
        }
        // End with stop bar
        $bars[] = [1, 'white'];
        $bars = array_merge($bars, self::patternToBars(self::$patterns['-']));
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Code11', $data, $bars, $width);
    }

    private static function patternToBars(string $pattern): array
    {
        $bars = [];
        for ($i = 0; $i < strlen($pattern); $i++) {
            $bars[] = [$pattern[$i] === '1' ? 2 : 1, $i % 2 === 0 ? 'black' : 'white'];
        }
        return $bars;
    }

    // Checksum C
    public static function checksumC(string $data): string
    {
        $weight = 1;
        $sum = 0;
        for ($i = strlen($data) - 1; $i >= 0; $i--) {
            $char = $data[$i];
            $value = $char === '-' ? 10 : (int)$char;
            $sum += $value * $weight;
            $weight = $weight == 10 ? 1 : $weight + 1;
        }
        $c = $sum % 11;
        return $c === 10 ? '-' : (string)$c;
    }
    // Checksum K (if data+checksumC > 10 chars)
    public static function checksumK(string $data): string
    {
        $data .= self::checksumC($data);
        $weight = 1;
        $sum = 0;
        for ($i = strlen($data) - 1; $i >= 0; $i--) {
            $char = $data[$i];
            $value = $char === '-' ? 10 : (int)$char;
            $sum += $value * $weight;
            $weight = $weight == 9 ? 1 : $weight + 1;
        }
        $k = $sum % 11;
        return $k === 10 ? '-' : (string)$k;
    }

    public function validate(string $data): bool
    {
        $data = strtoupper($data);
        foreach (str_split($data) as $char) {
            if (!in_array($char, self::$validChars, true)) return false;
        }
        return true;
    }
} 