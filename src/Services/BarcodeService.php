<?php

namespace Isahaq\Barcode\Services;

use Isahaq\Barcode\Types\Code128;
use Isahaq\Barcode\Renderers\PNGRenderer;
use Isahaq\Barcode\Renderers\SVGRenderer;
use Isahaq\Barcode\Renderers\HTMLRenderer;

class BarcodeService
{
    public function png(string $data, array $options = []): string
    {
        $type = new Code128();
        $barcode = $type->encode($data);
        $renderer = new PNGRenderer();
        return $renderer->render($barcode, $options);
    }

    public function svg(string $data, array $options = []): string
    {
        $type = new Code128();
        $barcode = $type->encode($data);
        $renderer = new SVGRenderer();
        return $renderer->render($barcode, $options);
    }

    public function html(string $data, array $options = []): string
    {
        $type = new Code128();
        $barcode = $type->encode($data);
        $renderer = new HTMLRenderer();
        return $renderer->render($barcode, $options);
    }
} 