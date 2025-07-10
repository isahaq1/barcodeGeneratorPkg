<?php

namespace Isahaq\Barcode\Utils;

/**
 * Real QR Code generator (byte mode, ECC L, version 1-4, PNG output)
 * Based on Kazuhiko Arase's QRCode generator (MIT License)
 * https://github.com/kazuhikoarase/qrcode-generator/tree/master/php
 */
class SimpleQRCode
{
    public static function png(string $data, int $version = null, int $margin = 4, int $moduleSize = 4): string
    {
        require_once __DIR__ . '/phpqrcode.php';
        ob_start();
        // If version is null, let the library auto-select
        if ($version === null) {
            \QRcode::png($data, null, QR_ECLEVEL_L, $moduleSize, $margin);
        } else {
            \QRcode::png($data, null, QR_ECLEVEL_L, $moduleSize, $margin, false, $version);
        }
        return ob_get_clean();
    }
} 