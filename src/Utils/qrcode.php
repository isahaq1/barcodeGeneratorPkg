<?php
// Pure PHP QR Code Generator from scratch
// Implements QR Code specification (ISO/IEC 18004)

class QRCodeGenerator {
    private $typeNumber = 1;
    private $errorCorrectLevel = 1; // L
    private $modules = null;
    private $moduleCount = 0;
    private $dataList = array();
    private $dataCache = null;

    public static function factory() {
        return new self();
    }

    public function addData($data) {
        $this->dataList[] = new QR8bitByte($data);
        $this->dataCache = null;
    }

    public function make() {
        // Auto-select appropriate QR version based on data length
        $this->selectVersion();
        $this->makeImpl(false, $this->getBestMaskPattern());
    }

    private function selectVersion() {
        // Calculate required capacity
        $dataLength = 0;
        foreach ($this->dataList as $data) {
            $dataLength += strlen($data->data);
        }
        
        // Estimate bits needed (4 bits mode + 8 bits length + 8 bits per byte)
        $estimatedBits = 4 + 8 + ($dataLength * 8);
        
        // Find appropriate version
        for ($version = 1; $version <= 10; $version++) {
            $capacity = $this->getCapacity($version, $this->errorCorrectLevel);
            if ($estimatedBits <= $capacity) {
                $this->typeNumber = $version;
                return;
            }
        }
        
        // If we get here, use version 10 (maximum supported)
        $this->typeNumber = 10;
    }

    private function getCapacity($version, $errorLevel) {
        // Simplified capacity calculation
        $baseCapacity = 26; // Version 1, Level L
        return $baseCapacity * $version * (1 - ($errorLevel * 0.25));
    }

    public function getModuleCount() {
        return $this->moduleCount;
    }

    public function isDark($x, $y) {
        if ($this->modules === null) return false;
        return isset($this->modules[$y][$x]) ? $this->modules[$y][$x] : false;
    }

    private function getBestMaskPattern() {
        return 0; // Simple mask pattern for now
    }

    private function makeImpl($test, $maskPattern) {
        $this->moduleCount = $this->typeNumber * 4 + 17;
        $this->modules = array();
        
        // Initialize matrix
        for ($row = 0; $row < $this->moduleCount; $row++) {
            $this->modules[$row] = array_fill(0, $this->moduleCount, null);
        }

        // Place finder patterns
        $this->setupPositionProbePattern(0, 0);
        $this->setupPositionProbePattern($this->moduleCount - 7, 0);
        $this->setupPositionProbePattern(0, $this->moduleCount - 7);

        // Place timing patterns
        $this->setupTimingPattern();

        // Place type info
        $this->setupTypeInfo($test, $maskPattern);

        // Place position adjustment patterns
        if ($this->typeNumber >= 2) {
            $this->setupPositionAdjustPattern();
        }

        // Create and place data
        $data = $this->createData($this->typeNumber, $this->errorCorrectLevel, $this->dataList);
        $this->mapData($data, $maskPattern);
    }

    private function setupPositionProbePattern($row, $col) {
        for ($r = 0; $r < 7; $r++) {
            for ($c = 0; $c < 7; $c++) {
                if (($r == 0 || $r == 6 || $c == 0 || $c == 6) ||
                    ($r >= 2 && $r <= 4 && $c >= 2 && $c <= 4)) {
                    $this->modules[$row + $r][$col + $c] = true;
                } else {
                    $this->modules[$row + $r][$col + $c] = false;
                }
            }
        }
    }

    private function setupTimingPattern() {
        for ($i = 8; $i < $this->moduleCount - 8; $i++) {
            if ($this->modules[6][$i] === null) {
                $this->modules[6][$i] = ($i % 2 == 0);
            }
        }
        for ($i = 8; $i < $this->moduleCount - 8; $i++) {
            if ($this->modules[$i][6] === null) {
                $this->modules[$i][6] = ($i % 2 == 0);
            }
        }
    }

