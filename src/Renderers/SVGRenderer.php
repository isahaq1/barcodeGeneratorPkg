<?php

namespace Isahaq\Barcode\Renderers;

use Isahaq\Barcode\Barcode;

class SVGRenderer implements RendererInterface
{
    protected array $foregroundColor = [0, 0, 0];
    protected array $backgroundColor = [255, 255, 255];

    public function setForegroundColor(array $rgb): void
    {
        $this->foregroundColor = $rgb;
    }

    public function setBackgroundColor(array $rgb): void
    {
        $this->backgroundColor = $rgb;
    }

    public function render(Barcode $barcode, array $options = []): string
    {
        $bars = $barcode->bars;
        $widthFactor = $options['widthFactor'] ?? 2;
        $height = $options['height'] ?? 50;
        $fg = sprintf('rgb(%d,%d,%d)', ...$this->foregroundColor);
        $bg = sprintf('rgb(%d,%d,%d)', ...$this->backgroundColor);

        $totalWidth = 0;
        foreach ($bars as $bar) {
            $totalWidth += $bar[0] * $widthFactor;
        }
        $svg = "<svg width='{$totalWidth}' height='{$height}' xmlns='http://www.w3.org/2000/svg'>";
        $svg .= "<rect width='{$totalWidth}' height='{$height}' fill='{$bg}'/>";
        $x = 0;
        foreach ($bars as $bar) {
            $barWidth = $bar[0] * $widthFactor;
            if ($bar[1] === 'black') {
                $svg .= "<rect x='{$x}' y='0' width='{$barWidth}' height='{$height}' fill='{$fg}'/>";
            }
            $x += $barWidth;
        }
        $svg .= '</svg>';
        return $svg;
    }
} 