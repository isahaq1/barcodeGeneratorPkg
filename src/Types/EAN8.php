<?php

namespace UniversalBarcodeGenerator\Types;

use UniversalBarcodeGenerator\Barcode;

class EAN8 implements BarcodeTypeInterface
{
    public function encode(string $data): Barcode
    {
        // Stub: Replace with real EAN8 encoding logic
        $bars = [];
        $toggle = true;
        foreach (str_split($data) as $char) {
            $bars[] = [2, $toggle ? 'black' : 'white'];
            $toggle = !$toggle;
        }
        return new Barcode('EAN8', $data, $bars, count($bars) * 2);
    }

    public function validate(string $data): bool
    {
        // Stub: Add real validation logic
        return !empty($data);
    }
} 