<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class MSIAuto implements BarcodeTypeInterface
{
    public function encode(string $data): Barcode
    {
        // Try with checksum first, fallback to regular MSI
        try {
            $msichecksum = new \Isahaq\Barcode\Types\MSIChecksum();
            return $msichecksum->encode($data);
        } catch (\Exception $e) {
            $msi = new \Isahaq\Barcode\Types\MSI();
            return $msi->encode($data);
        }
    }

    public function validate(string $data): bool
    {
        // Check if data is valid for MSI
        $msi = new \Isahaq\Barcode\Types\MSI();
        return $msi->validate($data);
    }
} 