<?php
namespace Larakit\Helper;

use Illuminate\Support\Arr;

class HelperPhone {
    /*static function denormalize($phone) {
        if (mb_substr($phone, 0, 5) == '73522') {
            return '8 (3522) ' . mb_substr($phone, 5, 2) . '-' . mb_substr($phone, 7, 2) . '-' . mb_substr(
                $phone,
                9,
                2
            );
        }
        else {
            return '+7 (' . mb_substr($phone, 1, 3) . ') ' . mb_substr($phone, 4, 3) . '-' . mb_substr(
                $phone,
                7,
                2
            ) . '-' . mb_substr($phone, 9, 2);
        }
    }*/

    static function normalize($phone) {
        $p = preg_replace('/\D/', '', $phone);

        return $p;
    }

    static function country($phone) {
        $p      = self::normalize($phone);
        $length = mb_strlen($p);
        $config = \Config::get('larakit::phones.' . $length);
        $codes  = array_keys($config);
        rsort($codes);
        foreach ($codes as $code) {
            if ($code == mb_substr($p, 0, mb_strlen($code))) {
                return Arr::get($config, $code . '.code');
            }
        }
        return null;
    }

    static function denormalize($phone) {
        $p      = self::normalize($phone);
        $length = mb_strlen($p);
        if (6 == $length) {
            $p      = '83522' . $p;
            $length = mb_strlen($p);
        }
        $config = \Config::get('larakit::phones.' . $length, []);
        if (!count($config)) {
            return $p;
        }
        $codes = array_keys($config);
        rsort($codes);
        foreach ($codes as $code) {
            if ($code == mb_substr($p, 0, mb_strlen($code))) {
                $mask        = Arr::get($config, $code . '.mask');
                $phone_array = str_split(mb_substr($p, mb_strlen($code)));
                $mask_array  = str_split($mask);
                foreach ($mask_array as $key => $symbol) {
                    if ('#' == $symbol) {
                        $mask_array[$key] = array_shift($phone_array);
                    }
                }
                return implode('', $mask_array);
            }
        }

        return null;
    }
}

