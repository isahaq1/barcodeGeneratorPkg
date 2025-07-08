<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

class IMB implements BarcodeTypeInterface
{
    // IMB bar types: F=Full, A=Ascender, D=Descender, T=Tracker
    // Real IMB encoding is complex (F/A/D/T for each bar, based on input and Reed-Solomon ECC)
    // This is a placeholder for the bar sequence; full implementation would require full spec

    public function encode(string $data): Barcode
    {
        // Validate input: numeric, 20, 25, 29, or 31 digits
        if (!preg_match('/^\d{20}$|^\d{25}$|^\d{29}$|^\d{31}$/', $data)) {
            throw new \InvalidArgumentException('IMB must be 20, 25, 29, or 31 digits');
        }
        // Placeholder: encode as alternating bar types (for demo)
        $bars = [];
        $barTypes = ['F', 'A', 'D', 'T'];
        for ($i = 0; $i < strlen($data); $i++) {
            $bars[] = [2, 'black']; // In real, would encode bar height/type
        }
        $width = 0;
        foreach ($bars as $bar) { $width += $bar[0]; }
        return new Barcode('IMB', $data, $bars, $width);
    }

    public function validate(string $data): bool
    {
        return preg_match('/^\d{20}$|^\d{25}$|^\d{29}$|^\d{31}$/', $data);
    }
} 