<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class PharmaCodeTwoTracks implements BarcodeTypeInterface
{
    // PharmaCode Two-Track encoding patterns
    // Each position can have: 0=none, 1=top track, 2=bottom track, 3=both tracks
    private static array $trackPatterns = [
        0 => [0, 0], // No bars
        1 => [1, 0], // Top track only
        2 => [0, 1], // Bottom track only  
        3 => [1, 1], // Both tracks
    ];

    public function encode(string $data): Barcode
    {
        // Validate input: integer 4-64570080
        if (!preg_match('/^\d+$/', $data) || (int)$data < 4 || (int)$data > 64570080) {
            throw new \InvalidArgumentException('PharmaCode Two-Track must be an integer between 4 and 64570080');
        }
        
        $value = (int)$data;
        $bars = [];
        
        // Start code: both tracks
        $bars[] = [1, 'black', 'both']; // Start with both tracks
        
        // Encode the value using base-4 encoding (0-3 for each position)
        $encoded = [];
        while ($value > 0) {
            $encoded[] = $value % 4;
            $value = intdiv($value, 4);
        }
        $encoded = array_reverse($encoded);
        
        // Encode each position as track pattern
        foreach ($encoded as $digit) {
            $pattern = self::$trackPatterns[$digit];
            
            // Add space before each position
            $bars[] = [1, 'white', 'space'];
            
            // Encode the track pattern
            if ($pattern[0] && $pattern[1]) {
                $bars[] = [2, 'black', 'both']; // Both tracks
            } elseif ($pattern[0]) {
                $bars[] = [1, 'black', 'top']; // Top track only
            } elseif ($pattern[1]) {
                $bars[] = [1, 'black', 'bottom']; // Bottom track only
            } else {
                $bars[] = [1, 'white', 'none']; // No bars
            }
        }
        
        // Stop code: both tracks
        $bars[] = [1, 'white', 'space'];
        $bars[] = [2, 'black', 'both']; // End with both tracks
        
        $width = 0;
        foreach ($bars as $bar) { 
            $width += $bar[0]; 
        }
        
        return new Barcode('PharmaCodeTwoTracks', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return preg_match('/^\d+$/', $data) && (int)$data >= 4 && (int)$data <= 64570080;
    }
} 