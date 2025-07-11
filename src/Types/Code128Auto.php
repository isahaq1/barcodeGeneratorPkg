<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class Code128Auto implements BarcodeTypeInterface
{
    public function encode(string $data): Barcode
    {
        // Auto-detect the best Code128 variant
        if (ctype_digit($data) && strlen($data) % 2 === 0) {
            // Use Code128C for even-length numeric data
            $code128c = new Code128C();
            return $code128c->encode($data);
        } elseif (ctype_digit($data) || preg_match('/^[A-Z0-9\s\-\.\/\+\%]+$/', $data)) {
            // Use Code128B for alphanumeric data
            $code128b = new Code128B();
            return $code128b->encode($data);
        } else {
            // Use Code128A for control characters and other data
            $code128a = new Code128A();
            return $code128a->encode($data);
        }
    }

    public function validate(string $data): bool
    {
        // Check if data is valid for any Code128 variant
        $code128a = new Code128A();
        $code128b = new Code128B();
        $code128c = new Code128C();
        
        return $code128a->validate($data) || $code128b->validate($data) || $code128c->validate($data);
    }
} 