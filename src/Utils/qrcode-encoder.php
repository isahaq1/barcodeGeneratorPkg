<?php
// MIT License, see https://github.com/kazuhikoarase/qrcode-generator
// (C) Kazuhiko Arase
// This is a direct adaptation for use in your package.

class QR8bitByte {
    public $mode;
    public $data;
    public $bytes;
    public function __construct($data) {
        $this->mode = 4; // 8bit byte mode
        $this->data = $data;
        $this->bytes = array();
        for ($i = 0; $i < strlen($data); $i++) {
            $this->bytes[] = ord($data[$i]);
        }
    }
    public function getLength() {
        return count($this->bytes);
    }
    public function write(&$buffer) {
        foreach ($this->bytes as $b) {
            $buffer->put($b, 8);
        }
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
} 