<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class KIX implements BarcodeTypeInterface
{
    // KIX encoding table - each character is encoded as 4 bars with specific heights
    // Bar types: A=ascender (top), D=descender (bottom), T=tracker (middle), F=full (full height)
    private static array $encoding = [
        'A' => 'ADTF', 'B' => 'ADFT', 'C' => 'AFDT', 'D' => 'AFTD', 'E' => 'ATDF', 'F' => 'ATFD',
        'G' => 'DAFT', 'H' => 'DATF', 'I' => 'DFAT', 'J' => 'DFTA', 'K' => 'DTAF', 'L' => 'DTFA',
        'M' => 'FADT', 'N' => 'FATD', 'O' => 'FDAT', 'P' => 'FDTA', 'Q' => 'FTAD', 'R' => 'FTDA',
        'S' => 'TADF', 'T' => 'TAFD', 'U' => 'TDAF', 'V' => 'TDFA', 'W' => 'TFAD', 'X' => 'TFDA',
        'Y' => 'ADTF', 'Z' => 'AFDT',
        '0' => 'ADTF', '1' => 'ADFT', '2' => 'AFDT', '3' => 'AFTD', '4' => 'ATDF', '5' => 'ATFD',
        '6' => 'DAFT', '7' => 'DATF', '8' => 'DFAT', '9' => 'DFTA'
    ];

    private static array $validChars = [
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
        '0','1','2','3','4','5','6','7','8','9'
    ];

    public function encode(string $data): Barcode
    {
        $data = strtoupper(trim($data));
        
        // Validate input
        if (empty($data)) {
            throw new \InvalidArgumentException('KIX data cannot be empty');
        }
        
        foreach (str_split($data) as $char) {
            if (!in_array($char, self::$validChars, true)) {
                throw new \InvalidArgumentException("Invalid character for KIX: $char");
            }
        }

        $bars = [];
        
        // Start code (4 bars: full, ascender, descender, full)
        $bars[] = [2, 'black', 'F']; // Full height start bar
        $bars[] = [1, 'white', 'S']; // Space
        $bars[] = [1, 'black', 'A']; // Ascender
        $bars[] = [1, 'white', 'S']; // Space
        $bars[] = [1, 'black', 'D']; // Descender
        $bars[] = [1, 'white', 'S']; // Space
        $bars[] = [2, 'black', 'F']; // Full height
        
        // Encode each character as 4 bars
        foreach (str_split($data) as $char) {
            $pattern = self::$encoding[$char];
            
            foreach (str_split($pattern) as $barType) {
                $bars[] = [1, 'white', 'S']; // Space before each bar
                $bars[] = [1, 'black', $barType]; // Bar with type
            }
        }
        
        // Stop code (4 bars: full, ascender, descender, full)
        $bars[] = [1, 'white', 'S']; // Space
        $bars[] = [2, 'black', 'F']; // Full height
        $bars[] = [1, 'white', 'S'];// Space
        $bars[] = [1, 'black', 'A']; // Ascender
        $bars[] = [1, 'white', 'S'];// Space
        $bars[] = [1, 'black', 'D']; // Descender
        $bars[] = [1, 'white', 'S'];// Space
        $bars[] = [2, 'black', 'F']; // Full height stop bar

        $width = 0;
        foreach ($bars as $bar) { 
            $width += $bar[0]; 
        }
        
        return new Barcode('KIX', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        $data = strtoupper(trim($data));
        
        if (empty($data)) {
            return false;
        }
        
        foreach (str_split($data) as $char) {
            if (!in_array($char, self::$validChars, true)) {
                return false;
            }
        }
        
        return true;
    }
} 