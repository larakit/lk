<?php

namespace Larakit\Manager;

class ManagerManyToMany extends ManagerBase {

    /**
     * Регистрация компонента
     *
     * @param      $package
     * @param bool $dir
     */
    static function register($model_class1, $model_class2) {
        static::set([
            1 => $model_class1,
            2 => $model_class2,
        ]);
    }
}