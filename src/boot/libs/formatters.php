<?php
if (!function_exists('format_widget_date')) {
    function format_widget_date($value) {
        dd(1);
        return $value ? \Carbon\Carbon::parse($value)->format('d.m.Y') : null;
    }
}
if (!function_exists('format_widget_datetime')) {
    function format_widget_datetime($value) {
        return $value ? \Carbon\Carbon::parse($value)->format('d.m.Y H:i:s') : null;
    }
}
if (!function_exists('format_widget_time')) {
    function format_widget_time($value) {
        return $value ? \Carbon\Carbon::parse($value)->format('H:i:s') : null;
    }
}
if (!function_exists('filter_to_datetime')) {
    function filter_to_datetime($value) {
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s') : null;
    }
}
if (!function_exists('filter_to_date')) {
    function filter_to_date($value) {
        $value = \Carbon\Carbon::parse($value);
        return $value ? $value->format('Y-m-d') : null;
    }
}
if (!function_exists('filter_to_time')) {
    function filter_to_time($value) {
        $value = \Carbon\Carbon::parse($value);
        return $value ? $value->format('H:i:s') : null;
    }
}
if (!function_exists('human_file_size')) {
    function human_file_size($size) {
        if ($size >= 1073741824) {
            $fileSize = round($size / 1024 / 1024 / 1024, 1) . ' ГБ';
        } elseif ($size >= 1048576) {
            $fileSize = round($size / 1024 / 1024, 1) . ' МБ';
        } elseif ($size >= 1024) {
            $fileSize = round($size / 1024, 1) . ' КБ';
        } else {
            $fileSize = \Larakit\Helper\HelperText::plural($size, 'байт', 'байта', 'байтов');
        }
        return $fileSize;
    }
}

