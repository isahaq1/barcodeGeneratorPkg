<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class QRCode implements BarcodeTypeInterface
{
    // Only supports QR Version 1, ECC Level L, byte mode, up to 17 bytes
    public function encode(string $data): Barcode
    {
        // Limit: up to 17 bytes for QR v1-L
        if (strlen($data) > 17) {
            throw new \InvalidArgumentException('QR Code (raw PHP) only supports up to 17 bytes for Version 1-L');
        }
        // 1. Encode data in byte mode
        $bits = [];
        // Mode indicator (byte mode): 0100
        $bits = array_merge($bits, [0,1,0,0]);
        // Character count (8 bits)
        $len = strlen($data);
        for ($i = 7; $i >= 0; $i--) {
            $bits[] = ($len >> $i) & 1;
        }
        // Data bytes
        for ($i = 0; $i < $len; $i++) {
            $byte = ord($data[$i]);
            for ($j = 7; $j >= 0; $j--) {
                $bits[] = ($byte >> $j) & 1;
            }
        }
        // Terminator (up to 4 bits)
        $maxDataBits = 152; // v1-L
        $bits = array_slice($bits, 0, $maxDataBits);
        while (count($bits) < $maxDataBits) {
            $bits[] = 0;
        }
        // Pad to byte boundary
        while (count($bits) % 8 !== 0) {
            $bits[] = 0;
        }
        // Pad bytes (0xEC, 0x11 alternately)
        $padBytes = [0xEC, 0x11];
        $i = 0;
        while (count($bits) < $maxDataBits) {
            $pad = $padBytes[$i++ % 2];
            for ($j = 7; $j >= 0; $j--) {
                $bits[] = ($pad >> $j) & 1;
            }
        }
        // 2. Error correction (Reed-Solomon, 7 bytes for v1-L)
        $dataBytes = [];
        for ($i = 0; $i < $maxDataBits; $i += 8) {
            $byte = 0;
            for ($j = 0; $j < 8; $j++) {
                $byte = ($byte << 1) | $bits[$i + $j];
            }
            $dataBytes[] = $byte;
        }
        $ecBytes = self::reedSolomon($dataBytes, 7);
        // 3. Interleave data and EC bytes
        $allBytes = array_merge($dataBytes, $ecBytes);
        // 4. Build QR matrix (21x21)
        $size = 21;
        $matrix = array_fill(0, $size, array_fill(0, $size, null));
        self::placeFinder($matrix, 0, 0);
        self::placeFinder($matrix, $size-7, 0);
        self::placeFinder($matrix, 0, $size-7);
        for ($i = 8; $i < $size-8; $i++) {
            $matrix[6][$i] = $matrix[$i][6] = $i % 2;
        }
        // Place data bits (bottom-up, right-to-left, zigzag)
        $dir = -1; $x = $size-1; $y = $size-1; $bitIdx = 0;
        while ($x > 0) {
            if ($x == 6) $x--;
            for ($i = 0; $i < $size; $i++) {
                $row = $y + $dir * $i;
                for ($col = 0; $col < 2; $col++) {
                    $xx = $x - $col;
                    if ($matrix[$row][$xx] === null) {
                        $matrix[$row][$xx] = ($bitIdx < count($allBytes)*8)
                            ? (($allBytes[intdiv($bitIdx,8)] >> (7-($bitIdx%8))) & 1)
                            : 0;
                        $bitIdx++;
                    }
                }
            }
            $x -= 2; $dir = -$dir; $y = ($dir > 0) ? 0 : $size-1;
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

    // Place finder pattern
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

    // Reed-Solomon ECC for QR (GF(256), generator for 7 bytes)
    private static function reedSolomon(array $data, int $ecLen): array
    {
        $gen = [87, 229, 146, 149, 238, 102, 21]; // Generator for v1-L
        $ec = array_fill(0, $ecLen, 0);
        foreach ($data as $d) {
            $factor = $d ^ $ec[0];
            for ($i = 0; $i < $ecLen-1; $i++) {
                $ec[$i] = $ec[$i+1] ^ self::gmult($gen[$i], $factor);
            }
            $ec[$ecLen-1] = self::gmult($gen[$ecLen-1], $factor);
        }
        return $ec;
    }
    // Galois field multiplication (QR uses GF(256) with 0x11d)
    private static function gmult($a, $b) {
        $p = 0;
        for ($i = 0; $i < 8; $i++) {
            if ($b & 1) $p ^= $a;
            $carry = $a & 0x80;
            $a <<= 1;
            if ($carry) $a ^= 0x11d;
            $b >>= 1;
        }
        return $p & 0xFF;
    }

    public function validate(string $data): bool
    {
        return strlen($data) > 0 && strlen($data) <= 17;
    }
} 