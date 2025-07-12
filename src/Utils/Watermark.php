<?php

namespace Isahaq\Barcode\Utils;

class Watermark
{
    public const POSITION_TOP_LEFT = 'top-left';
    public const POSITION_TOP_RIGHT = 'top-right';
    public const POSITION_BOTTOM_LEFT = 'bottom-left';
    public const POSITION_BOTTOM_RIGHT = 'bottom-right';
    public const POSITION_CENTER = 'center';
    public const POSITION_TOP_CENTER = 'top-center';
    public const POSITION_BOTTOM_CENTER = 'bottom-center';
    public const POSITION_LEFT_CENTER = 'left-center';
    public const POSITION_RIGHT_CENTER = 'right-center';

    /**
     * Apply watermark to barcode image
     */
    public static function apply(
        string $imageData, 
        string $watermarkPath, 
        string $position = self::POSITION_BOTTOM_RIGHT,
        float $opacity = 0.7,
        int $scale = 100,
        int $margin = 10
    ): string {
        // Load barcode image
        $barcodeIm = @imagecreatefromstring($imageData);
        if (!$barcodeIm) {
            throw new \InvalidArgumentException('Invalid barcode image data');
        }

        // Load watermark image
        $watermarkIm = self::loadWatermarkImage($watermarkPath);
        if (!$watermarkIm) {
            imagedestroy($barcodeIm);
            throw new \InvalidArgumentException('Failed to load watermark image: ' . $watermarkPath);
        }

        // Get dimensions
        $bw = imagesx($barcodeIm);
        $bh = imagesy($barcodeIm);
        $ww = imagesx($watermarkIm);
        $wh = imagesy($watermarkIm);

        // Scale watermark if needed
        if ($scale !== 100) {
            $watermarkIm = self::scaleImage($watermarkIm, $scale);
            $ww = imagesx($watermarkIm);
            $wh = imagesy($watermarkIm);
        }

        // Calculate position
        $position = self::calculatePosition($bw, $bh, $ww, $wh, $position, $margin);

        // Apply watermark with transparency
        self::applyWatermarkWithOpacity($barcodeIm, $watermarkIm, $position['x'], $position['y'], $opacity);

        // Output result
        ob_start();
        imagepng($barcodeIm);
        $result = ob_get_clean();

        // Cleanup
        imagedestroy($barcodeIm);
        imagedestroy($watermarkIm);

        return $result;
    }

