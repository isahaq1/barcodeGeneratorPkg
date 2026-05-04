<?php

use PHPUnit\Framework\TestCase;
use Isahaq\Barcode\Types\Code128;

class DiagnosticTest extends TestCase
{
    public function testBasicFunctionality()
    {
        // Test basic PHP functionality
        $type = new Code128();
        $this->assertNotNull($type);
        $this->assertTrue(method_exists($type, 'encode'));
        $this->assertTrue(method_exists($type, 'validate'));
    }

    public function testPhpVersion()
    {
        // Just verify the test can run
        $version = phpversion();
        $this->assertNotEmpty($version);
        echo "Running on PHP " . $version . PHP_EOL;
    }

    public function testGDExtension()
    {
        $this->assertTrue(extension_loaded('gd'), 'GD extension should be loaded');
    }

    public function testImageCreation()
    {
        $this->assertTrue(function_exists('imagecreatetruecolor'), 'imagecreatetruecolor should exist');
        
        $im = @imagecreatetruecolor(10, 10);
        $this->assertNotFalse($im, 'imagecreatetruecolor should work');
        
        if ($im) {
            imagedestroy($im);
        }
    }
}
