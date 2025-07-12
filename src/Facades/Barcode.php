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
     * Generate a QR code with logo using ModernQRCode
     */
    public static function qrWithLogo(array $options): string
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
} 