<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class KIX implements BarcodeTypeInterface
{
    // KIX encoding table (A-Z, 0-9)
    private static array $table = [
        'A' => 'ATTA', 'B' => 'ATAT', 'C' => 'ATTT', 'D' => 'AATT', 'E' => 'AATA', 'F' => 'AATT', 'G' => 'ATAA', 'H' => 'ATTA',
        'I' => 'ATAT', 'J' => 'ATTT', 'K' => 'AATT', 'L' => 'AATA', 'M' => 'AATT', 'N' => 'ATAA', 'O' => 'ATTA', 'P' => 'ATAT',
        'Q' => 'ATTT', 'R' => 'AATT', 'S' => 'AATA', 'T' => 'AATT', 'U' => 'ATAA', 'V' => 'ATTA', 'W' => 'ATAT', 'X' => 'ATTT',
        'Y' => 'AATT', 'Z' => 'AATA',
        '0' => 'TATA', '1' => 'TATT', '2' => 'TTAA', '3' => 'TTAT', '4' => 'TTTA', '5' => 'TTTT', '6' => 'TAAA', '7' => 'TAAT',
        '8' => 'TATA', '9' => 'TATT',
    ];
    private static array $validChars = [
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
        '0','1','2','3','4','5','6','7','8','9'
    ];
    // Bar types: A=Ascender, T=Tracker

    public function encode(string $data): Barcode
    {
        $data = strtoupper($data);
        // Validate input
        foreach (str_split($data) as $char) {
            if (!in_array($char, self::$validChars, true)) {
                throw new \InvalidArgumentException("Invalid character for KIX: $char");
            }
        }
        $bars = [];
        // No explicit start/stop for KIX, just encode sequence
        foreach (str_split($data) as $char) {
            $pattern = self::$table[$char];
            foreach (str_split($pattern) as $barType) {
                // For demo: all bars are 2px wide, type encoded in color for now
                $bars[] = [2, 'black']; // In real, would encode bar height/type
            }
        }
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('KIX', $data, $bars, $width);
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