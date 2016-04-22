<?php
namespace Larakit\Helper;

use Illuminate\Support\Arr;
use Larakit\Base\Model;

class HelperText {

    /**
     * Передаем дробь, на выходе имеем текст в процентах
     * Text::percent(0.7458) => "74,58%"
     *
     * @param float $val
     *
     * @return string
     */
    static function percent($val) {
        $val = floatval($val) * 100;

        return number_format($val, 0, ',', '.') . '%';
    }


    static function fileSize($bytes, $decimals = 2) {
        $size   = laralang('larakit::filesize');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . Arr::get($size, $factor);
    }

    static function slug($str) {
        $tr = [
            "А" => "A",
            "Б" => "B",
            "В" => "V",
            "Г" => "G",
            "Д" => "D",
            "Е" => "E",
            "Ж" => "J",
            "З" => "Z",
            "И" => "I",
            "Й" => "Y",
            "К" => "K",
            "Л" => "L",
            "М" => "M",
            "Н" => "N",
            "О" => "O",
            "П" => "P",
            "Р" => "R",
            "С" => "S",
            "Т" => "T",
            "У" => "U",
            "Ф" => "F",
            "Х" => "H",
            "Ц" => "TS",
            "Ч" => "CH",
            "Ш" => "SH",
            "Щ" => "SCH",
            "Ъ" => "",
            "Ы" => "YI",
            "Ь" => "",
            "Э" => "E",
            "Ю" => "YU",
            "Я" => "YA",
            "а" => "a",
            "б" => "b",
            "в" => "v",
            "г" => "g",
            "д" => "d",
            "е" => "e",
            "ж" => "j",
            "з" => "z",
            "и" => "i",
            "й" => "y",
            "к" => "k",
            "л" => "l",
            "м" => "m",
            "н" => "n",
            "о" => "o",
            "п" => "p",
            "р" => "r",
            "с" => "s",
            "т" => "t",
            "у" => "u",
            "ф" => "f",
            "х" => "h",
            "ц" => "ts",
            "ч" => "ch",
            "ш" => "sh",
            "щ" => "sch",
            "ъ" => "y",
            "ы" => "yi",
            "ь" => "",
            "э" => "e",
            "ю" => "yu",
            "я" => "ya"
        ];

        $title     = strtr($str, $tr);
        $separator = '-';

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

        // Trim separators from the beginning and end
        $title = trim($title, $separator);

        preg_match_all('/[a-zA-Z0-9' . preg_quote($separator) . ']+/ui', $title, $matches);
        return \Str::lower(implode('', $matches[0]));

    }

    static function plural_with_number($cnt, $form1, $form2, $form5, $form0 = null) {
        $cnt = intval($cnt);
        $ret = self::plural($cnt, $form1, $form2, $form5, $form0);
        if ($cnt) {
            $ret = $cnt . ' ' . $ret;
        }

        return $ret;
    }

    static function plural($cnt, $form1, $form2, $form5, $form0 = null) {
        $cnt = intval($cnt);
        if (!$cnt) {
            if (is_null($form0)) {
                return 'нет ' . $form5;
            } else {
                return $form0;
            }
        }
        $form1 = ($form1);
        $form2 = ($form2);
        $form5 = ($form5);
        $n     = abs($cnt) % 100;
        $n1    = $cnt % 10;
        if ($n > 10 && $n < 20) {
            return $form5;
        }
        if ($n1 > 1 && $n1 < 5)
            return $form2;
        if ($n1 == 1)
            return $form1;

        return $form5;
    }

    static function mb_ucfirst($text) {
        return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
    }

    static function gender($gender, $text_male, $text_female, $text_middle) {
        switch ((int)$gender) {
            case Model::GENDER_FEMALE:
                return $text_female;
                break;
            case Model::GENDER_MIDDLE:
                return $text_middle;
                break;
            default:
                return $text_male;
                break;
        }
    }


    static function masked_email($email, $cnt = 4) {
        $base = explode('@', $email);
        return self::masked_text(Arr::get($base, 0), $cnt) . '@' . Arr::get($base, 1);
    }

    static function masked_text($text, $cnt = 2) {
        $cnt = min($cnt, (int)mb_strlen($text) / 2);
        return str_pad(mb_substr($text, 0, $cnt), mb_strlen($text), '*', STR_PAD_RIGHT);
    }


}