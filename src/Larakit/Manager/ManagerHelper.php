<?php
namespace Larakit\Manager;

class ManagerHelper extends ManagerBase {
    /**
     * Регистрация хелпера
     *
     * @param      $package
     * @param bool $dir
     */
    static function register($helper, $class) {
        static::set($class, $helper);
    }
}