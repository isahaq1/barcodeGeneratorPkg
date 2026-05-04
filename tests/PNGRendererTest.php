<?php

use PHPUnit\Framework\TestCase;
use Isahaq\Barcode\Types\Code128;
use Isahaq\Barcode\Renderers\PNGRenderer;

class PNGRendererTest extends TestCase
{
    public function testRenderPNG()
    {
        $type = new Code128();
        $barcode = $type->encode('A B');
        $renderer = new PNGRenderer();
        $png = $renderer->render($barcode);
        $this->assertStringStartsWith("\x89PNG", $png); // PNG signature
    }
} 