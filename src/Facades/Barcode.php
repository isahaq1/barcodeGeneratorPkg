<?php

namespace Isahaq\Barcode\Facades;

use Illuminate\Support\Facades\Facade;

class Barcode extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'barcode';
    }

    /**
     * Generate a QR code using ModernQRCode (endroid/qr-code style)
     */
    public static function modernQr(array $options): string
    {
        $qr = new \Isahaq\Barcode\Utils\ModernQRCode();
        if (isset($options['data'])) $qr->setData($options['data']);
        if (isset($options['size'])) $qr->setSize($options['size']);
        if (isset($options['margin'])) $qr->setMargin($options['margin']);
        if (isset($options['error_correction'])) $qr->setErrorCorrection($options['error_correction']);
        if (isset($options['foreground_color'])) $qr->setForegroundColor($options['foreground_color']);
        if (isset($options['background_color'])) $qr->setBackgroundColor($options['background_color']);
        if (isset($options['label'])) $qr->setLabel($options['label']);
        if (isset($options['logoPath'])) $qr->setLogo($options['logoPath'], $options['logoSize'] ?? 60);
        return $qr->writeString();
    }

    /**
     * Generate barcode with image watermark
     */
    public static function withWatermark(
        string $data, 
        string $type = 'code128', 
        string $format = 'png',
        string $watermarkPath = '',
        array $watermarkOptions = []
    ): string {
        $service = app('barcode');
        return $service->withWatermark($data, $type, $format, $watermarkPath, $watermarkOptions);
    }

    /**
     * Generate barcode with text watermark
     */
    public static function withTextWatermark(
        string $data, 
        string $type = 'code128', 
        string $format = 'png',
        string $text = '',
        array $textOptions = []
    ): string {
        $service = app('barcode');
        return $service->withTextWatermark($data, $type, $format, $text, $textOptions);
    }

    /**
     * Get available watermark positions
     */
    public static function getWatermarkPositions(): array
    {
        $service = app('barcode');
        return $service->getWatermarkPositions();
    }

    /**
     * Debug logo loading
     */
    public static function debugLogo(string $logoPath): array
    {
        $qr = new \Isahaq\Barcode\Utils\ModernQRCode();
        return $qr->debugLogo($logoPath);
    }
} 