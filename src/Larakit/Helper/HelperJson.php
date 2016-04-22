<?php
namespace Larakit\Helper;

use Illuminate\Support\Arr;

Class HelperJson {

    static function json_encode($data, $options = null) {
        $keys = self::prepareArray($data);
        $json = json_encode(Arr::get($keys, 'data'), $options);
        $json = str_replace(Arr::get($keys, 'replacer'), Arr::get($keys, 'values'), $json);

        return $json;
    }

    static function prepareArray($data, $values = [], $replacer = [], $prefix = '') {
        foreach ($data as $k => &$value) {
            if (is_array($value) || is_object($value)) {
                $keys     = self::prepareArray($value, $values, $replacer, trim($prefix . '-' . $k), '-');
                $values   = Arr::get($keys, 'values');
                $replacer = Arr::get($keys, 'replacer');
                $value    = Arr::get($keys, 'data');
            }
            else {
                $replacer_key = trim($prefix . '-' . $k, '-');
                $tmp          = preg_replace('/[\s\t]/', '', $value);
                if (strpos($tmp, 'function(') === 0) {
                    $values[$replacer_key]   = $value;
                    $value                   = '%' . $replacer_key . '%';
                    $replacer[$replacer_key] = '"' . $value . '"';
                }
            }
        }
        ksort($values);
        ksort($replacer);

        return [
            'values'   => array_values($values),
            'replacer' => array_values($replacer),
            'data'     => $data
        ];
    }
}