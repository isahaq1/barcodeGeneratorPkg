<?php

namespace Isahaq\Barcode\Utils;

class ModernQRCode
{
    private string $data = '';
    private int $size = 300; // Output image size in pixels
    private int $margin = 10;
    private string $errorCorrection = 'L'; // L, M, Q, H
    private array $foregroundColor = [0, 0, 0];
    private array $backgroundColor = [255, 255, 255];
    private ?string $label = null;

    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function setMargin(int $margin): self
    {
        $this->margin = $margin;
        return $this;
    }

    public function setErrorCorrection(string $level): self
    {
        $this->errorCorrection = strtoupper($level);
        return $this;
    }

    public function setForegroundColor(array $rgb): self
    {
        $this->foregroundColor = $rgb;
        return $this;
    }

    public function setBackgroundColor(array $rgb): self
    {
        $this->backgroundColor = $rgb;
        return $this;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function writeString(): string
    {
        require_once __DIR__ . '/phpqrcode.php';
        // Generate a base QR code image
        ob_start();
        \QRcode::png($this->data, null, constant('QR_ECLEVEL_' . $this->errorCorrection), 1, $this->margin, false, false);
        $rawPng = ob_get_clean();
        $srcIm = imagecreatefromstring($rawPng);
        $srcW = imagesx($srcIm);
        $srcH = imagesy($srcIm);
        $moduleSize = max(1, intval($this->size / max($srcW, $srcH)));
        // Generate QR with correct module size
        ob_start();
        \QRcode::png($this->data, null, constant('QR_ECLEVEL_' . $this->errorCorrection), $moduleSize, $this->margin, false, false);
        $pngData = ob_get_clean();
        $im = imagecreatefromstring($pngData);
        // Recolor if needed
        if ($this->foregroundColor !== [0,0,0] || $this->backgroundColor !== [255,255,255]) {
            $im = $this->recolor($im, $this->foregroundColor, $this->backgroundColor);
        }
        // Add label if needed
        if ($this->label) {
            $im = $this->addLabel($im, $this->label, $this->size);
        }
        ob_start();
        imagepng($im);
        $finalPng = ob_get_clean();
        imagedestroy($im);
        return $finalPng;
    }

    public function writeFile(string $path): void
    {
        file_put_contents($path, $this->writeString());
    }

    public function display(): void
    {
        header('Content-Type: image/png');
        echo $this->writeString();
        exit;
    }

    private function recolor($im, $fg, $bg)
    {
        $w = imagesx($im);
        $h = imagesy($im);
        $newIm = imagecreatetruecolor($w, $h);
        $fgColor = imagecolorallocate($newIm, $fg[0], $fg[1], $fg[2]);
        $bgColor = imagecolorallocate($newIm, $bg[0], $bg[1], $bg[2]);
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgb = imagecolorat($im, $x, $y);
                if ($rgb == 0x000000) {
                    imagesetpixel($newIm, $x, $y, $fgColor);
                } else {
                    imagesetpixel($newIm, $x, $y, $bgColor);
                }
            }
        }
        imagedestroy($im);
        return $newIm;
    }

    private function addLabel($im, $label, $size)
    {
        $font = 2;
        $labelHeight = imagefontheight($font) + 6;
        $w = imagesx($im);
        $h = imagesy($im);
        $newIm = imagecreatetruecolor($w, $h + $labelHeight);
        $bgColor = imagecolorallocate($newIm, $this->backgroundColor[0], $this->backgroundColor[1], $this->backgroundColor[2]);
        imagefill($newIm, 0, 0, $bgColor);
        imagecopy($newIm, $im, 0, 0, 0, 0, $w, $h);
        $fgColor = imagecolorallocate($newIm, $this->foregroundColor[0], $this->foregroundColor[1], $this->foregroundColor[2]);
        $labelWidth = imagefontwidth($font) * strlen($label);
        $x = ($w - $labelWidth) / 2;
        $y = $h + 3;
        imagestring($newIm, $font, $x, $y, $label, $fgColor);
        imagedestroy($im);
        return $newIm;
    }
} 