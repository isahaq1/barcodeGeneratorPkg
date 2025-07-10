<?php

namespace Isahaq\Barcode\Utils;

/**
 * Real QR Code generator (byte mode, ECC L, version 1-4, PNG output)
 * Based on Kazuhiko Arase's QRCode generator (MIT License)
 * https://github.com/kazuhikoarase/qrcode-generator/tree/master/php
 */
class SimpleQRCode
{
    private string $data;
    private int $version;
    private int $margin;
    private int $moduleSize;
    private $qr;

    public function __construct(string $data, int $version = 2, int $margin = 4, int $moduleSize = 8)
    {
        $this->data = $data;
        $this->version = $version;
        $this->margin = $margin;
        $this->moduleSize = $moduleSize;
    }

    /**
     * Generate the QR code matrix using a real encoder
     */
    public function generateMatrix(): array
    {
        require_once __DIR__ . '/qrcode-php-standalone.php'; // This file will contain the QR encoder class
        $qr = new \QRCode();
        $qr->setErrorCorrectLevel('L');
        $qr->setTypeNumber($this->version);
        $qr->addData($this->data);
        $qr->make();
        $size = $qr->getModuleCount();
        $matrix = [];
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $matrix[$y][$x] = $qr->isDark($x, $y) ? 1 : 0;
            }
        }
        $this->qr = $qr;
        return $matrix;
    }

    public function renderPNG(): string
    {
        $matrix = $this->generateMatrix();
        $size = count($matrix);
        $imgSize = ($size + 2 * $this->margin) * $this->moduleSize;
        $im = imagecreatetruecolor($imgSize, $imgSize);
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        imagefill($im, 0, 0, $white);
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if ($matrix[$y][$x]) {
                    imagefilledrectangle(
                        $im,
                        ($x + $this->margin) * $this->moduleSize,
                        ($y + $this->margin) * $this->moduleSize,
                        ($x + $this->margin + 1) * $this->moduleSize - 1,
                        ($y + $this->margin + 1) * $this->moduleSize - 1,
                        $black
                    );
                }
            }
        }
        ob_start();
        imagepng($im);
        $pngData = ob_get_clean();
        imagedestroy($im);
        return $pngData;
    }

    public static function png(string $data, int $version = 2, int $margin = 4, int $moduleSize = 8): string
    {
        $qr = new self($data, $version, $margin, $moduleSize);
        return $qr->renderPNG();
    }
} 