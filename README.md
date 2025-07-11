# Isahaq Barcode Generator

A universal barcode generator package supporting multiple barcode types and output formats, with extra features like batch generation, watermarking, validation, CLI, and Laravel Facade support.

## 🚀 Features

- **Multiple Barcode Types**: Code128, Code39, EAN13, EAN8, UPC-A, UPC-E, QR Code, and many more
- **Multiple Output Formats**: PNG, SVG, HTML, JPG, PDF
- **Laravel Integration**: Service Provider and Facade support
- **CLI Tool**: Command-line interface for quick barcode generation
- **QR Code Builder**: Fluent API for advanced QR code generation
- **Validation**: Built-in data validation for different barcode types
- **Customization**: Size, colors, margins, and more options

## 📦 Installation

### Via Composer

```bash
composer require isahaq/barcode
```

### Requirements

- PHP 8.0 or higher
- ext-mbstring extension
- For Laravel: Illuminate/Support 8.0+ (automatically included)

## 🔧 Why Use This Package?

1. **Comprehensive Support**: 25+ barcode types including industry standards
2. **Multiple Formats**: Generate barcodes in PNG, SVG, HTML, JPG, and PDF
3. **Laravel Ready**: Seamless integration with Laravel framework
4. **CLI Support**: Generate barcodes from command line
5. **Advanced QR Codes**: Customizable QR codes with logos and labels
6. **Validation**: Built-in data validation for each barcode type
7. **Performance**: Optimized for high-volume generation
8. **Extensible**: Easy to add new barcode types and renderers

## 📋 Supported Barcode Types

- **Code128** - General purpose, high-density barcode
- **Code39** - Industrial barcode standard
- **EAN13** - European Article Number (13 digits)
- **EAN8** - European Article Number (8 digits)
- **UPC-A** - Universal Product Code (12 digits)
- **UPC-E** - Compressed UPC (8 digits)
- **QR Code** - 2D barcode for URLs, text, contact info
- **ITF14** - Interleaved 2 of 5 (14 digits)
- **MSI** - Modified Plessey
- **POSTNET** - US Postal Service
- **PLANET** - US Postal Service
- **Codabar** - Library and blood bank standard
- **Code11** - Telecommunications
- **PharmaCode** - Pharmaceutical industry
- And many more...

## 🛠️ Usage

### Basic PHP Usage

```php
<?php

require_once 'vendor/autoload.php';

use Isahaq\Barcode\Types\Code128;
use Isahaq\Barcode\Renderers\PNGRenderer;

// Generate a simple barcode
$barcodeType = new Code128();
$renderer = new PNGRenderer();

$barcode = $barcodeType->encode('1234567890');
$result = $renderer->render($barcode);

// Save to file
file_put_contents('barcode.png', $result);
```

### Using the Service Class

```php
<?php

use Isahaq\Barcode\Services\BarcodeService;

$barcodeService = new BarcodeService();

// Generate PNG barcode
$pngData = $barcodeService->png('1234567890', 'code128');

// Generate SVG barcode
$svgData = $barcodeService->svg('1234567890', 'ean13');

// Generate HTML barcode
$htmlData = $barcodeService->html('1234567890', 'code39');

// With custom options
$options = [
    'width' => 300,
    'height' => 100,
    'foreground_color' => [0, 0, 0],
    'background_color' => [255, 255, 255]
];

$customBarcode = $barcodeService->make('code128', 'png', '1234567890', $options);
```

### QR Code Builder (Advanced)

```php
<?php

use Isahaq\Barcode\QrCodeBuilder;

// Create a QR code with logo and label
$qrCode = QrCodeBuilder::create()
    ->data('https://example.com')
    ->size(300)
    ->margin(10)
    ->foregroundColor([0, 0, 0])
    ->backgroundColor([255, 255, 255])
    ->logoPath('path/to/logo.png')
    ->label('Scan me!')
    ->labelFont('path/to/font.ttf', 16)
    ->format('png')
    ->build();

// Save to file
$qrCode->saveToFile('qr-code.png');

// Get as data URI for HTML
$dataUri = $qrCode->getDataUri();
echo "<img src='$dataUri' alt='QR Code'>";
```

## 🎯 Laravel Integration

### Installation

The package automatically registers with Laravel. No additional configuration needed!

### Using Laravel Facade

```php
<?php

use Isahaq\Barcode\Facades\Barcode;

// Generate basic barcode
$barcode = Barcode::png('1234567890', 'code128');

// Generate QR code with modern styling
$qrCode = Barcode::modernQr([
    'data' => 'https://example.com',
    'size' => 300,
    'margin' => 10,
    'error_correction' => 'H',
    'foreground_color' => [0, 0, 0],
    'background_color' => [255, 255, 255],
    'label' => 'Scan me!'
]);

// Return as response
return response($barcode)
    ->header('Content-Type', 'image/png');
```

### Using Service Provider

```php
<?php

// In your controller or service
public function generateBarcode(Request $request)
{
    $barcodeService = app('barcode');

    $data = $request->input('data', '1234567890');
    $type = $request->input('type', 'code128');
    $format = $request->input('format', 'png');

    $options = [
        'width' => $request->input('width', 300),
        'height' => $request->input('height', 100),
    ];

    $barcode = $barcodeService->make($type, $format, $data, $options);

    return response($barcode)
        ->header('Content-Type', $this->getMimeType($format));
}

private function getMimeType($format)
{
    return match($format) {
        'png' => 'image/png',
        'svg' => 'image/svg+xml',
        'html' => 'text/html',
        default => 'image/png'
    };
}
```

