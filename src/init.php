<?php
//регистрируем провайдеры
Larakit\SPA::register_provider(Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
Larakit\SPA::register_provider(\Larakit\LarakitServiceProvider::class);


if (!function_exists('rglob')) {
    function rglob($pattern = '*', $flags = 0, $path = false) {
        if (!$path) {
            $path = dirname($pattern) . DIRECTORY_SEPARATOR;
        }
        $pattern = basename($pattern);
        $paths   = glob($path . '*', GLOB_MARK | GLOB_ONLYDIR | GLOB_NOSORT);
        $files   = glob($path . $pattern, $flags);
        foreach ($paths as $path) {
            $files = array_merge($files, rglob($pattern, $flags, $path));
        }

        $files = array_map(['Larakit\Helper\HelperFile', 'normalizeFilePath'], $files);
        return $files;
    }
}