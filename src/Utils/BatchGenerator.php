<?php

namespace Isahaq\Barcode\Utils;

use Isahaq\Barcode\Types\BarcodeTypeInterface;
use Isahaq\Barcode\Renderers\RendererInterface;

class BatchGenerator
{
    public static function generate(BarcodeTypeInterface $type, RendererInterface $renderer, array $dataList, array $options = []): array
    {
        $results = [];
        foreach ($dataList as $data) {
            $error = null;
            if (!$type->validate($data)) {
                $results[] = [
                    'data' => $data,
                    'result' => null,
                    'error' => 'Invalid data for barcode type.'
                ];
                continue;
            }
            $barcode = $type->encode($data);
            $result = $renderer->render($barcode, $options);
            $results[] = [
                'data' => $data,
                'result' => $result,
                'error' => null
            ];
        }
        return $results;
    }
} 