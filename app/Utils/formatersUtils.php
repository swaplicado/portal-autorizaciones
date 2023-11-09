<?php namespace App\Utils;

class formatersUtils {
    public static function formatCoin($value) {
        $result = "$" . number_format((float)$value, 2, '.', ',');
        return $result;
    }

    public static function formatNumber($value, $numberDecimal = 2, $separator = ',') {
        $result = number_format((float)$value, $numberDecimal, '.', $separator);
        return $result;
    }
}