    private function setupTypeInfo($test, $maskPattern) {
        $data = ($this->errorCorrectLevel << 3) | $maskPattern;
        $bits = $this->getBCHTypeInfo($data);

        for ($i = 0; $i < 15; $i++) {
            $mod = (!$test && (($bits >> $i) & 1) == 1);
            if ($i < 6) {
                $this->modules[$i][8] = $mod;
            } elseif ($i < 8) {
                $this->modules[$i + 1][8] = $mod;
            } else {
                $this->modules[$this->moduleCount - 15 + $i][8] = $mod;
            }
        }

        for ($i = 0; $i < 15; $i++) {
            $mod = (!$test && (($bits >> $i) & 1) == 1);
            if ($i < 8) {
                $this->modules[8][$this->moduleCount - $i - 1] = $mod;
            } elseif ($i < 9) {
                $this->modules[8][15 - $i - 1 + 1] = $mod;
            } else {
                $this->modules[8][15 - $i - 1] = $mod;
            }
        }

        $this->modules[$this->moduleCount - 8][8] = !$test;
    }

    private function setupPositionAdjustPattern() {
        $pos = QRUtil::getPatternPosition($this->typeNumber);
        foreach ($pos as $i) {
            foreach ($pos as $j) {
                if ($this->modules[$i][$j] === null) {
                    for ($r = -2; $r <= 2; $r++) {
                        for ($c = -2; $c <= 2; $c++) {
                            if ($r == -2 || $r == 2 || $c == -2 || $c == 2 ||
                                ($r == 0 && $c == 0)) {
                                $this->modules[$i + $r][$j + $c] = true;
                            } else {
                                $this->modules[$i + $r][$j + $c] = false;
                            }
                        }
                    }
                }
            }
        }
    }

    private function getBCHTypeInfo($data) {
        $d = $data << 10;
        while ($this->getBCHDigit($d) - $this->getBCHDigit(1335) >= 0) {
            $d ^= (1335 << ($this->getBCHDigit($d) - $this->getBCHDigit(1335)));
        }
        return (($data << 10) | $d) ^ 21522;
    }

    private function getBCHDigit($data) {
        $digit = 0;
        while ($data != 0) {
            $digit++;
            $data >>= 1;
        }
        return $digit;
    }

    private function createData($typeNumber, $errorCorrectLevel, $dataList) {
        $rsBlocks = QRRSBlock::getRSBlocks($typeNumber, $errorCorrectLevel);
        $buffer = new QRBitBuffer();
        
        foreach ($dataList as $data) {
            $buffer->put($data->mode, 4);
            $buffer->put($data->getLength(), $this->getLengthInBits($data->mode, $typeNumber));
            $data->write($buffer);
        }

        $totalDataCount = 0;
        foreach ($rsBlocks as $rsBlock) {
            $totalDataCount += $rsBlock->getDataCount();
        }

        if ($buffer->getLengthInBits() > $totalDataCount * 8) {
            throw new Exception("code length overflow. (" . $buffer->getLengthInBits() . ">" . ($totalDataCount * 8) . ")");
        }

        if ($buffer->getLengthInBits() + 4 <= $totalDataCount * 8) {
            $buffer->put(0, 4);
        }

        while ($buffer->getLengthInBits() % 8 != 0) {
            $buffer->putBit(false);
        }

        while (true) {
            if ($buffer->getLengthInBits() >= $totalDataCount * 8) {
                break;
            }
            $buffer->put(0xEC, 8);

            if ($buffer->getLengthInBits() >= $totalDataCount * 8) {
                break;
            }
            $buffer->put(0x11, 8);
        }

        return QRCode::createBytes($buffer, $rsBlocks);
    }

