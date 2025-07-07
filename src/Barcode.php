<?php

namespace Isahaq\Barcode;

class Barcode
{
    public string $type;
    public string $data;
    public array $bars;
    public int $width;

    public function __construct(string $type, string $data, array $bars, int $width)
    {
        $this->type = $type;
        $this->data = $data;
        $this->bars = $bars;
        $this->width = $width;
    }

    public function getWidth(): int
    {
        return $this->width;
    }
} 