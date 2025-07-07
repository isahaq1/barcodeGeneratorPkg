<?php

namespace Isahaq\Barcode\Types;

use Isahaq\Barcode\Barcode;

interface BarcodeTypeInterface
{
    public function encode(string $data): Barcode;
    public function validate(string $data): bool;
} 