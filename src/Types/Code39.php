<?php

namespace UniversalBarcodeGenerator\Types;

use UniversalBarcodeGenerator\Barcode;

class Code39 implements BarcodeTypeInterface
{
    public function encode(string $data): Barcode
    {
        // Stub: Replace with real Code39 encoding logic
        $bars = [];
        $toggle = true;
        foreach (str_split($data) as $char) {
            $bars[] = [2, $toggle ? 'black' : 'white'];
            $toggle = !$toggle;
        }
        return new Barcode('Code39', $data, $bars, count($bars) * 2);
    }

    public function validate(string $data): bool
    {
        // Stub: Add real validation logic
        return !empty($data);
    }
} 