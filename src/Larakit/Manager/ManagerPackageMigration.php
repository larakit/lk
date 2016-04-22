<?php

namespace Larakit\Manager;


class ManagerPackageMigration extends ManagerBase{

    /**
     * Регистрация компонента
     *
     * @param      $package
     * @param bool $dir
     */
    static function register($package) {
        static::set($package, $package);
    }
} 