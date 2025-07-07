<?php

namespace Isahaq\Barcode\Renderers;

use Isahaq\Barcode\Barcode;

class PDFRenderer implements RendererInterface
{
    public function render(Barcode $barcode, array $options = []): string
    {
        if (!class_exists('FPDF')) {
            return 'FPDF library not installed. PDF rendering not available.';
        }
        $bars = $barcode->bars;
        $widthFactor = $options['widthFactor'] ?? 2;
        $height = $options['height'] ?? 50;
        $pdf = new \FPDF('P', 'pt', [$barcode->getWidth() * $widthFactor, $height]);
        $pdf->AddPage();
        $x = 0;
        foreach ($bars as $bar) {
            $barWidth = $bar[0] * $widthFactor;
            if ($bar[1] === 'black') {
                $pdf->Rect($x, 0, $barWidth, $height, 'F');
            }
            $x += $barWidth;
        }
        ob_start();
        $pdf->Output('S');
        $pdfData = ob_get_clean();
        return $pdfData;
    }
} 