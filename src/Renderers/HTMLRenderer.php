<?php

namespace Isahaq\Barcode\Renderers;

use Isahaq\Barcode\Barcode;

class HTMLRenderer implements RendererInterface
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

        $html = "<div style='display:inline-block;background:{$bg};height:{$height}px;'>";
        foreach ($bars as $bar) {
            $barWidth = $bar[0] * $widthFactor;
            $color = $bar[1] === 'black' ? $fg : $bg;
            $html .= "<div style='display:inline-block;width:{$barWidth}px;height:{$height}px;background:{$color};margin:0;padding:0'></div>";
        }
        $html .= '</div>';
        return $html;
    }
} 