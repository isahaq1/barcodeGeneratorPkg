<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class UPCA implements BarcodeTypeInterface
{
    // L and R encoding patterns for digits 0-9 (same as EAN-13)
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
        if (!preg_match('/^\d{11,12}$/', $data)) {
            throw new \InvalidArgumentException('UPC-A must be 11 or 12 digits');
        }
        if (strlen($data) === 11) {
            $data .= self::calculateChecksum(substr($data, 0, 11));
        }
        if (!self::validate($data)) {
            throw new \InvalidArgumentException('Invalid UPC-A checksum');
        }
        $bars = [];
        // UPC-A is encoded as EAN-13 with a leading zero
        $ean13 = '0' . $data;
        // Start guard
        $pattern = '101';
        // Left side (digits 2-7, L)
        for ($i = 1; $i <= 6; $i++) {
            $digit = $ean13[$i];
            $pattern .= self::$L[$digit];
        }
        // Center guard
        $pattern .= '01010';
        // Right side (digits 8-13, R)
        for ($i = 7; $i <= 12; $i++) {
            $digit = $ean13[$i];
            $pattern .= self::$R[$digit];
        }
        // End guard
        $pattern .= '101';
        // Convert pattern to bars
        for ($i = 0; $i < strlen($pattern); $i++) {
            $bars[] = [1, $pattern[$i] === '1' ? 'black' : 'white'];
        }
        $width = count($bars);
        return new Barcode('UPCA', $data, $bars, $width);
    }

    public static function calculateChecksum(string $data): string
    {
        $sum = 0;
        for ($i = 0; $i < 11; $i++) {
            $n = (int)$data[$i];
            $sum += ($i % 2 === 0) ? $n * 3 : $n;
        }
        $check = (10 - ($sum % 10)) % 10;
        return (string)$check;
    }

    public function validate(string $data): bool
    {
        if (!preg_match('/^\d{12}$/', $data)) return false;
        return $data[11] === self::calculateChecksum(substr($data, 0, 11));
    }
} 