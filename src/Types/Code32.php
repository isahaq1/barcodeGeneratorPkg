<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Code32 implements BarcodeTypeInterface
{
    // Code 32 patterns (Italian Pharmacode)
    private static array $patterns = [
        '0' => [1,1,1,1,2,2,2,2], '1' => [1,1,1,2,2,1,1,2], '2' => [1,1,1,2,2,2,2,1], '3' => [1,1,2,2,1,1,1,2],
        '4' => [1,1,2,2,2,2,1,1], '5' => [1,2,1,1,1,1,2,2], '6' => [1,2,1,2,2,2,1,1], '7' => [1,2,2,1,1,1,2,2],
        '8' => [1,2,2,2,2,1,1,1], '9' => [2,1,1,1,1,2,2,2], 'A' => [2,1,1,2,2,1,1,2], 'B' => [2,1,1,2,2,2,2,1],
        'C' => [2,1,2,1,1,1,2,2], 'D' => [2,1,2,2,2,2,1,1], 'E' => [2,2,1,1,1,1,2,2], 'F' => [2,2,1,2,2,2,1,1],
        'G' => [2,2,2,1,1,1,2,2], 'H' => [2,2,2,2,2,1,1,1], 'I' => [1,1,1,1,1,2,2,2], 'J' => [1,1,1,2,2,1,1,2],
        'K' => [1,1,1,2,2,2,2,1], 'L' => [1,1,2,2,1,1,1,2], 'M' => [1,1,2,2,2,2,1,1], 'N' => [1,2,1,1,1,1,2,2],
        'O' => [1,2,1,2,2,2,1,1], 'P' => [1,2,2,1,1,1,2,2], 'Q' => [1,2,2,2,2,1,1,1], 'R' => [2,1,1,1,1,2,2,2],
        'S' => [2,1,1,2,2,1,1,2], 'T' => [2,1,1,2,2,2,2,1], 'U' => [2,1,2,1,1,1,2,2], 'V' => [2,1,2,2,2,2,1,1],
        'W' => [2,2,1,1,1,1,2,2], 'X' => [2,2,1,2,2,2,1,1], 'Y' => [2,2,2,1,1,1,2,2], 'Z' => [2,2,2,2,2,1,1,1],
    ];

    public function encode(string $data): Barcode
    {
        // Validate input - Code 32 supports alphanumeric
        if (!ctype_alnum($data)) {
            throw new \InvalidArgumentException("Code32 only supports alphanumeric data");
        }

        $bars = [];
        
        // Start pattern
        $bars[] = [1, 'black']; $bars[] = [1, 'white']; $bars[] = [1, 'black']; $bars[] = [1, 'white'];
        
        // Data
        for ($i = 0; $i < strlen($data); $i++) {
            $char = strtoupper($data[$i]);
            if (!isset(self::$patterns[$char])) {
                throw new \InvalidArgumentException("Invalid character for Code32: $char");
            }
            $pattern = self::$patterns[$char];
            
            for ($j = 0; $j < count($pattern); $j++) {
                $bars[] = [$pattern[$j], $j % 2 === 0 ? 'black' : 'white'];
            }
        }
        
        // Stop pattern
        $bars[] = [2, 'black']; $bars[] = [1, 'white']; $bars[] = [1, 'black'];
        
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Code32', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return ctype_alnum($data);
    }
} 