<?php

namespace Isahaq\Barcode\Renderers;

use Isahaq\Barcode\Barcode;

class PNGRenderer implements RendererInterface
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
        $fg = $this->foregroundColor;
        $bg = $this->backgroundColor;

        $totalWidth = 0;
        foreach ($bars as $bar) {
            $totalWidth += $bar[0] * $widthFactor;
        }
        $im = imagecreatetruecolor($totalWidth, $height);
        $bgColor = imagecolorallocate($im, $bg[0], $bg[1], $bg[2]);
        $fgColor = imagecolorallocate($im, $fg[0], $fg[1], $fg[2]);
        imagefill($im, 0, 0, $bgColor);

        $x = 0;
        foreach ($bars as $bar) {
            $barWidth = $bar[0] * $widthFactor;
            $color = $bar[1] === 'black' ? $fgColor : $bgColor;
            if ($bar[1] === 'black') {
                imagefilledrectangle($im, $x, 0, $x + $barWidth - 1, $height, $color);
            }
            $x += $barWidth;
        }

        ob_start();
        imagepng($im);
        $pngData = ob_get_clean();
        imagedestroy($im);
        return $pngData;
    }
} 