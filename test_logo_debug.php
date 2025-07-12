<?php

require_once 'vendor/autoload.php';

use Isahaq\Barcode\Utils\ModernQRCode;

echo "=== Logo Debug Test ===\n\n";

// Test 1: Debug logo loading with different URLs
$qr = new ModernQRCode();

// Test with different logo URLs
$logoUrls = [
    'https://via.placeholder.com/100x100/FF0000/FFFFFF?text=LOGO',
    'https://httpbin.org/image/png',
    'https://picsum.photos/100/100'
];

foreach ($logoUrls as $logoUrl) {
    echo "Testing logo URL: $logoUrl\n";
    $debugResult = $qr->debugLogo($logoUrl);
    print_r($debugResult);
    echo "\n";
}

// Test 2: Create a simple test image locally
echo "=== Creating local test image ===\n";
$testImage = imagecreatetruecolor(100, 100);
$red = imagecolorallocate($testImage, 255, 0, 0);
imagefill($testImage, 0, 0, $red);
imagepng($testImage, 'test_logo.png');
imagedestroy($testImage);
echo "Created test_logo.png\n";

// Test 3: Debug local logo
echo "=== Testing local logo ===\n";
$debugResult = $qr->debugLogo('test_logo.png');
print_r($debugResult);
echo "\n";

// Test 4: Generate QR with local logo
echo "=== Generating QR with local logo ===\n";
$qr = new ModernQRCode();
$qr->setData('https://example.com')
   ->setSize(300)
   ->setLogo('test_logo.png', 60);

try {
    $qrData = $qr->writeString();
    echo "QR generated successfully. Size: " . strlen($qrData) . " bytes\n";
    
    // Save to file for inspection
    file_put_contents('test_qr_with_local_logo.png', $qrData);
    echo "QR saved to test_qr_with_local_logo.png\n";
} catch (Exception $e) {
    echo "Error generating QR: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n"; 