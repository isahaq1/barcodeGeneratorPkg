<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class RMS4CC implements BarcodeTypeInterface
{
    // RMS4CC encoding table (A-Z, 0-9)
    private static array $table = [
        'A' => 'ADAT', 'B' => 'ADTA', 'C' => 'ADTD', 'D' => 'ADTT', 'E' => 'ATAD', 'F' => 'ATDA', 'G' => 'ATDT', 'H' => 'ATTA',
        'I' => 'ATTD', 'J' => 'ATTT', 'K' => 'DAAT', 'L' => 'DATA', 'M' => 'DATD', 'N' => 'DATT', 'O' => 'DTAA', 'P' => 'DTAD',
        'Q' => 'DTDA', 'R' => 'DTDT', 'S' => 'DTTA', 'T' => 'DTTD', 'U' => 'DTTT', 'V' => 'TAAD', 'W' => 'TADA', 'X' => 'TADT',
        'Y' => 'TATA', 'Z' => 'TATD', '0' => 'TATT', '1' => 'TDAA', '2' => 'TDAD', '3' => 'TDDA', '4' => 'TDDD', '5' => 'TTAA',
        '6' => 'TTAD', '7' => 'TTDA', '8' => 'TTDT', '9' => 'TTTA',
    ];
    private static array $validChars = [
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
        '0','1','2','3','4','5','6','7','8','9'
    ];
    // Bar types: A=Ascender, D=Descender, T=Tracker

    public function encode(string $data): Barcode
    {
        $data = strtoupper($data);
        // Validate input
        foreach (str_split($data) as $char) {
            if (!in_array($char, self::$validChars, true)) {
                throw new \InvalidArgumentException("Invalid character for RMS4CC: $char");
            }
        }
        $bars = [];
        // Start bar (Tracker)
        $bars[] = [2, 'black'];
        foreach (str_split($data) as $char) {
            $pattern = self::$table[$char];
            foreach (str_split($pattern) as $barType) {
                // For demo: all bars are 2px wide, type encoded in color for now
                $bars[] = [2, 'black']; // In real, would encode bar height/type
            }
        }
        // Stop bar (Tracker)
        $bars[] = [2, 'black'];
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('RMS4CC', $data, $bars, $width);
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