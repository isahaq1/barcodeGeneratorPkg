<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class EAN8 implements BarcodeTypeInterface
{
    // L and R encoding patterns for digits 0-9
    private static array $L = [
        '0' => '0001101', '1' => '0011001', '2' => '0010011', '3' => '0111101', '4' => '0100011',
        '5' => '0110001', '6' => '0101111', '7' => '0111011', '8' => '0110111', '9' => '0001011',
    ];
    private static array $R = [
        '0' => '1110010', '1' => '1100110', '2' => '1101100', '3' => '1000010', '4' => '1011100',
        '5' => '1001110', '6' => '1010000', '7' => '1000100', '8' => '1001000', '9' => '1110100',
    ];

    public function encode(string $data): Barcode
    {
        // Validate and pad/checksum
        if (!preg_match('/^\d{7,8}$/', $data)) {
            throw new \InvalidArgumentException('EAN-8 must be 7 or 8 digits');
        }
        if (strlen($data) === 7) {
            $data .= self::calculateChecksum(substr($data, 0, 7));
        }
        if (!self::validate($data)) {
            throw new \InvalidArgumentException('Invalid EAN-8 checksum');
        }
        $bars = [];
        // Start guard
        $pattern = '101';
        // Left side (digits 1-4, L)
        for ($i = 0; $i < 4; $i++) {
            $digit = $data[$i];
            $pattern .= self::$L[$digit];
        }
        // Center guard
        $pattern .= '01010';
        // Right side (digits 5-8, R)
        for ($i = 4; $i < 8; $i++) {
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
        return new Barcode('EAN8', $data, $bars, $width);
    }

    public static function calculateChecksum(string $data): string
    {
        $sum = 0;
        for ($i = 0; $i < 7; $i++) {
            $n = (int)$data[$i];
            $sum += ($i % 2 === 0) ? $n * 3 : $n;
        }
        $check = (10 - ($sum % 10)) % 10;
        return (string)$check;
    }

    public function validate(string $data): bool
    {
        if (!preg_match('/^\d{8}$/', $data)) return false;
        return $data[7] === self::calculateChecksum(substr($data, 0, 7));
    }
} 