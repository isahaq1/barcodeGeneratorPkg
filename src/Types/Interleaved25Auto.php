<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Interleaved25Auto implements BarcodeTypeInterface
{
    public function encode(string $data): Barcode
    {
        // Try with checksum first, fallback to regular Interleaved25
        try {
            $interleaved25checksum = new \Isahaq\Barcode\Types\Interleaved25Checksum();
            return $interleaved25checksum->encode($data);
        } catch (\Exception $e) {
            $interleaved25 = new \Isahaq\Barcode\Types\Interleaved25();
            return $interleaved25->encode($data);
        }
    }

    public function validate(string $data): bool
    {
        // Check if data is valid for Interleaved25
        $interleaved25 = new \Isahaq\Barcode\Types\Interleaved25();
        return $interleaved25->validate($data);
    }
} 