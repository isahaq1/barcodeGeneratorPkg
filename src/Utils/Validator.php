<?php

namespace Isahaq\Barcode\Utils;

use Isahaq\Barcode\Types\BarcodeTypeInterface;

class Validator
{
    public static function validate(BarcodeTypeInterface $type, string $data, ?string &$error = null): bool
    {
        if (!$type->validate($data)) {
            $error = 'Invalid data for barcode type.';
            return false;
        }
        return true;
    }
} 