    private function getLengthInBits($mode, $type) {
        if (1 <= $type && $type < 10) {
            switch ($mode) {
                case 0: return 10;
                case 1: return 9;
                case 2: return 8;
                case 3: return 8;
                case 4: return 8; // 8-bit byte mode
                default: throw new Exception("mode:" . $mode);
            }
        } elseif ($type < 27) {
            switch ($mode) {
                case 0: return 12;
                case 1: return 11;
                case 2: return 16;
                case 3: return 10;
                case 4: return 8; // 8-bit byte mode
                default: throw new Exception("mode:" . $mode);
            }
        } elseif ($type < 41) {
            switch ($mode) {
                case 0: return 14;
                case 1: return 13;
                case 2: return 16;
                case 3: return 12;
                case 4: return 8; // 8-bit byte mode
                default: throw new Exception("mode:" . $mode);
            }
        } else {
            throw new Exception("type:" . $type);
        }
    }

    private function mapData($data, $maskPattern) {
        $inc = -1;
        $row = $this->moduleCount - 1;
        $bitIndex = 7;
        $byteIndex = 0;

        for ($col = $this->moduleCount - 1; $col > 0; $col -= 2) {
            if ($col == 6) $col--;

            while (true) {
                for ($c = 0; $c < 2; $c++) {
                    if ($this->modules[$row][$col - $c] === null) {
                        $dark = false;

                        if ($byteIndex < count($data)) {
                            $dark = (((($data[$byteIndex] >> $bitIndex) & 1) == 1));
                        }

                        $mask = $this->getMask($maskPattern, $row, $col - $c);
                        if ($mask) {
                            $dark = !$dark;
                        }

                        $this->modules[$row][$col - $c] = $dark;
                        $bitIndex--;

                        if ($bitIndex == -1) {
                            $byteIndex++;
                            $bitIndex = 7;
                        }
                    }
                }

                $row += $inc;

                if ($row < 0 || $this->moduleCount <= $row) {
                    $row -= $inc;
                    $inc = -$inc;
                    break;
                }
            }
        }
    }

    private function getMask($maskPattern, $i, $j) {
        switch ($maskPattern) {
            case 0: return ($i + $j) % 2 == 0;
            case 1: return $i % 2 == 0;
            case 2: return $j % 3 == 0;
            case 3: return ($i + $j) % 3 == 0;
            case 4: return (floor($i / 2) + floor($j / 3)) % 2 == 0;
            case 5: return (($i * $j) % 2 + ($i * $j) % 3) == 0;
            case 6: return ((($i * $j) % 2 + ($i * $j) % 3) % 2) == 0;
            case 7: return ((($i + $j) % 2 + ($i * $j) % 3) % 2) == 0;
            default: throw new Exception("bad maskPattern:" . $maskPattern);
        }
    }
}

// QR Code utility classes
class QRRSBlock {
    private $totalCount;
    private $dataCount;

    public function __construct($totalCount, $dataCount) {
        $this->totalCount = $totalCount;
        $this->dataCount = $dataCount;
    }

    public function getDataCount() {
        return $this->dataCount;
    }

    public function getTotalCount() {
        return $this->totalCount;
    }

    public static function getRSBlocks($typeNumber, $errorCorrectLevel) {
        $rsBlock = self::getRsBlockTable($typeNumber, $errorCorrectLevel);
        $length = count($rsBlock) / 3;
        $list = array();

        for ($i = 0; $i < $length; $i++) {
            $count = $rsBlock[$i * 3 + 0];
            $totalCount = $rsBlock[$i * 3 + 1];
            $dataCount = $rsBlock[$i * 3 + 2];

            for ($j = 0; $j < $count; $j++) {
                $list[] = new QRRSBlock($totalCount, $dataCount);
            }
        }

        return $list;
    }

    private static function getRsBlockTable($typeNumber, $errorCorrectLevel) {
        switch ($errorCorrectLevel) {
            case 0: return self::getRsBlockTable0($typeNumber);
            case 1: return self::getRsBlockTable1($typeNumber);
            case 2: return self::getRsBlockTable2($typeNumber);
            case 3: return self::getRsBlockTable3($typeNumber);
            default: throw new Exception("bad rs block @ errorCorrectLevel:" . $errorCorrectLevel);
        }
    }

