<?php

namespace Isahaq\Barcode\Utils;

class Watermark
{
    public static function apply(string $imageData, string $watermarkPath): string
    {
        // Only works for PNG images (demo logic)
        $barcodeIm = imagecreatefromstring($imageData);
        $watermarkIm = @imagecreatefrompng($watermarkPath);
        if (!$barcodeIm || !$watermarkIm) {
            return $imageData; // fallback: return original
        }
        $bw = imagesx($barcodeIm);
        $bh = imagesy($barcodeIm);
        $ww = imagesx($watermarkIm);
        $wh = imagesy($watermarkIm);
        // Place watermark at bottom right
        imagecopy($barcodeIm, $watermarkIm, $bw - $ww, $bh - $wh, 0, 0, $ww, $wh);
        ob_start();
        imagepng($barcodeIm);
        $result = ob_get_clean();
        imagedestroy($barcodeIm);
        imagedestroy($watermarkIm);
        return $result;
    }
} 