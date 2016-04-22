<?php

namespace Larakit\Manager;

use Illuminate\Support\Arr;

class ManagerMigrator extends ManagerBase
{
    static protected $types = [];

    /**
     * Регистрация компонента
     *
     * @param      $package
     * @param bool $dir
     */
    static function register($model_name, $vendor_and_type = null)
    {
        if(mb_strpos($vendor_and_type, '::')) {
            $item = explode('::', $vendor_and_type);
            $vendor = Arr::get($item, 0);
            $type = Arr::get($item, 1);

        } else {
            $vendor = null;
            $type = $vendor_and_type;
        }
        static::$types[$type] = $type;
        static::add($model_name, [
                'type'   => $type,
                'vendor' => $vendor,
            ]
        );
    }

    /**
     * Получить все типы зарегистрированных миграторов
     *
     * @return array
     */
    static function getTypes()
    {
        return static::$types;
    }
}