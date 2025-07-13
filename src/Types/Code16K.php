<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Code16K implements BarcodeTypeInterface
{
    public function encode(string $data, array $options = []): Barcode
    {
        // For demo: split data into rows of 8 chars, encode each as Code 128
        $rows = str_split($data, 8);
        $bars = [];
        foreach ($rows as $row) {
            // Use Code 128 patterns for each row (demo)
            $rowBars = $this->encodeCode128Row($row);
            $bars = array_merge($bars, $rowBars);
            // Add a gap between rows
            $bars[] = [2, 'white'];
        }
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('Code16K', $data, $bars, $width);
    }

    private function encodeCode128Row($data)
    {
        // Simple Code 128 B encoding for demo (A-Z, 0-9, space)
        $patterns = [
            'A' => '11010000100', 'B' => '11010010000', 'C' => '11010011100', 'D' => '11000111010',
            'E' => '11010111000', 'F' => '11010001110', 'G' => '11000101110', 'H' => '11010100010',
            'I' => '11010001010', 'J' => '11010101000', 'K' => '11010100001', 'L' => '11010010010',
            'M' => '11010011000', 'N' => '11010011010', 'O' => '11010100100', 'P' => '11010110000',
            'Q' => '11010111010', 'R' => '11010010110', 'S' => '11010011110', 'T' => '11010101100',
            'U' => '11010110100', 'V' => '11010111100', 'W' => '11010010100', 'X' => '11010011100',
            'Y' => '11010100110', 'Z' => '11010110010', '0' => '11010010000', '1' => '11010011000',
            '2' => '11010011100', '3' => '11010100010', '4' => '11010100100', '5' => '11010101000',
            '6' => '11010101100', '7' => '11010110000', '8' => '11010110100', '9' => '11010111000',
            ' ' => '11010000100'
        ];
        $bars = [];
        // Start pattern (Code 128 B)
        $bars[] = [2, 'black'];
        $bars[] = [1, 'white'];
        foreach (str_split(strtoupper($data)) as $char) {
            $pattern = $patterns[$char] ?? '11010000100'; // fallback to 'A'
            for ($i = 0; $i < strlen($pattern); $i++) {
                $bars[] = [1, $pattern[$i] === '1' ? 'black' : 'white'];
            }
            $bars[] = [1, 'white'];
        }
        // Stop pattern
        $bars[] = [2, 'black'];
        return $bars;
    }

    public function validate(string $data): bool
    {
        return preg_match('/^[A-Z0-9 ]+$/', strtoupper($data));
    }
} 