    private static function getRsBlockTable0($typeNumber) {
        switch ($typeNumber) {
            case 1: return array(1, 26, 19);
            case 2: return array(1, 44, 34);
            case 3: return array(1, 70, 55);
            case 4: return array(1, 100, 80);
            case 5: return array(1, 134, 108);
            case 6: return array(2, 86, 68);
            case 7: return array(2, 112, 90);
            case 8: return array(2, 140, 112);
            case 9: return array(2, 172, 140);
            case 10: return array(2, 196, 168);
            default: throw new Exception("bad rs block @ typeNumber:" . $typeNumber);
        }
    }

    private static function getRsBlockTable1($typeNumber) {
        switch ($typeNumber) {
            case 1: return array(1, 26, 16);
            case 2: return array(1, 44, 28);
            case 3: return array(1, 70, 44);
            case 4: return array(1, 100, 64);
            case 5: return array(1, 134, 86);
            case 6: return array(2, 86, 56);
            case 7: return array(2, 112, 72);
            case 8: return array(2, 140, 88);
            case 9: return array(2, 172, 110);
            case 10: return array(2, 196, 130);
            default: throw new Exception("bad rs block @ typeNumber:" . $typeNumber);
        }
    }

    private static function getRsBlockTable2($typeNumber) {
        switch ($typeNumber) {
            case 1: return array(1, 26, 13);
            case 2: return array(1, 44, 22);
            case 3: return array(1, 70, 34);
            case 4: return array(1, 100, 48);
            case 5: return array(1, 134, 62);
            case 6: return array(2, 86, 44);
            case 7: return array(2, 112, 58);
            case 8: return array(2, 140, 72);
            case 9: return array(2, 172, 88);
            case 10: return array(2, 196, 110);
            default: throw new Exception("bad rs block @ typeNumber:" . $typeNumber);
        }
    }

    private static function getRsBlockTable3($typeNumber) {
        switch ($typeNumber) {
            case 1: return array(1, 26, 9);
            case 2: return array(1, 44, 16);
            case 3: return array(1, 70, 26);
            case 4: return array(1, 100, 36);
            case 5: return array(1, 134, 46);
            case 6: return array(2, 86, 32);
            case 7: return array(2, 112, 42);
            case 8: return array(2, 140, 52);
            case 9: return array(2, 172, 64);
            case 10: return array(2, 196, 76);
            default: throw new Exception("bad rs block @ typeNumber:" . $typeNumber);
        }
    }
}

class QRCode {
    public static function createBytes($buffer, $rsBlocks) {
        $offset = 0;
        $maxDcCount = 0;
        $maxEcCount = 0;
        $dcdata = array();
        $ecdata = array();

        foreach ($rsBlocks as $r => $rsBlock) {
            $dcCount = $rsBlock->getDataCount();
            $ecCount = $rsBlock->getTotalCount() - $dcCount;
            $maxDcCount = max($maxDcCount, $dcCount);
            $maxEcCount = max($maxEcCount, $ecCount);

            $dcdata[$r] = array();
            for ($i = 0; $i < $dcCount; $i++) {
                $dcdata[$r][$i] = 0xff & $buffer->get($offset + $i);
            }
            $offset += $dcCount;

            $rsPoly = QRUtil::getErrorCorrectPolynomial($ecCount);
            $rawPoly = new QRPolynomial($dcdata[$r], $rsPoly->getLength() - 1);
            $modPoly = $rawPoly->mod($rsPoly);
            $ecdata[$r] = array();
            for ($i = 0; $i < $rsPoly->getLength() - 1; $i++) {
                $modIndex = $i + $modPoly->getLength() - count($ecdata[$r]);
                $ecdata[$r][$i] = ($modIndex >= 0) ? $modPoly->get($modIndex) : 0;
            }
        }

        $totalCodeCount = 0;
        foreach ($rsBlocks as $rsBlock) {
            $totalCodeCount += $rsBlock->getTotalCount();
        }

        $data = array();
        $index = 0;

        for ($i = 0; $i < $maxDcCount; $i++) {
            foreach ($rsBlocks as $r => $rsBlock) {
                if ($i < count($dcdata[$r])) {
                    $data[$index++] = $dcdata[$r][$i];
                }
            }
        }

        for ($i = 0; $i < $maxEcCount; $i++) {
            foreach ($rsBlocks as $r => $rsBlock) {
                if ($i < count($ecdata[$r])) {
                    $data[$index++] = $ecdata[$r][$i];
                }
            }
        }

        return $data;
    }
}

