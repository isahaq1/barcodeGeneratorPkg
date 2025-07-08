<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class UPCE implements BarcodeTypeInterface
{
    // UPC-E encoding patterns for digits 0-9 (from spec)
    private static array $patterns = [
        '0' => '0001101', '1' => '0011001', '2' => '0010011', '3' => '0111101', '4' => '0100011',
        '5' => '0110001', '6' => '0101111', '7' => '0111011', '8' => '0110111', '9' => '0001011',
    ];
    // Parity patterns for number system 0 (from spec)
    private static array $parity = [
        '0' => ['E','E','E','O','O','O'],
        '1' => ['E','E','O','E','O','O'],
        '2' => ['E','E','O','O','E','O'],
        '3' => ['E','E','O','O','O','E'],
        '4' => ['E','O','E','E','O','O'],
        '5' => ['E','O','O','E','E','O'],
        '6' => ['E','O','O','O','E','E'],
        '7' => ['E','O','E','O','E','O'],
        '8' => ['E','O','E','O','O','E'],
        '9' => ['E','O','O','E','O','E'],
    ];

    public function encode(string $data): Barcode
    {
        // Accept 6, 7, or 8 digits (last is checksum)
        if (!preg_match('/^\d{6,8}$/', $data)) {
            throw new \InvalidArgumentException('UPC-E must be 6, 7, or 8 digits');
        }
        // Expand to UPC-A for validation and checksum
        $upca = self::expandToUPCA($data);
        if (strlen($data) < 8) {
            $data .= UPCA::calculateChecksum(substr($upca, 0, 11));
        }
        if (!self::validate($data)) {
            throw new \InvalidArgumentException('Invalid UPC-E checksum');
        }
        // Number system (assume 0 for most retail)
        $numberSystem = '0';
        $parityPattern = self::$parity[$data[6]];
        $bars = [];
        // Start guard
        $pattern = '101';
        // Encode 6 digits using parity
        for ($i = 0; $i < 6; $i++) {
            $digit = $data[$i];
            $parity = $parityPattern[$i];
            $enc = ($parity === 'O') ? self::$patterns[$digit] : self::invert(self::$patterns[$digit]);
            $pattern .= $enc;
        }
        // End guard
        $pattern .= '010101';
        // Convert pattern to bars
        for ($i = 0; $i < strlen($pattern); $i++) {
            $bars[] = [1, $pattern[$i] === '1' ? 'black' : 'white'];
        }
        $width = count($bars);
        return new Barcode('UPCE', $data, $bars, $width);
    }

    // Expand UPC-E to UPC-A (only for number system 0, no manufacturer code ending in 000, 100, 200, etc.)
    public static function expandToUPCA(string $data): string
    {
        $data = str_pad($data, 6, '0', STR_PAD_RIGHT);
        $ns = '0';
        $d = str_split($data);
        switch ($d[5]) {
            case '0': case '1': case '2':
                $upca = $ns . $d[0] . $d[1] . $d[5] . '0000' . $d[2] . $d[3] . $d[4];
                break;
            case '3':
                $upca = $ns . $d[0] . $d[1] . $d[2] . '00000' . $d[3] . $d[4];
                break;
            case '4':
                $upca = $ns . $d[0] . $d[1] . $d[2] . $d[3] . '00000' . $d[4];
                break;
            default:
                $upca = $ns . $d[0] . $d[1] . $d[2] . $d[3] . $d[4] . '0000' . $d[5];
                break;
        }
        return $upca;
    }

    public static function invert(string $bits): string
    {
        return strtr($bits, '01', '10');
    }

    public function validate(string $data): bool
    {
        if (!preg_match('/^\d{6,8}$/', $data)) return false;
        $upca = self::expandToUPCA(substr($data, 0, 6));
        $expected = UPCA::calculateChecksum(substr($upca, 0, 11));
        return $data[7] === $expected;
    }
} 