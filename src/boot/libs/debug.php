<?php
if (!function_exists('laraprintr')) {
    function laraprintr($val, $title = null) {
        print '<pre>';
        if ($title) {
            print $title . ' ';
        }
        print_r($val);
        print '</pre>';
    }
}
if (!function_exists('laradump')) {
    function laradump($val, $title = null) {
        print '<pre>';
        if ($title) {
            print $title . ' ';
        }
        if (is_bool($val)) {
            print $val ? '<span style="color:green">YES</span>' : '<span style="color:red">NO</span>';
        }
        else {
            var_dump($val);
        }

        print '</pre>';
    }
}
if (!function_exists('laratrace')) {
    function laratrace($title = 'Trace') {
        $ret   = [];
        $ret[] = str_pad('-', 80, '-') . PHP_EOL;
        $ret[] = str_pad($title, 80, ' ', STR_PAD_BOTH) . PHP_EOL;
        $ret[] = str_pad('-', 80, '-') . PHP_EOL;
        foreach (debug_backtrace() as $t) {
            $ret[] = \Illuminate\Support\Arr::get($t, 'file') . ':' . \Illuminate\Support\Arr::get($t, 'line') . PHP_EOL;
        }
        $ret = implode('', $ret);
        if (PHP_SAPI != 'cli') {
            dump($ret);
        }
        else {
            print $ret;
        }

    }
}
if (!function_exists('larasafepath')) {
    function larasafepath($path) {
        $path = str_replace(['\\', '/'], '/', $path);
        $base_path = str_replace(['\\', '/'], '/', base_path());
        $path = str_replace($base_path, '', $path);
        return $path;
    }
}