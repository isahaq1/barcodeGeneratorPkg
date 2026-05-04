![License](https://img.shields.io/github/license/isahaq1/barcodeGeneratorPkg)
![Stars](https://img.shields.io/github/stars/isahaq1/barcodeGeneratorPkg)
![Issues](https://img.shields.io/github/issues/isahaq1/barcodeGeneratorPkg)
![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0-blue)
![Packagist](https://img.shields.io/packagist/v/isahaq/barcode)

# 📊 Isahaq Barcode Generator

A universal barcode generator package supporting **32+ barcode types** (linear, 2D, postal, stacked, and auto-detection variants), multiple output formats, CLI, and full Laravel integration (Service Provider & Facade).

> Generate barcodes of any type, in any format, anywhere. Perfect for e-commerce, inventory management, ticketing, and more.

---

## 🚀 Features

- **32+ Barcode Types**: Linear, 2D, postal, stacked, and auto-detection variants
- **Multiple Output Formats**: PNG, SVG, HTML, JPG, PDF
- **Laravel Integration**: Service Provider and Facade support  
- **CLI Tool**: Command-line interface for quick barcode generation
- **QR Code Builder**: Fluent API for advanced QR code generation
- **Batch Generation**: Generate multiple barcodes efficiently
- **Validation**: Built-in data validation for different barcode types
- **Customization**: Size, colors, margins, and more options
- **No External Dependencies**: Pure PHP implementation
- **Well-Tested**: Comprehensive test suite included

## 📦 Installation

### Via Composer

```bash
composer require isahaq/barcode
```

### Requirements

- PHP 8.0 or higher
- ext-mbstring extension
- For Laravel: Illuminate/Support 8.0+ (automatically included if using Laravel)

## 🔧 Why Use This Package?

1. **Comprehensive Support**: 32+ barcode types including industry standards and auto-detection
2. **Multiple Formats**: Generate barcodes in PNG, SVG, HTML, JPG, and PDF
3. **Laravel Ready**: Seamless integration with Laravel framework
4. **CLI Support**: Generate barcodes from command line
5. **Advanced QR Codes**: Customizable QR codes with logos and labels
6. **Validation**: Built-in data validation for each barcode type
7. **Performance**: Optimized for high-volume generation
8. **Extensible**: Easy to add new barcode types and renderers

## ⚡ Laravel Setup for Older Versions

If you are using an older version of Laravel (before package auto-discovery), add the service provider and facade alias manually in your `config/app.php`:

```php
'providers' => [
    // ...
    Isahaq\Barcode\Providers\BarcodeServiceProvider::class,
],

'aliases' => [
    // ...
    'Barcode' => Isahaq\Barcode\Facades\Barcode::class,
],
```

## 📋 Supported Barcode Types (32+ Types)

### Linear Barcodes

- **Code128** (A, B, C, Auto)
- **Code39** (Standard, Checksum, Extended, Auto)
- **Code93**
- **Code25** (Standard, Auto)
- **Code32** (Italian Pharmacode)
- **Standard25** (Standard, Checksum)
- **Interleaved25** (Standard, Checksum, Auto)
- **MSI** (Standard, Checksum, Auto)

### EAN/UPC Family

- **EAN13**, **EAN8**, **EAN2**, **EAN5**
- **UPC-A**, **UPC-E**
- **ITF14**

### Postal Barcodes

- **POSTNET**, **PLANET**, **RMS4CC**, **KIX**, **IMB**

### Specialized Barcodes

- **Codabar**, **Code11**, **PharmaCode**, **PharmaCodeTwoTracks**

### 2D Matrix Codes

- **QRCode**, **DataMatrix**, **Aztec**, **PDF417**, **MicroQR**, **Maxicode**

### Stacked Linear Codes

- **Code16K**, **Code49**

---

## 🛠️ Quick Usage Examples

### Basic Barcode Generation

```php
use Isahaq\Barcode\Types\Code128;
use Isahaq\Barcode\Renderers\PNGRenderer;

$barcodeType = new Code128();
$renderer = new PNGRenderer();
$barcode = $barcodeType->encode('1234567890');
$pngData = $renderer->render($barcode);
file_put_contents('barcode.png', $pngData);
```

### Laravel Usage (with Facade)

```php
// Generate QR Code
$qrCode = Barcode::qrCode()
    ->data('https://example.com')
    ->size(300)
    ->format('png')
    ->generate();

// Generate EAN13 Barcode
$ean13 = Barcode::code('EAN13', '1234567890128')
    ->png()
    ->save('barcode.png');

// Get as Base64 (for web display)
$base64 = Barcode::code('Code128', '123456')
    ->png()
    ->asBase64();
```

### Display Barcode as HTML Image

```php
$barcodeImage = $renderer->render($barcode);
$base64 = base64_encode($barcodeImage);
echo '<img src="data:image/png;base64,' . $base64 . '" alt="Barcode" />';
```

### Batch Generation

```php
use Isahaq\Barcode\Utils\BatchGenerator;

$batch = new BatchGenerator();
$codes = ['ABC123', 'DEF456', 'GHI789'];

foreach ($codes as $code) {
    $barcode = $batch->generate('Code128', $code, 'png');
    file_put_contents("barcode_{$code}.png", $barcode);
}
```

### Validation

```php
use Isahaq\Barcode\Utils\Validator;

$validator = new Validator();
if ($validator->validate('EAN13', '1234567890128')) {
    echo 'Valid EAN13';
} else {
    echo 'Invalid EAN13';
}
```

### CLI Usage

```bash
php src/CLI/generate.php --type=Code128 --data=123456 --format=png --output=barcode.png
```

---

## 📚 Documentation

Full documentation available at [docs/](docs/) directory:

- [Installation Guide](docs/INSTALLATION.md)
- [API Reference](docs/API.md)
- [Barcode Types](docs/BARCODE_TYPES.md)
- [Output Formats](docs/RENDERERS.md)
- [Laravel Integration](docs/LARAVEL.md)
- [Examples](docs/EXAMPLES.md)

---

## 🧪 Testing

Run the test suite:

```bash
composer test
# or
vendor/bin/phpunit
```

Test coverage includes:

- Barcode type encoding validation
- Renderer output formats
- Laravel integration tests
- Batch generation
- Data validation

---

## 🤝 Contributing

We welcome contributions! Here's how you can help:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Make** your changes
4. **Write** or update tests
5. **Commit** your changes (`git commit -m 'Add amazing feature'`)
6. **Push** to the branch (`git push origin feature/amazing-feature`)
7. **Open** a Pull Request

### Contribution Guidelines

- Follow PSR-12 coding standards
- Write unit tests for new features
- Update documentation as needed
- Ensure all tests pass before submitting

---

## 📝 License

This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

## 🐛 Bug Reports & Feature Requests

Found a bug or have an idea? [Open an issue](https://github.com/isahaq1/barcodeGeneratorPkg/issues) on GitHub.

---

## 💬 Support

- **Issues**: [GitHub Issues](https://github.com/isahaq1/barcodeGeneratorPkg/issues)
- **Discussions**: [GitHub Discussions](https://github.com/isahaq1/barcodeGeneratorPkg/discussions)
- **Email**: hmisahaq01@gmail.com

---

## 📊 Barcode Types Reference

| Type | Example | Best For |
|------|---------|----------|
| Code128 | `ABC123` | General purpose, high data density |
| Code39 | `ABC-123` | Alphanumeric, inventory |
| EAN13 | `5901234123457` | Retail products (13 digits) |
| EAN8 | `96385074` | Small products (8 digits) |
| QR Code | Any data | URLs, contact info, product data |
| DataMatrix | Any data | Small spaces, pharmaceutical |
| PDF417 | Large data | IDs, documents, certificates |

---

## 🙏 Acknowledgments

- Built with PHP 8.0+
- Inspired by industry-standard barcode specifications
- Thanks to all contributors and users

---

## 📈 Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and updates.

---

Made with ❤️ by [Isahaq](https://github.com/isahaq1)

## 🛠️ Usage

### Basic PHP Usage

```php
require_once 'vendor/autoload.php';
use Isahaq\Barcode\Types\Code128;
use Isahaq\Barcode\Renderers\PNGRenderer;
$barcodeType = new Code128();
$renderer = new PNGRenderer();
$barcode = $barcodeType->encode('1234567890');
$result = $renderer->render($barcode);
file_put_contents('barcode.png', $result);
```

### Display Barcode as Base64 Image (PNG)

```php
// ... after generating $barcode (PNG data)
$barcodeImage = base64_encode($barcode);
echo '<img src="data:image/png;base64,' . $barcodeImage . '" alt="Barcode" />';
//with custom height width
echo '<img src="data:image/png;base64,' . $barcodeImage . '" alt="Barcode" />';
```

### QR Code with Logo and Watermark

```php
use Isahaq\Barcode\QrCodeBuilder;

$qrCode = QrCodeBuilder::create()
    ->data('https://example.com')
    ->size(300)
    ->margin(10)
    ->foregroundColor([0, 0, 0])
    ->backgroundColor([255, 255, 255])
    ->logoPath('path/to/logo.png') // Logo in the center
    ->label('Scan me!')            // Watermark or label below
    ->labelFont('path/to/font.ttf', 16)
    ->format('png')
    ->build();

// Save to file
$qrCode->saveToFile('qr-code-with-logo.png');

// Display as base64 image
echo '<img src="data:image/png;base64,' . base64_encode($qrCode->getString()) . '" alt="QR Code with Logo and Watermark" />';
```

### Using the Service Class

```php
use Isahaq\Barcode\Services\BarcodeService;
$barcodeService = new BarcodeService();
$pngData = $barcodeService->png('1234567890', 'code128');
$svgData = $barcodeService->svg('1234567890', 'ean13');
$htmlData = $barcodeService->html('1234567890', 'code39');
$options = [ 'width' => 300, 'height' => 100 ];
$customBarcode = $barcodeService->make('code128', 'png', '1234567890', $options);
```

### QR Code Builder (Advanced)

```php
use Isahaq\Barcode\QrCodeBuilder;
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
$qrCode->saveToFile('qr-code.png');
$dataUri = $qrCode->getDataUri();
echo "<img src='$dataUri' alt='QR Code'>";
```

## 🎯 Laravel Integration

### Using Laravel Facade

```php
use Isahaq\Barcode\Facades\Barcode;
$barcode = Barcode::png('1234567890', 'code128');
$qrCode = Barcode::modernQr([
    'data' => 'https://example.com',
    'size' => 300,
    'margin' => 10,
    'error_correction' => 'H',
    'foreground_color' => [0, 0, 0],
    'background_color' => [255, 255, 255],
    'label' => 'Scan me!'
]);
return response($barcode)->header('Content-Type', 'image/png');
```

### QR Code with Logo (Laravel Facade)

```php
use Isahaq\Barcode\Facades\Barcode;
$qrWithLogo = Barcode::modernQr([
    'data' => 'https://example.com',
    'size' => 300,
    'margin' => 10,
    'logoPath' => 'path/to/logo.png',
    'logoSize' => 60, // Logo size as percentage (default: 60)
    'label' => 'Scan me!',
    'error_correction' => 'H' // Use H for better logo support
]);
return response($qrWithLogo)->header('Content-Type', 'image/png');
```

### Available Watermark Positions

```php
$positions = Barcode::getWatermarkPositions();
// Returns: ['top-left', 'top-right', 'bottom-left', 'bottom-right', 'center',
//           'top-center', 'bottom-center', 'left-center', 'right-center']
```

### Using Service Provider

```php
public function generateBarcode(Request $request)
{
    $barcodeService = app('barcode');
    $data = $request->input('data', '1234567890');
    $type = $request->input('type', 'code128');
    $format = $request->input('format', 'png');
    $options = [ 'width' => $request->input('width', 300), 'height' => $request->input('height', 100) ];
    $barcode = $barcodeService->make($type, $format, $data, $options);
    return response($barcode)->header('Content-Type', $this->getMimeType($format));
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

### Blade Templates & Routes

```php
<img src="data:image/png;base64,{{ base64_encode(Barcode::png('1234567890')) }}" alt="Barcode">
//with custom height width
<img src="data:image/png;base64,{{ base64_encode(Barcode::png('1234567890','C128',['width' => 1,
    'height' => 40,
    'foreground_color' => [255, 0, 0], // Red
    'background_color' => [255, 255, 255], // White
    'padding' => 20
])) }}" alt="Barcode">

<img src="data:image/png;base64,{{ base64_encode(Barcode::modernQr(['data' => 'https://example.com'])) }}" alt="QR Code">

<!-- QR Code with Logo -->
<img src="data:image/png;base64,{{ base64_encode(Barcode::modernQr([
    'data' => 'https://example.com',
    'logoPath' => 'http://127.0.0.1:8000/assets/images/logo-dark.png',
    'logoSize' => 60,
    'error_correction' => 'H'
])) }}" alt="QR Code with Logo">
// routes/web.php
Route::get('/barcode/{data}', function ($data) {
    $barcode = Barcode::png($data, 'code128');
    return response($barcode)->header('Content-Type', 'image/png');
});
```

## 🖥️ CLI Usage

```bash
php vendor/bin/generate.php --data="1234567890" --type="code128" --format="png" --output="barcode.png"
php vendor/bin/generate.php --data="https://example.com" --type="qrcode" --format="png" --output="qr.png"
php vendor/bin/generate.php --data="1234567890" --type="ean13" --format="svg" --output="barcode.svg"
```

## 📊 Advanced Features

- **Batch Generation**
- **Custom Renderer Options**
- **Validation**
- **Auto-detection**: Use `code128auto`, `code39auto`, `code25auto`, `interleaved25auto`, `