### Laravel Blade Templates

```php
<!-- In your blade template -->
<img src="data:image/png;base64,{{ base64_encode(Barcode::png('1234567890')) }}" alt="Barcode">

<!-- Or using data URI -->
<img src="{{ Barcode::modernQr(['data' => 'https://example.com'])->getDataUri() }}" alt="QR Code">
```

### Laravel Routes

```php
// routes/web.php
Route::get('/barcode/{data}', function ($data) {
    $barcode = Barcode::png($data, 'code128');
    return response($barcode)->header('Content-Type', 'image/png');
});

Route::get('/qr/{data}', function ($data) {
    $qrCode = Barcode::modernQr(['data' => $data]);
    return response($qrCode)->header('Content-Type', 'image/png');
});
```

## 🖥️ CLI Usage

The package includes a command-line tool for quick barcode generation:

```bash
# Basic usage
php vendor/bin/generate.php --data="1234567890" --type="code128" --format="png" --output="barcode.png"

# Generate QR code
php vendor/bin/generate.php --data="https://example.com" --type="qrcode" --format="png" --output="qr.png"

# Generate SVG barcode
php vendor/bin/generate.php --data="1234567890" --type="ean13" --format="svg" --output="barcode.svg"

# Available options
--type: barcode type (code128, code39, ean13, qrcode, etc.)
--format: output format (png, svg, html, jpg, pdf)
--data: barcode data
--output: output file path (optional, prints to stdout if not specified)
```

## 📊 Advanced Features

### Batch Generation

```php
<?php

use Isahaq\Barcode\Services\BarcodeService;

$barcodeService = new BarcodeService();
$dataArray = ['1234567890', '0987654321', '5555555555'];

foreach ($dataArray as $index => $data) {
    $barcode = $barcodeService->png($data, 'code128');
    file_put_contents("barcode_{$index}.png", $barcode);
}
```

### Custom Renderer Options

```php
<?php

use Isahaq\Barcode\Renderers\PNGRenderer;

$renderer = new PNGRenderer();
$options = [
    'width' => 400,
    'height' => 150,
    'foreground_color' => [255, 0, 0], // Red
    'background_color' => [255, 255, 255], // White
    'margin' => 20,
    'scale' => 2
];

$barcode = $barcodeType->encode('1234567890');
$result = $renderer->render($barcode, $options);
```

### Validation

```php
<?php

use Isahaq\Barcode\Types\EAN13;

$ean13 = new EAN13();

// Check if data is valid for EAN13
if ($ean13->validate('1234567890123')) {
    $barcode = $ean13->encode('1234567890123');
    // Process barcode...
} else {
    echo "Invalid EAN13 data";
}
```

## 🔧 Configuration

### Laravel Configuration (Optional)

Create a config file `config/barcode.php`:

```php
<?php

return [
    'default_type' => 'code128',
    'default_format' => 'png',
    'default_options' => [
        'width' => 300,
        'height' => 100,
        'foreground_color' => [0, 0, 0],
        'background_color' => [255, 255, 255],
        'margin' => 10,
    ],
    'qr_code' => [
        'default_size' => 300,
        'default_margin' => 10,
        'error_correction' => 'L', // L, M, Q, H
    ],
];
```

## 🧪 Testing

```bash
# Run tests
vendor/bin/phpunit

# Or with composer
composer test
```

## 📝 Examples

### E-commerce Product Barcode

```php
<?php

use Isahaq\Barcode\Facades\Barcode;

// Generate UPC-A for product
$productCode = '123456789012';
$barcode = Barcode::png($productCode, 'upca');

// Save to product image directory
file_put_contents("products/{$productCode}.png", $barcode);
```

### QR Code for Contact Information

```php
<?php

use Isahaq\Barcode\QrCodeBuilder;

$contactInfo = "BEGIN:VCARD\nVERSION:3.0\nFN:John Doe\nTEL:+1234567890\nEMAIL:john@example.com\nEND:VCARD";

$qrCode = QrCodeBuilder::create()
    ->data($contactInfo)
    ->size(400)
    ->margin(20)
    ->label('John Doe')
    ->format('png')
    ->build();

$qrCode->saveToFile('contact-card.png');
```

### Shipping Label Barcode

```php
<?php

use Isahaq\Barcode\Services\BarcodeService;

$barcodeService = new BarcodeService();
$trackingNumber = '1Z999AA1234567890';

// Generate POSTNET barcode for shipping
$barcode = $barcodeService->make('postnet', 'png', $trackingNumber, [
    'width' => 600,
    'height' => 80
]);

file_put_contents('shipping-label.png', $barcode);
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## 👨‍💻 Author

**Isahaq** - [hmisahaq01@gmail.com](mailto:hmisahaq01@gmail.com)

## 🙏 Acknowledgments

- Inspired by various barcode generation libraries
- Built with modern PHP 8.0+ features
- Laravel integration for seamless framework usage

---

**Happy Barcode Generating! 🎉**
