<?php

namespace Isahaq\Barcode\Utils;

class SimpleQRCode
{
    public static function png(string $data, int $version = 2, int $margin = 4, int $moduleSize = 8): string
    {
        require_once __DIR__ . '/phpqrcode.php';
        ob_start();
        \QRcode::png($data, null, QR_ECLEVEL_L, $moduleSize, $margin);
        return ob_get_clean();
    }
} 