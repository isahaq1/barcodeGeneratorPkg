<?php

namespace Isahaq\Barcode\Utils;

/**
 * Simple QR Code generator (byte mode, ECC L, version 1-4, PNG output)
 * Inspired by BaconQrCode, but self-contained and minimal.
 */
class SimpleQRCode
{
    private string $data;
    private int $version;
    private int $margin;
    private int $moduleSize;
    private array $matrix = [];

    public function __construct(string $data, int $version = 2, int $margin = 4, int $moduleSize = 8)
    {
        $this->data = $data;
        $this->version = $version;
        $this->margin = $margin;
        $this->moduleSize = $moduleSize;
    }

    /**
     * Generate the QR code matrix (dummy implementation for demo)
     * In production, use a full QR code algorithm. Here, we use a placeholder pattern.
     */
    public function generateMatrix(): array
    {
        // For demo: create a checkerboard pattern (replace with real QR logic for production)
        $size = 21 + ($this->version - 1) * 4;
        $matrix = [];
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $matrix[$y][$x] = (($x + $y) % 2 === 0) ? 1 : 0;
            }
        }
        $this->matrix = $matrix;
        return $matrix;
    }

    /**
     * Render the QR code as a PNG image (GD required)
     */
    public function renderPNG(): string
    {
        $matrix = $this->matrix ?: $this->generateMatrix();
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

    /**
     * Static helper for one-shot QR code PNG generation
     */
    public static function png(string $data, int $version = 2, int $margin = 4, int $moduleSize = 8): string
    {
        $qr = new self($data, $version, $margin, $moduleSize);
        $qr->generateMatrix();
        return $qr->renderPNG();
    }
} 