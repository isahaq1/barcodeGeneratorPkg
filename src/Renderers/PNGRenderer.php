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
        $imageHeight = $height + $margin + 24;
        $im = imagecreatetruecolor($imageWidth, $imageHeight);
        $bgColor = imagecolorallocate($im, $bg[0], $bg[1], $bg[2]);
        $fgColor = imagecolorallocate($im, $fg[0], $fg[1], $fg[2]);
        imagefill($im, 0, 0, $bgColor);

        // Special rendering for EAN-13
        if ($barcode->type === 'EAN13' && strlen($barcode->data) === 13) {
            $guardBarHeight = $height + 8;
            $normalBarHeight = $height;
            $x = $margin;
            // Guard bar positions in EAN-13 pattern (fixed positions)
            $guardBarIdx = [0, 1, 2, 45, 46, 47, 92, 93, 94];
            foreach ($bars as $i => $bar) {
                $barWidth = $bar[0] * $widthFactor;
                $isGuard = in_array($i, $guardBarIdx);
                $barH = $isGuard ? $guardBarHeight : $normalBarHeight;
                if ($bar[1] === 'black') {
                    imagefilledrectangle($im, $x, $margin, $x + $barWidth - 1, $margin + $barH, $fgColor);
                }
                $x += $barWidth;
            }
            // Draw numbers
            $font = 3;
            $num = $barcode->data;
            // First digit (left of bars)
            $firstX = $margin - imagefontwidth($font) * 1.5;
            $firstY = $margin + $height + 2;
            imagestring($im, $font, $firstX, $firstY, $num[0], $fgColor);
            // Left 6 digits (under left half)
            $x = $margin + 3 * $widthFactor; // after first guard
            for ($i = 1; $i <= 6; $i++) {
                $digitX = $x + (($i - 1) * 7 * $widthFactor) + 2;
                imagestring($im, $font, $digitX, $firstY, $num[$i], $fgColor);
            }
            // Right 6 digits (under right half)
            $x = $margin + (3 + 42 + 5) * $widthFactor; // after center guard
            for ($i = 7; $i <= 12; $i++) {
                $digitX = $x + (($i - 7) * 7 * $widthFactor) + 2;
                imagestring($im, $font, $digitX, $firstY, $num[$i], $fgColor);
            }
            ob_start();
            imagepng($im);
            $pngData = ob_get_clean();
            imagedestroy($im);
            return $pngData;
        }
        // Special rendering for EAN-8
        if ($barcode->type === 'EAN8' && strlen($barcode->data) === 8) {
            $guardBarHeight = $height + 8;
            $normalBarHeight = $height;
            $x = $margin;
            // Guard bar positions in EAN-8 pattern (fixed positions)
            $guardBarIdx = [0, 1, 2, 31, 32, 33, 66, 67, 68];
            foreach ($bars as $i => $bar) {
                $barWidth = $bar[0] * $widthFactor;
                $isGuard = in_array($i, $guardBarIdx);
                $barH = $isGuard ? $guardBarHeight : $normalBarHeight;
                if ($bar[1] === 'black') {
                    imagefilledrectangle($im, $x, $margin, $x + $barWidth - 1, $margin + $barH, $fgColor);
                }
                $x += $barWidth;
            }
            // Draw numbers
            $font = 3;
            $num = $barcode->data;
            $firstY = $margin + $height + 2;
            // Left 4 digits
            $x = $margin + 3 * $widthFactor;
            for ($i = 0; $i < 4; $i++) {
                $digitX = $x + ($i * 7 * $widthFactor) + 2;
                imagestring($im, $font, $digitX, $firstY, $num[$i], $fgColor);
            }
            // Right 4 digits
            $x = $margin + (3 + 28 + 5) * $widthFactor;
            for ($i = 4; $i < 8; $i++) {
                $digitX = $x + (($i - 4) * 7 * $widthFactor) + 2;
                imagestring($im, $font, $digitX, $firstY, $num[$i], $fgColor);
            }
            ob_start();
            imagepng($im);
            $pngData = ob_get_clean();
            imagedestroy($im);
            return $pngData;
        }
        // Special rendering for all Code39 variants
        if (strpos($barcode->type, 'Code39') === 0) {
            $narrow = $options['narrow'] ?? 2;
            $wide = $options['wide'] ?? 5;
            $height = $options['height'] ?? 50;
            $margin = $options['margin'] ?? 20;
            $font = 4;
            $patternBars = [];
            // Convert bars to wide/narrow
            foreach ($barcode->bars as $i => $bar) {
                $barWidth = ($bar[0] === 1) ? $narrow : $wide;
                $patternBars[] = [$barWidth, $bar[1]];
            }
            $totalWidth = 0;
            foreach ($patternBars as $bar) {
                $totalWidth += $bar[0];
            }
            $imageWidth = $totalWidth + 2 * $margin;
            $imageHeight = $height + $margin + 24;
            $im = imagecreatetruecolor($imageWidth, $imageHeight);
            $bgColor = imagecolorallocate($im, $bg[0], $bg[1], $bg[2]);
            $fgColor = imagecolorallocate($im, $fg[0], $fg[1], $fg[2]);
            imagefill($im, 0, 0, $bgColor);
            $x = $margin;
            foreach ($patternBars as $bar) {
                if ($bar[1] === 'black') {
                    imagefilledrectangle($im, $x, $margin, $x + $bar[0] - 1, $margin + $height, $fgColor);
                }
                $x += $bar[0];
            }
            // Human-readable text (strip * from start/end)
            $text = $barcode->data;
            if (strlen($text) > 2 && $text[0] === '*' && $text[strlen($text)-1] === '*') {
                $text = substr($text, 1, -1);
            }
            $textBoxWidth = imagefontwidth($font) * strlen($text);
            $textX = ($imageWidth - $textBoxWidth) / 2;
            $textY = $margin + $height + 2;
            imagestring($im, $font, $textX, $textY, $text, $fgColor);
            ob_start();
            imagepng($im);
            $pngData = ob_get_clean();
            imagedestroy($im);
            return $pngData;
        }
        // Default rendering for other 1D barcodes
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