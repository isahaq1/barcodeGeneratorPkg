<?php

namespace Isahaq\Barcode\Services;

use Isahaq\Barcode\Types\Code128;
use Isahaq\Barcode\Types\Code128A;
use Isahaq\Barcode\Types\Code128B;
use Isahaq\Barcode\Types\Code128C;
use Isahaq\Barcode\Types\Code128Auto;
use Isahaq\Barcode\Types\Code39;
use Isahaq\Barcode\Types\Code39Checksum;
use Isahaq\Barcode\Types\Code39E;
use Isahaq\Barcode\Types\Code39EChecksum;
use Isahaq\Barcode\Types\Code39Auto;
use Isahaq\Barcode\Types\Code93;
use Isahaq\Barcode\Types\Code25;
use Isahaq\Barcode\Types\Code25Auto;
use Isahaq\Barcode\Types\Code32;
use Isahaq\Barcode\Types\Standard25;
use Isahaq\Barcode\Types\Standard25Checksum;
use Isahaq\Barcode\Types\Interleaved25;
use Isahaq\Barcode\Types\Interleaved25Checksum;
use Isahaq\Barcode\Types\Interleaved25Auto;
use Isahaq\Barcode\Types\EAN2;
use Isahaq\Barcode\Types\EAN5;
use Isahaq\Barcode\Types\EAN8;
use Isahaq\Barcode\Types\EAN13;
use Isahaq\Barcode\Types\ITF14;
use Isahaq\Barcode\Types\UPCA;
use Isahaq\Barcode\Types\UPCE;
use Isahaq\Barcode\Types\MSI;
use Isahaq\Barcode\Types\MSIChecksum;
use Isahaq\Barcode\Types\MSIAuto;
use Isahaq\Barcode\Types\POSTNET;
use Isahaq\Barcode\Types\PLANET;
use Isahaq\Barcode\Types\RMS4CC;
use Isahaq\Barcode\Types\KIX;
use Isahaq\Barcode\Types\IMB;
use Isahaq\Barcode\Types\Codabar;
use Isahaq\Barcode\Types\Code11;
use Isahaq\Barcode\Types\PharmaCode;
use Isahaq\Barcode\Types\PharmaCodeTwoTracks;
use Isahaq\Barcode\Types\QRCode;
use Isahaq\Barcode\Types\DataMatrix;
use Isahaq\Barcode\Types\Aztec;
use Isahaq\Barcode\Types\PDF417;
use Isahaq\Barcode\Types\MicroQR;
use Isahaq\Barcode\Types\Maxicode;
use Isahaq\Barcode\Types\Code16K;
use Isahaq\Barcode\Types\Code49;
use Isahaq\Barcode\Renderers\PNGRenderer;
use Isahaq\Barcode\Renderers\SVGRenderer;
use Isahaq\Barcode\Renderers\HTMLRenderer;
use Isahaq\Barcode\Renderers\ImagickRenderer;
use Isahaq\Barcode\Utils\Watermark;

class BarcodeService
{
    protected function resolveType(string $type): object
    {
        return match (strtolower($type)) {
            'qrcode' => new QRCode(),
            'datamatrix' => new DataMatrix(),
            'aztec' => new Aztec(),
            'pdf417' => new PDF417(),
            'microqr' => new MicroQR(),
            'maxicode' => new Maxicode(),
            'code16k' => new Code16K(),
            'code49' => new Code49(),
            'code128' => new Code128(),
            'code128a' => new Code128A(),
            'code128b' => new Code128B(),
            'code128c' => new Code128C(),
            'code128auto' => new Code128Auto(),
            'code39' => new Code39(),
            'code39checksum' => new Code39Checksum(),
            'code39e' => new Code39E(),
            'code39echecksum' => new Code39EChecksum(),
            'code39auto' => new Code39Auto(),
            'code93' => new Code93(),
            'code25' => new Code25(),
            'code25auto' => new Code25Auto(),
            'code32' => new Code32(),
            'standard25' => new Standard25(),
            'standard25checksum' => new Standard25Checksum(),
            'interleaved25' => new Interleaved25(),
            'interleaved25checksum' => new Interleaved25Checksum(),
            'interleaved25auto' => new Interleaved25Auto(),
            'ean2' => new EAN2(),
            'ean5' => new EAN5(),
            'ean8' => new EAN8(),
            'ean13' => new EAN13(),
            'itf14' => new ITF14(),
            'upca' => new UPCA(),
            'upce' => new UPCE(),
            'msi' => new MSI(),
            'msichecksum' => new MSIChecksum(),
            'msiauto' => new MSIAuto(),
            'postnet' => new POSTNET(),
            'planet' => new PLANET(),
            'rms4cc' => new RMS4CC(),
            'kix' => new KIX(),
            'imb' => new IMB(),
            'codabar' => new Codabar(),
            'code11' => new Code11(),
            'pharmacode' => new PharmaCode(),
            'pharmacodetwotracks' => new PharmaCodeTwoTracks(),
            default => new Code128(),
        };
    }

