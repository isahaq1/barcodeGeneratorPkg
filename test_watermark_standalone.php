<?php

require_once 'vendor/autoload.php';

use Isahaq\Barcode\Services\BarcodeService;
use Isahaq\Barcode\Utils\Watermark;

echo "=== Watermark Feature Test (Standalone) ===\n\n";

// Test 1: Create a simple test watermark image
echo "=== Creating test watermark image ===\n";
$watermarkImage = imagecreatetruecolor(100, 50);
$bgColor = imagecolorallocate($watermarkImage, 255, 255, 255);
$textColor = imagecolorallocate($watermarkImage, 0, 0, 0);
imagefill($watermarkImage, 0, 0, $bgColor);
imagestring($watermarkImage, 3, 10, 15, 'WATERMARK', $textColor);
imagepng($watermarkImage, 'test_watermark.png');
imagedestroy($watermarkImage);
echo "Created test_watermark.png\n\n";

// Test 2: Generate barcode with image watermark
echo "=== Testing image watermark ===\n";
try {
    $barcodeService = new BarcodeService();
    $barcodeWithWatermark = $barcodeService->withWatermark(
        '1234567890',
        'code128',
        'png',
        'test_watermark.png',
        [
            'position' => Watermark::POSITION_BOTTOM_RIGHT,
            'opacity' => 0.8,
            'scale' => 80,
            'margin' => 10
        ]
    );
    
    file_put_contents('barcode_with_watermark.png', $barcodeWithWatermark);
    echo "✓ Barcode with image watermark saved to barcode_with_watermark.png\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test 3: Generate barcode with text watermark
echo "\n=== Testing text watermark ===\n";
try {
    $barcodeService = new BarcodeService();
    $barcodeWithTextWatermark = $barcodeService->withTextWatermark(
        '1234567890',
        'code128',
        'png',
        'CONFIDENTIAL',
        [
            'position' => Watermark::POSITION_CENTER,
            'color' => [255, 0, 0], // Red text
            'font_size' => 16,
            'margin' => 20
        ]
    );
    
    file_put_contents('barcode_with_text_watermark.png', $barcodeWithTextWatermark);
    echo "✓ Barcode with text watermark saved to barcode_with_text_watermark.png\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test 4: Test different positions
echo "\n=== Testing different watermark positions ===\n";
$positions = [
    Watermark::POSITION_TOP_LEFT => 'top-left',
    Watermark::POSITION_TOP_RIGHT => 'top-right',
    Watermark::POSITION_BOTTOM_LEFT => 'bottom-left',
    Watermark::POSITION_CENTER => 'center'
];

foreach ($positions as $position => $name) {
    try {
        $barcodeService = new BarcodeService();
        $barcode = $barcodeService->withWatermark(
            '1234567890',
            'code128',
            'png',
            'test_watermark.png',
            [
                'position' => $position,
                'opacity' => 0.6,
                'scale' => 60
            ]
        );
        
        file_put_contents("barcode_watermark_{$name}.png", $barcode);
        echo "✓ Created barcode_watermark_{$name}.png\n";
    } catch (Exception $e) {
        echo "✗ Error with {$name}: " . $e->getMessage() . "\n";
    }
}

// Test 5: Test URL watermark
echo "\n=== Testing URL watermark ===\n";
try {
    $barcodeService = new BarcodeService();
    $barcodeWithUrlWatermark = $barcodeService->withWatermark(
        '1234567890',
        'code128',
        'png',
        'https://via.placeholder.com/80x40/FF0000/FFFFFF?text=LOGO',
        [
            'position' => Watermark::POSITION_TOP_RIGHT,
            'opacity' => 0.7,
            'scale' => 70
        ]
    );
    
    file_put_contents('barcode_with_url_watermark.png', $barcodeWithUrlWatermark);
    echo "✓ Barcode with URL watermark saved to barcode_with_url_watermark.png\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test 6: Show available positions
echo "\n=== Available watermark positions ===\n";
$barcodeService = new BarcodeService();
$availablePositions = $barcodeService->getWatermarkPositions();
foreach ($availablePositions as $position) {
    echo "- {$position}\n";
}

// Test 7: Test direct watermark application
echo "\n=== Testing direct watermark application ===\n";
try {
    $barcodeService = new BarcodeService();
    $barcodeData = $barcodeService->png('1234567890', 'code128');
    
    $watermarkedBarcode = Watermark::apply(
        $barcodeData,
        'test_watermark.png',
        Watermark::POSITION_BOTTOM_LEFT,
        0.5,
        50,
        5
    );
    
    file_put_contents('direct_watermark_test.png', $watermarkedBarcode);
    echo "✓ Direct watermark test saved to direct_watermark_test.png\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "Generated files:\n";
echo "- barcode_with_watermark.png\n";
echo "- barcode_with_text_watermark.png\n";
echo "- barcode_watermark_top-left.png\n";
echo "- barcode_watermark_top-right.png\n";
echo "- barcode_watermark_bottom-left.png\n";
echo "- barcode_watermark_center.png\n";
echo "- barcode_with_url_watermark.png\n";
echo "- direct_watermark_test.png\n"; 