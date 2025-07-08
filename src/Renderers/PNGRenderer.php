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
        $fg = $this->foregroundColor;
        $bg = $this->backgroundColor;
        $margin = $options['margin'] ?? 20;
        $fontSize = $options['font_size'] ?? 5;
        $text = $options['text'] ?? $barcode->data ?: ' ';

        // --- QRCode 2D rendering branch FIRST ---
        if ($barcode->type === 'QRCode') {
            $size = (int)sqrt(count($bars));
            $moduleSize = $options['module_size'] ?? 8;
            $imageWidth = $size * $moduleSize + 2 * $margin;
            $imageHeight = $size * $moduleSize + 2 * $margin + 20;
            $im = imagecreatetruecolor($imageWidth, $imageHeight);
            $bgColor = imagecolorallocate($im, $bg[0], $bg[1], $bg[2]);
            $fgColor = imagecolorallocate($im, $fg[0], $fg[1], $fg[2]);
            imagefill($im, 0, 0, $bgColor);
            for ($y = 0; $y < $size; $y++) {
                for ($x = 0; $x < $size; $x++) {
                    $idx = $y * $size + $x;
                    if ($bars[$idx][1] === 'black') {
                        imagefilledrectangle(
                            $im,
                            $margin + $x * $moduleSize,
                            $margin + $y * $moduleSize,
                            $margin + ($x + 1) * $moduleSize - 1,
                            $margin + ($y + 1) * $moduleSize - 1,
                            $fgColor
                        );
                    }
                }
            }
            // Draw text below QR code, centered
            $textBoxWidth = imagefontwidth($fontSize) * strlen($text);
            $textX = ($imageWidth - $textBoxWidth) / 2;
            $textY = $margin + $size * $moduleSize + 2;
            imagestring($im, $fontSize, $textX, $textY, $text, $fgColor);
            ob_start();
            imagepng($im);
            $pngData = ob_get_clean();
            imagedestroy($im);
            return $pngData;
        }
        // --- end QRCode branch ---

        // --- 1D barcode rendering for all other types ---
        $widthFactor = $options['width'] ?? 3;
        $height = $options['height'] ?? 50;
        $totalWidth = 0;
        foreach ($bars as $bar) {
            $totalWidth += $bar[0] * $widthFactor;
        }
        $imageWidth = $totalWidth + 2 * $margin;
        $imageHeight = $height + $margin + 20;
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