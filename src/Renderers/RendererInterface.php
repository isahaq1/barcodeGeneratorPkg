<?php

namespace Isahaq\Barcode\Renderers;

use Isahaq\Barcode\Barcode;
 
interface RendererInterface
{
    public function render(Barcode $barcode, array $options = []): string;
} 