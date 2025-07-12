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
        return $qr->writeString();
    }

    /**
     * Generate a QR code with logo using QrCodeBuilder
     */
    public static function qrWithLogo(array $options): string
    {
        $qrCode = \Isahaq\Barcode\QrCodeBuilder::create()
            ->data($options['data'] ?? '')
            ->size($options['size'] ?? 300)
            ->margin($options['margin'] ?? 10)
            ->foregroundColor($options['foreground_color'] ?? [0, 0, 0])
            ->backgroundColor($options['background_color'] ?? [255, 255, 255]);
        
        if (isset($options['logoPath'])) {
            $qrCode->logoPath($options['logoPath']);
        }
        
        if (isset($options['label'])) {
            $qrCode->label($options['label']);
        }
        
        if (isset($options['labelFont'])) {
            $qrCode->labelFont($options['labelFont'], $options['labelFontSize'] ?? 14);
        }
        
        return $qrCode->format('png')->build()->getString();
    }
} 