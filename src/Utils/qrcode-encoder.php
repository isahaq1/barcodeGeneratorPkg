<?php
// QR Code Data Encoding Classes

class QRDataSegment {
    public $mode;
    public $data;
    public $bytes;
    
    // Mode constants
    const MODE_NUMERIC = 1;
    const MODE_ALPHANUMERIC = 2;
    const MODE_BYTE = 4;
    const MODE_KANJI = 8;

    public static function make($data) {
        if (preg_match('/^[0-9]+$/', $data)) {
            return new self(self::MODE_NUMERIC, $data);
        } elseif (preg_match('/^[A-Z0-9 $%*+\-\.\/\:]+$/', $data)) {
            return new self(self::MODE_ALPHANUMERIC, $data);
        } elseif (self::isKanji($data)) {
            return new self(self::MODE_KANJI, $data);
        } else {
            return new self(self::MODE_BYTE, $data);
        }
    }

    public function __construct($mode, $data) {
        $this->mode = $mode;
        $this->data = $data;
        $this->bytes = [];
        if ($mode === self::MODE_BYTE) {
            for ($i = 0; $i < strlen($data); $i++) {
                $this->bytes[] = ord($data[$i]);
            }
        }
    }

    public function getLength() {
        if ($this->mode === self::MODE_NUMERIC) {
            return strlen($this->data);
        } elseif ($this->mode === self::MODE_ALPHANUMERIC) {
            return strlen($this->data);
        } elseif ($this->mode === self::MODE_BYTE) {
            return count($this->bytes);
        } elseif ($this->mode === self::MODE_KANJI) {
            return mb_strlen($this->data, 'SJIS');
        }
        return 0;
    }

    public function write(&$buffer) {
        if ($this->mode === self::MODE_NUMERIC) {
            $i = 0;
            $len = strlen($this->data);
            while ($i < $len) {
                $n = min(3, $len - $i);
                $chunk = substr($this->data, $i, $n);
                $buffer->put((int)$chunk, $n == 3 ? 10 : ($n == 2 ? 7 : 4));
                $i += $n;
            }
        } elseif ($this->mode === self::MODE_ALPHANUMERIC) {
            $table = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ $%*+-./:';
            $i = 0;
            $len = strlen($this->data);
            while ($i + 1 < $len) {
                $v = strpos($table, $this->data[$i]) * 45 + strpos($table, $this->data[$i+1]);
                $buffer->put($v, 11);
                $i += 2;
            }
            if ($i < $len) {
                $v = strpos($table, $this->data[$i]);
                $buffer->put($v, 6);
            }
        } elseif ($this->mode === self::MODE_BYTE) {
            foreach ($this->bytes as $b) {
                $buffer->put($b, 8);
            }
        } elseif ($this->mode === self::MODE_KANJI) {
            // Kanji encoding (Shift JIS)
            $sjis = mb_convert_encoding($this->data, 'SJIS', 'UTF-8');
            for ($i = 0; $i < strlen($sjis); $i += 2) {
                $c = (ord($sjis[$i]) << 8) | ord($sjis[$i+1]);
                if ($c >= 0x8140 && $c <= 0x9FFC) {
                    $sub = $c - 0x8140;
                } elseif ($c >= 0xE040 && $c <= 0xEBBF) {
                    $sub = $c - 0xC140;
                } else {
                    continue;
                }
                $buffer->put((($sub >> 8) * 0xC0) + ($sub & 0xFF), 13);
            }
        }
    }

    public static function isKanji($data) {
        // Simple check for double-byte Kanji (Shift JIS range)
        return false; // For now, skip Kanji unless you want to support it
    }
}

class QRBitBuffer {
    public $buffer = array();
    public $length = 0;
    
    public function put($num, $length) {
        for ($i = 0; $i < $length; $i++) {
            $this->putBit((($num >> ($length - $i - 1)) & 1) == 1);
        }
    }
    
    public function putBit($bit) {
        $bufIndex = intdiv($this->length, 8);
        if (count($this->buffer) <= $bufIndex) {
            $this->buffer[] = 0;
        }
        if ($bit) {
            $this->buffer[$bufIndex] |= (0x80 >> ($this->length % 8));
        }
        $this->length++;
    }
    
    public function get($index) {
        $bufIndex = intdiv($index, 8);
        return (($this->buffer[$bufIndex] >> (7 - ($index % 8))) & 1);
    }
    
    public function getLengthInBits() {
        return $this->length;
    }
} 