    protected function resolveRenderer(string $format): object
    {
        return match (strtolower($format)) {
            'png' => new PNGRenderer(),
            'svg' => new SVGRenderer(),
            'html' => new HTMLRenderer(),
            // ... add more renderers here
            default => new PNGRenderer(),
        };
    }

    public function make(string $type, string $format, string $data, array $options = []): string
    {
        if (strtolower($type) === 'qrcode') {
            // Use the new SimpleQRCode generator
            $version = $options['version'] ?? 2;
            $margin = $options['margin'] ?? 4;
            $moduleSize = $options['module_size'] ?? 8;
            return \Isahaq\Barcode\Utils\SimpleQRCode::png($data, $version, $margin, $moduleSize);
        }
        $barcodeType = $this->resolveType($type);
        $barcode = $barcodeType->encode($data);
        $renderer = $this->resolveRenderer($format);
        return $renderer->render($barcode, $options);
    }

    // Convenience methods
    public function png(string $data, string $type = 'code128', array $options = []): string
    {
        return $this->make($type, 'png', $data, $options);
    }
    public function svg(string $data, string $type = 'code128', array $options = []): string
    {
        return $this->make($type, 'svg', $data, $options);
    }
    public function html(string $data, string $type = 'code128', array $options = []): string
    {
        return $this->make($type, 'html', $data, $options);
    }

    /**
     * Generate barcode with image watermark
     */
    public function withWatermark(
        string $data, 
        string $type = 'code128', 
        string $format = 'png',
        string $watermarkPath = '',
        array $watermarkOptions = []
    ): string {
        $barcodeData = $this->make($type, $format, $data);
        
        if (empty($watermarkPath)) {
            return $barcodeData;
        }

        $position = $watermarkOptions['position'] ?? Watermark::POSITION_BOTTOM_RIGHT;
        $opacity = $watermarkOptions['opacity'] ?? 0.7;
        $scale = $watermarkOptions['scale'] ?? 100;
        $margin = $watermarkOptions['margin'] ?? 10;

        return Watermark::apply($barcodeData, $watermarkPath, $position, $opacity, $scale, $margin);
    }

    /**
     * Generate barcode with text watermark
     */
    public function withTextWatermark(
        string $data, 
        string $type = 'code128', 
        string $format = 'png',
        string $text = '',
        array $textOptions = []
    ): string {
        $barcodeData = $this->make($type, $format, $data);
        
        if (empty($text)) {
            return $barcodeData;
        }

        $position = $textOptions['position'] ?? Watermark::POSITION_BOTTOM_RIGHT;
        $color = $textOptions['color'] ?? [255, 255, 255];
        $fontSize = $textOptions['font_size'] ?? 12;
        $margin = $textOptions['margin'] ?? 10;

        return Watermark::applyText($barcodeData, $text, $position, $color, $fontSize, $margin);
    }

    /**
     * Get available watermark positions
     */
    public function getWatermarkPositions(): array
    {
        return Watermark::getAvailablePositions();
    }
} 