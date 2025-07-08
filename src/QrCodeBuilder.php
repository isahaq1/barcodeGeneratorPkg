<?php
namespace Isahaq\Barcode;

use Isahaq\Barcode\Renderers\PNGRenderer;
use Isahaq\Barcode\Renderers\SVGRenderer;

class QrCodeBuilder {
    private $data = '';
    private $encoding = 'UTF-8';
    private $errorCorrection = 'L';
    private $size = 300;
    private $margin = 10;
    private $foregroundColor = [0, 0, 0];
    private $backgroundColor = [255, 255, 255];
    private $format = 'png';
    private $logoPath = null;
    private $label = null;
    private $labelFont = null;
    private $labelFontSize = 14;

    public static function create() {
        return new self();
    }
    public function data($data) { $this->data = $data; return $this; }
    public function encoding($encoding) { $this->encoding = $encoding; return $this; }
    public function errorCorrectionLevel($level) { $this->errorCorrection = $level; return $this; }
    public function size($size) { $this->size = $size; return $this; }
    public function margin($margin) { $this->margin = $margin; return $this; }
    public function foregroundColor($color) { $this->foregroundColor = $color; return $this; }
    public function backgroundColor($color) { $this->backgroundColor = $color; return $this; }
    public function format($format) { $this->format = strtolower($format); return $this; }
    public function logoPath($path) { $this->logoPath = $path; return $this; }
    public function label($label) { $this->label = $label; return $this; }
    public function labelFont($fontPath, $fontSize = 14) { $this->labelFont = $fontPath; $this->labelFontSize = $fontSize; return $this; }

    public function build() {
        $options = [
            'encoding' => $this->encoding,
            'ecc' => $this->errorCorrection,
            'width' => $this->size,
            'height' => $this->size,
            'margin' => $this->margin,
            'foreground_color' => $this->foregroundColor,
            'background_color' => $this->backgroundColor,
            'logo_path' => $this->logoPath,
            'label' => $this->label,
            'label_font' => $this->labelFont,
            'label_font_size' => $this->labelFontSize,
        ];
        $barcode = new \Isahaq\Barcode\Types\QRCode();
        $barcodeObj = $barcode->encode($this->data, $options);
        $renderer = $this->format === 'svg' ? new SVGRenderer() : new PNGRenderer();
        $output = $renderer->render($barcodeObj, $options);
        return new QrCodeResult($output, $this->format, $this->logoPath, $this->label);
    }
}

class QrCodeResult {
    private $data;
    private $format;
    private $logoPath;
    private $label;
    public function __construct($data, $format, $logoPath = null, $label = null) {
        $this->data = $data;
        $this->format = $format;
        $this->logoPath = $logoPath;
        $this->label = $label;
    }
    public function getString() { return $this->data; }
    public function getMimeType() {
        return $this->format === 'svg' ? 'image/svg+xml' : 'image/png';
    }
    public function saveToFile($path) {
        file_put_contents($path, $this->data);
    }
    public function getDataUri() {
        $mime = $this->getMimeType();
        $base64 = base64_encode($this->data);
        return "data:$mime;base64,$base64";
    }
    public function getLogoPath() { return $this->logoPath; }
    public function getLabel() { return $this->label; }
} 