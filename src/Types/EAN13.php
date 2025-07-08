<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class EAN13 implements BarcodeTypeInterface
{
    // L, G, R encoding patterns for digits 0-9
    private static array $L = [
        '0' => '0001101', '1' => '0011001', '2' => '0010011', '3' => '0111101', '4' => '0100011',
        '5' => '0110001', '6' => '0101111', '7' => '0111011', '8' => '0110111', '9' => '0001011',
    ];
    private static array $G = [
        '0' => '0100111', '1' => '0110011', '2' => '0011011', '3' => '0100001', '4' => '0011101',
        '5' => '0111001', '6' => '0000101', '7' => '0010001', '8' => '0001001', '9' => '0010111',
    ];
    private static array $R = [
        '0' => '1110010', '1' => '1100110', '2' => '1101100', '3' => '1000010', '4' => '1011100',
        '5' => '1001110', '6' => '1010000', '7' => '1000100', '8' => '1001000', '9' => '1110100',
    ];
    // Parity pattern for first digit
    private static array $parity = [
        '0' => 'LLLLLL', '1' => 'LLGLGG', '2' => 'LLGGLG', '3' => 'LLGGGL', '4' => 'LGLLGG',
        '5' => 'LGGLLG', '6' => 'LGGGLL', '7' => 'LGLGLG', '8' => 'LGLGGL', '9' => 'LGGLGL',
    ];

    public function encode(string $data): Barcode
    {
        // Validate and pad/checksum
        if (!preg_match('/^\d{12,13}$/', $data)) {
            throw new \InvalidArgumentException('EAN-13 must be 12 or 13 digits');
        }
        if (strlen($data) === 12) {
            $data .= self::calculateChecksum(substr($data, 0, 12));
        }
        if (!self::validate($data)) {
            throw new \InvalidArgumentException('Invalid EAN-13 checksum');
        }
        $bars = [];
        // Start guard
        $pattern = '101';
        // Left side (digits 2-7, using parity from digit 1)
        $parity = self::$parity[$data[0]];
        for ($i = 1; $i <= 6; $i++) {
            $digit = $data[$i];
            $enc = $parity[$i-1] === 'L' ? self::$L[$digit] : self::$G[$digit];
            $pattern .= $enc;
        }
        // Center guard
        $pattern .= '01010';
        // Right side (digits 8-13, always R)
        for ($i = 7; $i <= 12; $i++) {
            $digit = $data[$i];
            $pattern .= self::$R[$digit];
        }
        // End guard
        $pattern .= '101';
        // Convert pattern to bars
        for ($i = 0; $i < strlen($pattern); $i++) {
            $bars[] = [1, $pattern[$i] === '1' ? 'black' : 'white'];
        }
        $width = count($bars);
        return new Barcode('EAN13', $data, $bars, $width);
    }

    public static function calculateChecksum(string $data): string
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $n = (int)$data[$i];
            $sum += ($i % 2 === 0) ? $n : $n * 3;
        }
        $check = (10 - ($sum % 10)) % 10;
        return (string)$check;
    }

    public function validate(string $data): bool
    {
        if (!preg_match('/^\d{13}$/', $data)) return false;
        return $data[12] === self::calculateChecksum(substr($data, 0, 12));
    }
} 