    /**
     * Load watermark image from file or URL
     */
    private static function loadWatermarkImage(string $watermarkPath): ?resource
    {
        if (filter_var($watermarkPath, FILTER_VALIDATE_URL)) {
            // Load from URL
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ]);
            $imageData = @file_get_contents($watermarkPath, false, $context);
            if ($imageData === false) {
                return null;
            }
            return @imagecreatefromstring($imageData);
        } else {
            // Load from local file
            if (!file_exists($watermarkPath)) {
                return null;
            }
            
            $extension = strtolower(pathinfo($watermarkPath, PATHINFO_EXTENSION));
            return match($extension) {
                'png' => @imagecreatefrompng($watermarkPath),
                'jpg', 'jpeg' => @imagecreatefromjpeg($watermarkPath),
                'gif' => @imagecreatefromgif($watermarkPath),
                'webp' => @imagecreatefromwebp($watermarkPath),
                default => @imagecreatefromstring(file_get_contents($watermarkPath))
            };
        }
    }

    /**
     * Scale image by percentage
     */
    private static function scaleImage($image, int $scale): resource
    {
        $width = imagesx($image);
        $height = imagesy($image);
        
        $newWidth = (int)($width * $scale / 100);
        $newHeight = (int)($height * $scale / 100);
        
        $scaledImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency
        imagealphablending($scaledImage, false);
        imagesavealpha($scaledImage, true);
        $transparent = imagecolorallocatealpha($scaledImage, 255, 255, 255, 127);
        imagefill($scaledImage, 0, 0, $transparent);
        
        imagecopyresampled($scaledImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        imagedestroy($image);
        return $scaledImage;
    }

    /**
     * Calculate watermark position
     */
    private static function calculatePosition(int $bw, int $bh, int $ww, int $wh, string $position, int $margin): array
    {
        return match($position) {
            self::POSITION_TOP_LEFT => ['x' => $margin, 'y' => $margin],
            self::POSITION_TOP_RIGHT => ['x' => $bw - $ww - $margin, 'y' => $margin],
            self::POSITION_BOTTOM_LEFT => ['x' => $margin, 'y' => $bh - $wh - $margin],
            self::POSITION_BOTTOM_RIGHT => ['x' => $bw - $ww - $margin, 'y' => $bh - $wh - $margin],
            self::POSITION_CENTER => ['x' => ($bw - $ww) / 2, 'y' => ($bh - $wh) / 2],
            self::POSITION_TOP_CENTER => ['x' => ($bw - $ww) / 2, 'y' => $margin],
            self::POSITION_BOTTOM_CENTER => ['x' => ($bw - $ww) / 2, 'y' => $bh - $wh - $margin],
            self::POSITION_LEFT_CENTER => ['x' => $margin, 'y' => ($bh - $wh) / 2],
            self::POSITION_RIGHT_CENTER => ['x' => $bw - $ww - $margin, 'y' => ($bh - $wh) / 2],
            default => ['x' => $bw - $ww - $margin, 'y' => $bh - $wh - $margin] // Default to bottom-right
        };
    }

    /**
     * Apply watermark with opacity
     */
    private static function applyWatermarkWithOpacity($barcodeIm, $watermarkIm, int $x, int $y, float $opacity): void
    {
        $ww = imagesx($watermarkIm);
        $wh = imagesy($watermarkIm);
        
        // Create temporary image for blending
        $tempImage = imagecreatetruecolor($ww, $wh);
        imagealphablending($tempImage, false);
        imagesavealpha($tempImage, true);
        
        // Copy watermark to temp image
        imagecopy($tempImage, $watermarkIm, 0, 0, 0, 0, $ww, $wh);
        
        // Apply opacity
        for ($py = 0; $py < $wh; $py++) {
            for ($px = 0; $px < $ww; $px++) {
                $color = imagecolorat($tempImage, $px, $py);
                $alpha = ($color >> 24) & 0xFF;
                $red = ($color >> 16) & 0xFF;
                $green = ($color >> 8) & 0xFF;
                $blue = $color & 0xFF;
                
                // Adjust alpha based on opacity
                $newAlpha = (int)(255 - (255 - $alpha) * $opacity);
                $newColor = imagecolorallocatealpha($tempImage, $red, $green, $blue, $newAlpha);
                imagesetpixel($tempImage, $px, $py, $newColor);
            }
        }
        
        // Copy to barcode image
        imagecopy($barcodeIm, $tempImage, $x, $y, 0, 0, $ww, $wh);
        
        imagedestroy($tempImage);
    }

    /**
     * Apply text watermark
     */
    public static function applyText(
        string $imageData,
        string $text,
        string $position = self::POSITION_BOTTOM_RIGHT,
        array $color = [255, 255, 255],
        int $fontSize = 12,
        int $margin = 10
    ): string {
        $barcodeIm = @imagecreatefromstring($imageData);
        if (!$barcodeIm) {
            throw new \InvalidArgumentException('Invalid barcode image data');
        }

        $bw = imagesx($barcodeIm);
        $bh = imagesy($barcodeIm);
        
        // Calculate text dimensions
        $textBox = imagettfbbox($fontSize, 0, __DIR__ . '/arial.ttf', $text);
        $textWidth = $textBox[4] - $textBox[0];
        $textHeight = $textBox[1] - $textBox[5];
        
        // Calculate position
        $position = self::calculatePosition($bw, $bh, $textWidth, $textHeight, $position, $margin);
        
        // Create text color
        $textColor = imagecolorallocate($barcodeIm, $color[0], $color[1], $color[2]);
        
        // Add text
        imagettftext($barcodeIm, $fontSize, 0, $position['x'], $position['y'] + $textHeight, $textColor, __DIR__ . '/arial.ttf', $text);
        
        ob_start();
        imagepng($barcodeIm);
        $result = ob_get_clean();
        
        imagedestroy($barcodeIm);
        
        return $result;
    }

    /**
     * Get available positions
     */
    public static function getAvailablePositions(): array
    {
        return [
            self::POSITION_TOP_LEFT,
            self::POSITION_TOP_RIGHT,
            self::POSITION_BOTTOM_LEFT,
            self::POSITION_BOTTOM_RIGHT,
            self::POSITION_CENTER,
            self::POSITION_TOP_CENTER,
            self::POSITION_BOTTOM_CENTER,
            self::POSITION_LEFT_CENTER,
            self::POSITION_RIGHT_CENTER
        ];
    }
} 