class QRPolynomial {
    private $num;

    public function __construct($num, $shift = 0) {
        $offset = 0;
        while ($offset < count($num) && $num[$offset] == 0) {
            $offset++;
        }
        $this->num = array();
        for ($i = 0; $i < count($num) - $offset; $i++) {
            $this->num[$i] = $num[$i + $offset];
        }
        for ($i = 0; $i < $shift; $i++) {
            $this->num[] = 0;
        }
    }

    public function get($index) {
        return $this->num[$index];
    }

    public function getLength() {
        return count($this->num);
    }

    public function multiply($e) {
        $num = array();
        for ($i = 0; $i < $this->getLength() + $e->getLength() - 1; $i++) {
            $num[$i] = 0;
        }
        for ($i = 0; $i < $this->getLength(); $i++) {
            for ($j = 0; $j < $e->getLength(); $j++) {
                $num[$i + $j] ^= QRMath::gexp(QRMath::glog($this->get($i)) + QRMath::glog($e->get($j)));
            }
        }
        return new QRPolynomial($num, 0);
    }

    public function mod($e) {
        if ($this->getLength() - $e->getLength() < 0) {
            return $this;
        }
        $ratio = QRMath::glog($this->get(0)) - QRMath::glog($e->get(0));
        $num = array();
        for ($i = 0; $i < $this->getLength(); $i++) {
            $num[$i] = $this->get($i);
        }
        for ($i = 0; $i < $e->getLength(); $i++) {
            $num[$i] ^= QRMath::gexp(QRMath::glog($e->get($i)) + $ratio);
        }
        $newPolynomial = new QRPolynomial($num, 0);
        return $newPolynomial->mod($e);
    }
}

class QRMath {
    private static $glog = array();
    private static $gexp = array();
    private static $initialized = false;

    private static function init() {
        if (self::$initialized) return;
        
        for ($i = 0; $i < 8; $i++) {
            self::$glog[$i] = 0;
        }
        for ($i = 8; $i < 256; $i++) {
            self::$glog[$i] = self::$glog[$i - 4] ^ self::$glog[$i - 5] ^ self::$glog[$i - 6] ^ self::$glog[$i - 8];
        }
        for ($i = 0; $i < 255; $i++) {
            self::$gexp[$i] = self::$glog[$i];
        }
        for ($i = 255; $i < 512; $i++) {
            self::$gexp[$i] = self::$gexp[$i - 255];
        }
        
        self::$initialized = true;
    }

    public static function glog($n) {
        self::init();
        if ($n < 1) {
            throw new Exception("glog(" . $n . ")");
        }
        return self::$glog[$n];
    }

    public static function gexp($n) {
        self::init();
        while ($n < 0) {
            $n += 255;
        }
        while ($n >= 256) {
            $n -= 255;
        }
        return self::$gexp[$n];
    }
}

class QRUtil {
    public static function getPatternPosition($typeNumber) {
        if ($typeNumber == 1) return array();
        $pos = array(6);
        $max = $typeNumber * 4 + 10;
        $interval = $typeNumber == 2 ? 18 : ceil(($max - 13) / ($typeNumber - 1));
        for ($i = $max - 7; count($pos) < $typeNumber - 1; $i -= $interval) {
            array_unshift($pos, $i);
        }
        $pos[] = $max - 7;
        return $pos;
    }

    public static function getErrorCorrectPolynomial($errorCorrectLength) {
        $a = new QRPolynomial(array(1), 0);
        for ($i = 0; $i < $errorCorrectLength; $i++) {
            $a = $a->multiply(new QRPolynomial(array(1, QRMath::gexp($i)), 0));
        }
        return $a;
    }
}

require_once __DIR__.'/qrcode-encoder.php';
require_once __DIR__.'/qrcode-matrix.php'; 