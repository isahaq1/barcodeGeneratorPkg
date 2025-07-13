<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class MSI implements BarcodeTypeInterface
{
    // MSI patterns for digits 0-9 (4 bars per digit)
    private static array $patterns = [
        '0' => '1001', '1' => '1100', '2' => '1010', '3' => '1111',
        '4' => '1101', '5' => '1011', '6' => '1000', '7' => '1110',
        '8' => '0010', '9' => '0001',
    ];

    public function encode(string $data): Barcode
    {
        // Validate input: numeric
        if (!preg_match('/^\d+$/', $data)) {
            throw new \InvalidArgumentException('MSI must be numeric');
        }
        
        if (empty($data)) {
            throw new \InvalidArgumentException('MSI data cannot be empty');
        }

        $bars = [];
        
        // Start code: narrow bar, narrow space, narrow bar, narrow space
        $bars[] = [1, 'black'];  // Start bar
        $bars[] = [1, 'white'];  // Space
        $bars[] = [1, 'black'];  // Bar
        $bars[] = [1, 'white'];  // Space
        
        // Encode each digit
        for ($i = 0; $i < strlen($data); $i++) {
            $pattern = self::$patterns[$data[$i]];
            for ($j = 0; $j < strlen($pattern); $j++) {
                $bars[] = [1, $pattern[$j] === '1' ? 'black' : 'white'];
            }
        }
        
        // Stop code: wide bar, narrow space, narrow bar
        $bars[] = [2, 'black'];  // Wide stop bar
        $bars[] = [1, 'white'];  // Space
        $bars[] = [1, 'black'];  // Final bar
        
        $width = 0;
        foreach ($bars as $bar) { 
            $width += $bar[0]; 
        }
        
        return new Barcode('MSI', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return preg_match('/^\d+$/', $data) && !empty($data);
    }
} 