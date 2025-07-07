<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Code128 implements BarcodeTypeInterface
{
    // Code128B character set (ASCII 32-127)
    private static array $patterns = [
        // Patterns for Code128B (partial, for demo)
        // Format: [bar/space widths...], e.g. [2,1,2,2,2,2] for ' ' (space)
        // Real implementation should include all 106 patterns
        ' ' => [2,1,2,2,2,2], // ASCII 32
        'A' => [2,2,2,1,2,2], // ASCII 65
        'B' => [2,2,2,2,2,1], // ASCII 66
        'C' => [1,2,1,2,2,3], // ASCII 67
        // ... (add more patterns for full support)
    ];

    public function encode(string $data): Barcode
    {
        $bars = [];
        // Start Code B (pattern for start, demo only)
        $bars[] = [2, 'black']; $bars[] = [1, 'white'];
        for ($i = 0; $i < strlen($data); $i++) {
            $char = $data[$i];
            if (!isset(self::$patterns[$char])) {
                // Unknown char: fallback to a single bar
                $bars[] = [2, 'black'];
                $bars[] = [1, 'white'];
            } else {
                foreach (self::$patterns[$char] as $j => $width) {
                    $bars[] = [$width, $j % 2 === 0 ? 'black' : 'white'];
                }
            }
        }
        // Stop pattern (demo)
        $bars[] = [2, 'black']; $bars[] = [3, 'white'];
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Code128', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        // Only allow chars in the demo pattern set
        foreach (str_split($data) as $char) {
            if (!isset(self::$patterns[$char])) return false;
        }
        return true;
    }
} 