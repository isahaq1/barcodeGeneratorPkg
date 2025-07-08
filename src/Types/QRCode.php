<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class QRCode implements BarcodeTypeInterface
{
    // Alphanumeric character set for QR
    private static $alphanum = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ $%*+-./:';

    public function encode(string $data): Barcode
    {
        // Validate input: alphanumeric, up to 25 chars (QR v1-L max)
        if (!preg_match('/^['.preg_quote(self::$alphanum, '/').']{1,25}$/', $data)) {
            throw new \InvalidArgumentException('QR Code (custom) only supports alphanumeric (max 25 chars)');
        }
        // QR Version 1, ECC Level L, 21x21 modules
        $size = 21;
        $matrix = array_fill(0, $size, array_fill(0, $size, 0));
        // Place finder patterns (top-left, top-right, bottom-left)
        self::placeFinder($matrix, 0, 0);
        self::placeFinder($matrix, $size-7, 0);
        self::placeFinder($matrix, 0, $size-7);
        // Place timing patterns
        for ($i = 8; $i < $size-8; $i++) {
            $matrix[6][$i] = $matrix[$i][6] = $i % 2;
        }
        // Place a simple data pattern (for demo: diagonal)
        $bit = 1;
        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                if ($matrix[$i][$j] === 0 && $i === $j) {
                    $matrix[$i][$j] = $bit;
                    $bit = 1 - $bit;
                }
            }
        }
        // For demo, flatten matrix to bars: row by row, 1=black, 0=white
        $bars = [];
        foreach ($matrix as $row) {
            foreach ($row as $cell) {
                $bars[] = [1, $cell ? 'black' : 'white'];
            }
        }
        $width = $size * $size;
        return new Barcode('QRCode', $data, $bars, $width);
    }

    private static function placeFinder(array &$m, int $x, int $y): void
    {
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $m[$y+$i][$x+$j] = (
                    $i === 0 || $i === 6 || $j === 0 || $j === 6 ||
                    ($i >= 2 && $i <= 4 && $j >= 2 && $j <= 4)
                ) ? 1 : 0;
            }
        }
    }

    public function validate(string $data): bool
    {
        return preg_match('/^['.preg_quote(self::$alphanum, '/').']{1,25}$/', $data);
    }
} 