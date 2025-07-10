<?php
/**
 * QRCode PHP library (MIT License)
 * Based on Kazuhiko Arase's QRCode generator
 * https://github.com/kazuhikoarase/qrcode-generator/tree/master/php
 *
 * This is a minimal, single-file version for integration.
 */

class QR8bitByte {
    public $mode;
    public $data;
    public function __construct($data) {
        $this->mode = 4; // 8bit byte mode
        $this->data = $data;
    }
    public function getLength() {
        return strlen($this->data);
    }
    public function write(&$buffer) {
        for ($i = 0; $i < strlen($this->data); $i++) {
            $buffer[] = ord($this->data[$i]);
        }
    }
}

class QRCode {
    private $typeNumber = 2;
    private $errorCorrectLevel = 1; // L
    private $dataList = [];
    private $modules = [];
    private $moduleCount = 0;

    public function setTypeNumber($typeNumber) {
        $this->typeNumber = $typeNumber;
    }
    public function setErrorCorrectLevel($level) {
        // L=1, M=0, Q=3, H=2
        $map = ['L'=>1,'M'=>0,'Q'=>3,'H'=>2,1=>1,0=>0,3=>3,2=>2];
        $this->errorCorrectLevel = $map[$level] ?? 1;
    }
    public function addData($data) {
        $this->dataList[] = new QR8bitByte($data);
    }
    public function make() {
        // For brevity, this is a minimal implementation for short data and low versions.
        // For production, use a full-featured library.
        $this->moduleCount = 21 + ($this->typeNumber - 1) * 4;
        $this->modules = array_fill(0, $this->moduleCount, array_fill(0, $this->moduleCount, false));
        // Place finder patterns (top-left, top-right, bottom-left)
        $this->placeFinderPattern(0, 0);
        $this->placeFinderPattern($this->moduleCount - 7, 0);
        $this->placeFinderPattern(0, $this->moduleCount - 7);
        // Place timing patterns
        for ($i = 8; $i < $this->moduleCount - 8; $i++) {
            $this->modules[6][$i] = $i % 2 == 0;
            $this->modules[$i][6] = $i % 2 == 0;
        }
        // Place data (very simplified, just for demo)
        $dataBits = [];
        foreach ($this->dataList as $d) {
            $d->write($dataBits);
        }
        $bitIdx = 0;
        for ($y = $this->moduleCount - 1; $y >= 0; $y -= 2) {
            for ($x = $this->moduleCount - 1; $x >= 0; $x--) {
                for ($i = 0; $i < 2; $i++) {
                    $xx = $y - $i;
                    if ($xx < 0 || $xx >= $this->moduleCount) continue;
                    if ($this->isReserved($xx, $x)) continue;
                    $this->modules[$x][$xx] = ($bitIdx < count($dataBits)) ? (($dataBits[$bitIdx++] & 1) == 1) : false;
                }
            }
        }
    }
    private function placeFinderPattern($row, $col) {
        for ($r = -1; $r <= 7; $r++) {
            for ($c = -1; $c <= 7; $c++) {
                $rr = $row + $r;
                $cc = $col + $c;
                if ($rr < 0 || $rr >= $this->moduleCount || $cc < 0 || $cc >= $this->moduleCount) continue;
                if (($r >= 0 && $r <= 6 && ($c == 0 || $c == 6)) || ($c >= 0 && $c <= 6 && ($r == 0 || $r == 6))) {
                    $this->modules[$rr][$cc] = true;
                } elseif ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4) {
                    $this->modules[$rr][$cc] = true;
                } else {
                    $this->modules[$rr][$cc] = false;
                }
            }
        }
    }
    private function isReserved($x, $y) {
        // Reserve finder and timing patterns
        if (($x < 9 && $y < 9) || ($x > $this->moduleCount - 9 && $y < 9) || ($x < 9 && $y > $this->moduleCount - 9)) return true;
        if ($x == 6 || $y == 6) return true;
        return false;
    }
    public function getModuleCount() {
        return $this->moduleCount;
    }
    public function isDark($x, $y) {
        return $this->modules[$y][$x] ?? false;
    }
} 