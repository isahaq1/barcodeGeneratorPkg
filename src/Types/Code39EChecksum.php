<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Code39EChecksum implements BarcodeTypeInterface
{
    // Code 39 patterns (same as in Code39.php)
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
        '/' => '100100101001', '+' => '100101001001', '%' => '101001001001', '*' => '100101101101',
    ];

    // Code 39 Extended encoding table (maps ASCII to Code 39 chars)
    private static $extended = [
        "\0" => '%U', "\1" => '$A', "\2" => '$B', "\3" => '$C', "\4" => '$D', "\5" => '$E', "\6" => '$F', "\7" => '$G',
        "\10" => '$H', "\11" => '$I', "\12" => '$J', "\13" => '$K', "\14" => '$L', "\15" => '$M', "\16" => '$N', "\17" => '$O',
        "\20" => '$P', "\21" => '$Q', "\22" => '$R', "\23" => '$S', "\24" => '$T', "\25" => '$U', "\26" => '$V', "\27" => '$W',
        "\30" => '$X', "\31" => '$Y', "\32" => '$Z', "\33" => '%A', "\34" => '%B', "\35" => '%C', "\36" => '%D', "\37" => '%E',
        ' ' => ' ', '!' => '/A', '"' => '/B', '#' => '/C', '$' => '/D', '%' => '/E', '&' => '/F', "'" => '/G',
        '(' => '/H', ')' => '/I', '*' => '/J', '+' => '/K', ',' => '/L', '-' => '-', '.' => '.', '/' => '/',
        '0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7',
        '8' => '8', '9' => '9', ':' => '/Z', ';' => '%F', '<' => '%G', '=' => '%H', '>' => '%I', '?' => '%J',
        '@' => '%V', 'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F', 'G' => 'G',
        'H' => 'H', 'I' => 'I', 'J' => 'J', 'K' => 'K', 'L' => 'L', 'M' => 'M', 'N' => 'N', 'O' => 'O',
        'P' => 'P', 'Q' => 'Q', 'R' => 'R', 'S' => 'S', 'T' => 'T', 'U' => 'U', 'V' => 'V', 'W' => 'W',
        'X' => 'X', 'Y' => 'Y', 'Z' => 'Z', '[' => '%K', '\\' => '%L', ']' => '%M', '^' => '%N', '_' => '%O',
        '`' => '%W', 'a' => '+A', 'b' => '+B', 'c' => '+C', 'd' => '+D', 'e' => '+E', 'f' => '+F', 'g' => '+G',
        'h' => '+H', 'i' => '+I', 'j' => '+J', 'k' => '+K', 'l' => '+L', 'm' => '+M', 'n' => '+N', 'o' => '+O',
        'p' => '+P', 'q' => '+Q', 'r' => '+R', 's' => '+S', 't' => '+T', 'u' => '+U', 'v' => '+V', 'w' => '+W',
        'x' => '+X', 'y' => '+Y', 'z' => '+Z', '{' => '%P', '|' => '%Q', '}' => '%R', '~' => '%S', "\177" => '%T'
    ];

    // Code 39 character set for checksum calculation
    private static $charset = [
        '0','1','2','3','4','5','6','7','8','9',
        'A','B','C','D','E','F','G','H','I','J',
        'K','L','M','N','O','P','Q','R','S','T',
        'U','V','W','X','Y','Z','-','.',' ','$','/','+','%'
    ];

    public function encode(string $data): Barcode
    {
        // Convert to Code 39 Extended representation
        $encoded = '';
        $len = strlen($data);
        for ($i = 0; $i < $len; $i++) {
            $char = $data[$i];
            if (!isset(self::$extended[$char])) {
                throw new \InvalidArgumentException("Invalid character for Code39EChecksum: $char");
            }
            $encoded .= self::$extended[$char];
        }

        // Calculate checksum over the encoded data (excluding start/stop)
        $sum = 0;
        for ($i = 0; $i < strlen($encoded); $i++) {
            $char = $encoded[$i];
            $idx = array_search($char, self::$charset, true);
            if ($idx === false) {
                throw new \InvalidArgumentException("Invalid Code39 character for checksum: $char");
            }
            $sum += $idx;
        }
        $checksumChar = self::$charset[$sum % 43];

        // Add start character '*', checksum, and stop character '*'
        $encoded = '*' . $encoded . $checksumChar . '*';

        // Encode using Code 39 patterns
        $bars = [];
        for ($i = 0; $i < strlen($encoded); $i++) {
            $char = $encoded[$i];
            if (!isset(self::$patterns[$char])) {
                throw new \InvalidArgumentException("Invalid Code39 character: $char");
            }
            $pattern = self::$patterns[$char];
            for ($j = 0; $j < strlen($pattern); $j++) {
                $bars[] = [1, $pattern[$j] === '1' ? 'black' : 'white'];
            }
            // Inter-character gap (except after last char)
            if ($i < strlen($encoded) - 1) {
                $bars[] = [1, 'white'];
            }
        }
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Code39EChecksum', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        // All ASCII 0-127 are valid for Code 39 Extended
        return is_string($data) && strlen($data) > 0;
    }
} 