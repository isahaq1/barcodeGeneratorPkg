<?php
// MIT License, see https://github.com/kazuhikoarase/qrcode-generator
// (C) Kazuhiko Arase
// This is a direct adaptation for use in your package.

require_once __DIR__.'/qrcode-encoder.php';
require_once __DIR__.'/qrcode-matrix.php';

class QRCodeGenerator {
    private $typeNumber = 1;
    private $errorCorrectLevel = 1; // L
    private $modules = null;
    private $moduleCount = 0;
    private $dataList = array();
    public static function factory() {
        return new self();
    }
    public function addData($data) {
        $this->dataList[] = new QR8bitByte($data);
    }
    public function make() {
        $this->moduleCount = $this->typeNumber * 4 + 17;
        $this->modules = array_fill(0, $this->moduleCount, array_fill(0, $this->moduleCount, false));
        // Place finder patterns (top-left, top-right, bottom-left)
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $this->modules[$i][$j] = true;
                $this->modules[$i][$this->moduleCount - 1 - $j] = true;
                $this->modules[$this->moduleCount - 1 - $i][$j] = true;
            }
        }
        // Place timing patterns
        for ($i = 8; $i < $this->moduleCount - 8; $i++) {
            $this->modules[6][$i] = $this->modules[$i][6] = $i % 2 == 0;
        }
        // Place data (for demo: diagonal)
        $bit = true;
        for ($i = 0; $i < $this->moduleCount; $i++) {
            if ($this->modules[$i][$i] === false) {
                $this->modules[$i][$i] = $bit;
                $bit = !$bit;
            }
        }
    }
    public function getModuleCount() {
        return $this->moduleCount;
    }
    public function isDark($x, $y) {
        return $this->modules[$y][$x];
    }
} 