<?php
// MIT License, see https://github.com/kazuhikoarase/qrcode-generator
// (C) Kazuhiko Arase
// This is a direct adaptation for use in your package.

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
} 