<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Code39Auto implements BarcodeTypeInterface
{
    public function encode(string $data): Barcode
    {
        // Try with checksum first, fallback to regular Code39
        try {
            $code39checksum = new \Isahaq\Barcode\Types\Code39Checksum();
            return $code39checksum->encode($data);
        } catch (\Exception $e) {
            $code39 = new \Isahaq\Barcode\Types\Code39();
            return $code39->encode($data);
        }
    }

    public function validate(string $data): bool
    {
        // Check if data is valid for Code39
        $code39 = new \Isahaq\Barcode\Types\Code39();
        return $code39->validate($data);
    }
} 