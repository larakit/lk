<?php
//регистрируем провайдеры
Larakit\Boot::register_provider(Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
Larakit\Boot::register_provider(\Larakit\LarakitServiceProvider::class);

\Larakit\StaticFiles\Manager::package('larakit/lk')
    ->setSourceDir('public')
    ->usePackage('pear/html_quickform2', ['hierselect', 'repeat',])
    ->usePackage('larakit/sf-larakit-js')
    ->usePackage('larakit/sf-larakit-js')
    ->jsPackage('js/filter-daterange.js')
    ->jsPackage('js/qf-password-twbs.js')
    ->jsPackage('js/lk-quickform.js')
    ->cssPackage('css/filter-daterange.css')
    ->cssPackage('css/lk-quickform.css')
;

\Larakit\StaticFiles\Manager::package('larakit/lk-quickform')
    ->usePackage('pear/html_quickform2', ['hierselect', 'repeat',])
    ->usePackage('larakit/sf-larakit-js')
    ->cssPackage('lk-quickform.css')
    ->jsPackage('qf-password-twbs.js')
    ->jsPackage('lk-quickform.js')
    ->setSourceDir('public');

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
include 'init/quickform.php';
include 'init/page.php';
include 'init/route.php';