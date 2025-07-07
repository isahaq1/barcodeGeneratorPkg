<?php

use PHPUnit\Framework\TestCase;
use UniversalBarcodeGenerator\Types\Code128;

class Code128Test extends TestCase
{
    public function testValidEncoding()
    {
        $type = new Code128();
        $barcode = $type->encode('A B');
        $this->assertEquals('Code128', $barcode->type);
        $this->assertEquals('A B', $barcode->data);
        $this->assertIsArray($barcode->bars);
        $this->assertGreaterThan(0, $barcode->getWidth());
    }

    public function testInvalidEncoding()
    {
        $type = new Code128();
        $this->assertFalse($type->validate("@")); // '@' not in demo pattern
    }
} 