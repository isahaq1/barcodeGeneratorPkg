<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Code25Auto implements BarcodeTypeInterface
{
    public function encode(string $data): Barcode
    {
        // Try with checksum first, fallback to regular Code25
        try {
            $code25checksum = new \Isahaq\Barcode\Types\Standard25Checksum();
            return $code25checksum->encode($data);
        } catch (\Exception $e) {
            $code25 = new \Isahaq\Barcode\Types\Standard25();
            return $code25->encode($data);
        }
    }

    public function validate(string $data): bool
    {
        // Check if data is valid for Code25
        $code25 = new \Isahaq\Barcode\Types\Standard25();
        return $code25->validate($data);
    }
} 