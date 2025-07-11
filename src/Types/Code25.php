<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Code25 implements BarcodeTypeInterface
{
    // Code 25 patterns (2 of 5)
    private static array $patterns = [
        '0' => [1,1,1,2,2,1,2,1], '1' => [2,1,1,1,1,2,2,1], '2' => [1,2,1,1,1,2,2,1], '3' => [2,2,1,1,1,1,2,1],
        '4' => [1,1,1,2,1,1,2,2], '5' => [2,1,1,2,1,1,1,2], '6' => [1,2,1,2,1,1,1,2], '7' => [1,1,1,1,1,2,2,2],
        '8' => [2,1,1,1,1,1,2,2], '9' => [1,2,1,1,1,1,1,2],
    ];

    public function encode(string $data): Barcode
    {
        // Validate input
        if (!ctype_digit($data)) {
            throw new \InvalidArgumentException("Code25 only supports numeric data");
        }

        $bars = [];
        
        // Start pattern
        $bars[] = [1, 'black']; $bars[] = [1, 'white']; $bars[] = [1, 'black']; $bars[] = [1, 'white'];
        
        // Data
        for ($i = 0; $i < strlen($data); $i++) {
            $char = $data[$i];
            $pattern = self::$patterns[$char];
            
            for ($j = 0; $j < count($pattern); $j++) {
                $bars[] = [$pattern[$j], $j % 2 === 0 ? 'black' : 'white'];
            }
        }
        
        // Stop pattern
        $bars[] = [2, 'black']; $bars[] = [1, 'white']; $bars[] = [1, 'black'];
        
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Code25', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return ctype_digit($data);
    }
} 