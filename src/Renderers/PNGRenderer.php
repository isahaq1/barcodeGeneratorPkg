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
        $widthFactor = isset($options['width']) ? max(2, (int)$options['width']) : 3; // Default to 3, min 2 for bold bars
        $height = isset($options['height']) ? (int)$options['height'] : 50;
        $fg = $this->foregroundColor;
        $bg = $this->backgroundColor;

        $margin = isset($options['margin']) ? (int)$options['margin'] : 20;
        $fontSize = isset($options['font_size']) ? (int)$options['font_size'] : 5;
        if ($fontSize < 1 || $fontSize > 5) {
            $fontSize = 5; // fallback to max allowed by GD
        }
        $text = isset($options['text']) && $options['text'] !== '' ? $options['text'] : $barcode->data;
        if (!$text) {
            $text = ' '; // Always render something to avoid skipping imagestring
        }

        $totalWidth = 0;
        foreach ($bars as $bar) {
            $totalWidth += $bar[0] * $widthFactor;
        }
        $imageWidth = $totalWidth + 2 * $margin;
        $imageHeight = $height + $margin + 20; // extra for text

        $im = imagecreatetruecolor($imageWidth, $imageHeight);
        $bgColor = imagecolorallocate($im, $bg[0], $bg[1], $bg[2]);
        $fgColor = imagecolorallocate($im, $fg[0], $fg[1], $fg[2]);
        imagefill($im, 0, 0, $bgColor);

        $x = $margin;
        foreach ($bars as $bar) {
            $barWidth = $bar[0] * $widthFactor;
            if ($bar[1] === 'black') {
                imagefilledrectangle($im, $x, $margin, $x + $barWidth - 1, $margin + $height, $fgColor);
            }
            $x += $barWidth;
        }

        // Draw text below barcode, centered
        $textBoxWidth = imagefontwidth($fontSize) * strlen($text);
        $textX = ($imageWidth - $textBoxWidth) / 2;
        $textY = $margin + $height + 2;
        imagestring($im, $fontSize, $textX, $textY, $text, $fgColor);

        ob_start();
        imagepng($im);
        $pngData = ob_get_clean();
        imagedestroy($im);
        return $pngData;
    }
} 