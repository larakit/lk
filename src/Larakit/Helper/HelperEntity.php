<?php
namespace Larakit\Helper;

use Illuminate\Support\Arr;
use Larakit\Base\Accessor;
use Larakit\Base\Acl;
use Larakit\Base\FormFilter;
use Larakit\Base\Model;
use Larakit\QuickForm\LaraForm;

Class HelperEntity {

    static protected $objects = [];

    static function parseBaseRoute($route) {
        $r      = explode('::', $route);
        $vendor = Arr::get($r, 0);
        $two    = Arr::get($r, 1);
        if (mb_strpos($two, '.')) {
            $two     = explode('.', $two);
            $section = Arr::get($two, 0);
            $entity  = Arr::get($two, 1);
        } else {
            $section = null;
            $entity  = $two;
        }

        return compact('vendor', 'section', 'entity');
    }


    /**
     * Получить Entity по классу
     *
     * @param string|\stdClass $class_name
     *
     * @return string
     * @throws \Exception
     */
    static function getEntity() {
        $object = self::getArgs(func_get_args());

        return (string)Arr::get(self::deNormalize($object), 'entity');
    }


    static function getVendor() {
        $object = self::getArgs(func_get_args());

        return (string)Arr::get(self::deNormalize($object), 'vendor');
    }

    static function getSection() {
        $object = self::getArgs(func_get_args());

        return (string)Arr::get(self::deNormalize($object), 'section');
    }

    static protected function deNormalize($object) {
        if (is_string($object)) {
            $class_name = $object;
        } elseif (is_object($object)) {
            $class_name = get_class($object);
        } elseif (is_array($object)) {
            $v          = Arr::get($object, 'vendor');
            $e          = Arr::get($object, 'entity');
            $s          = Arr::get($object, 'section');
            $class_name = self::makeClassName($v, $e, $s);
        } else {
            throw new \Exception('Ошибка получения Entity' . var_dump($object));
        }

        if (isset(self::$objects[$class_name])) {
            return self::$objects[$class_name];
        }

        if (!class_exists($class_name)) {
            throw new \Exception('Class ' . $class_name . ' not exists!');
        }

        if (is_a($class_name, \Controller::class, true)) {
            return self::deNormalizeController($class_name);
        }
        if (is_a($class_name, Model::class, true)) {
            return self::deNormalizeModel($class_name);
        }
        if (is_a($class_name, Accessor::class, true)) {
            return self::deNormalizeAccessor($class_name);
        }
        if (is_a($class_name, Acl::class, true)) {
            return self::deNormalizeAcl($class_name);
        }
        if (is_a($class_name, LaraForm::class, true)) {
            return self::deNormalizeForm($class_name);
        }
        if (is_a($class_name, FormFilter::class, true)) {
            return self::deNormalizeFormFilter($class_name);
        }


        //        dump(is_a($class_name, Model::class, true));
        dump(__FILE__ . ':' . __LINE__);
        dd($class_name);

        return [];
    }

    static protected function makeClassName($vendor, $entity, $section) {
        //        if(!$section)
        $vendor  = self::vendorToStudly($vendor);
        $entity  = \Str::studly($entity);
        $section = \Str::studly($section);
        if (!$section) {
            $class_name = $vendor . '\Model\\' . $entity;
        } else {
            $class_name = $vendor . '\Controller\\' . $section . '\Controller' . $section . '' . $entity;
        }

        return $class_name;
    }

    /**
     * @param Model $class_name
     *
     * @return array
     */
    static protected function deNormalizeModel($class_name) {
        preg_match('|^([A-Za-z\\\\]+)Model\\\\([A-Za-z]+)$|Umsi', $class_name, $matches);
        $vendor = trim(Arr::get($matches, 1, ''), '\\');
        $entity = trim(Arr::get($matches, 2, ''), '\\');
        $ret    = [
            'vendor' => str_replace('\\', '', \Str::snake($vendor, '-')),
            'entity' => \Str::snake($entity),
        ];

        return $ret;
    }

    /**
     * @param Accessor $class_name
     *
     * @return array
     */
    static protected function deNormalizeAccessor($class_name) {
        preg_match('|^([A-Za-z\\\\]+)Accessor\\\\Accessor([A-Za-z]+)$|Umsi', $class_name, $matches);
        $vendor = trim(Arr::get($matches, 1, ''), '\\');
        $entity = trim(Arr::get($matches, 2, ''), '\\');

        $reflection = new \ReflectionClass($class_name);
        $extends    = $reflection->getParentClass();
        if ($extends->getName() != Accessor::class) {
            $parent_entity        = self::getEntity($extends->getName());
            $parent_studly_entity = \Str::camel($parent_entity);
            $section              = mb_substr($entity, mb_strlen($parent_studly_entity));
            $entity               = $parent_entity;
        } else {
            $section = '';
        }


        $ret = [
            'vendor'  => str_replace('\\', '', \Str::snake($vendor, '-')),
            'entity'  => \Str::snake($entity),
            'section' => \Str::snake($section)
        ];

        return $ret;
    }

    /**
     * @param Acl $class_name
     *
     * @return array
     */
    static protected function deNormalizeAcl($class_name) {
        preg_match('|^([A-Za-z\\\\]+)Acl\\\\Acl([A-Za-z]+)$|Umsi', $class_name, $matches);
        $vendor = trim(Arr::get($matches, 1, ''), '\\');
        $entity = trim(Arr::get($matches, 2, ''), '\\');
        $ret    = [
            'vendor' => str_replace('\\', '', \Str::snake($vendor, '-')),
            'entity' => \Str::snake($entity),
        ];

        return $ret;
    }

    /**
     * @param LaraForm $class_name
     *
     * @return array
     */
    static protected function deNormalizeForm($class_name) {
        preg_match('|^([A-Za-z\\\\]+)Form\\\\Form([A-Za-z]+)$|Umsi', $class_name, $matches);
        $vendor = trim(Arr::get($matches, 1, ''), '\\');
        $entity = trim(Arr::get($matches, 2, ''), '\\');
        $ret    = [
            'vendor' => str_replace('\\', '', \Str::snake($vendor, '-')),
            'entity' => \Str::snake($entity),
        ];

        return $ret;
    }


    /**
     * @param LaraForm $class_name
     *
     * @return array
     */
    static protected function deNormalizeFormFilter($class_name) {
        preg_match('|^([A-Za-z\\\\]+)FormFilter\\\\FormFilter([A-Za-z]+)$|Umsi', $class_name, $matches);

        $vendor = trim(Arr::get($matches, 1, ''), '\\');
        $entity = trim(Arr::get($matches, 2, ''), '\\');

        $reflection = new \ReflectionClass($class_name);
        $extends    = $reflection->getParentClass();
        if ($extends->getName() != FormFilter::class) {
            $parent_entity        = self::getEntity($extends->getName());
            $parent_studly_entity = \Str::camel($parent_entity);
            $section              = mb_substr($entity, mb_strlen($parent_studly_entity));
            $entity               = $parent_entity;
        } else {
            $section = '';
        }

        $ret = [
            'vendor'  => str_replace('\\', '', \Str::snake($vendor, '-')),
            'entity'  => \Str::snake($entity),
            'section' => \Str::snake($section)
        ];

        return $ret;
    }


    /**
     * @param \Controller $class_name
     *
     * @return array
     */
    static protected function deNormalizeController($class_name) {
        preg_match('|^([A-Za-z\\\\]+)Controller\\\\([A-Za-z\\\\]+)Controller([A-Za-z]+)$|Umsi', $class_name, $matches);

        $vendor  = trim(Arr::get($matches, 1, ''), '\\');
        $section = trim(Arr::get($matches, 2, ''), '\\');
        $entity  = mb_substr(Arr::get($matches, 3), mb_strlen($section));

        if (method_exists($class_name, 'getEntitySuffix')) {
            $suffix = \Str::studly($class_name::getEntitySuffix());
            if ($suffix) {
                $entity = mb_substr($entity, 0, 0 - mb_strlen($suffix));
            }
        }

        return [
            'vendor'       => str_replace('\\', '', \Str::snake($vendor, '-')),
            'entity'       => \Str::snake($entity),
            'section'      => \Str::snake($section),
            'route_prefix' => \Str::snake($section)
        ];
    }


    static function vendorToStudly($vendor) {
        return \Str::studly(str_replace('-', '\\-', $vendor));
    }


    /**
     * Получить Модель по классу
     *
     * @param mixed $vendor - имя класса или вендор
     * @param       $entity
     * @param       $section
     *
     * @return string
     * @throws \Exception
     */
    static function getModelClass() {
        $object = self::getArgs(func_get_args());
        $entity = \Str::studly(self::getEntity($object));
        $vendor = self::vendorToStudly(self::getVendor($object));

        return $vendor . '\Model\\' . $entity;
    }

    static function getAccessorClass() {
        $object  = self::getArgs(func_get_args());
        $entity  = \Str::studly(self::getEntity($object));
        $vendor  = self::vendorToStudly(self::getVendor($object));
        $section = \Str::studly(self::getSection($object));

        return $vendor . '\Accessor\\Accessor' . $entity . $section;
    }


    static function getAclClass() {
        $object = self::getArgs(func_get_args());
        $entity = \Str::studly(self::getEntity($object));
        $vendor = self::vendorToStudly(self::getVendor($object));

        return $vendor . '\Acl\Acl' . $entity;
    }

    static function getDataValidatorClass() {
        $object = self::getArgs(func_get_args());
        $entity = \Str::studly(self::getEntity($object));
        $vendor = self::vendorToStudly(self::getVendor($object));
        $class  = $vendor . '\DataValidator\DataValidator' . $entity;
        if (!class_exists($class)) {
            $class = '\Larakit\Base\DataValidator';
        }

        return $class;
    }

    static function getFormClass() {
        $object = self::getArgs(func_get_args());
        $entity = \Str::studly(self::getEntity($object));
        $vendor = self::vendorToStudly(self::getVendor($object));

        return $vendor . '\Form\Form' . $entity;
    }


    static function getFormFilterClass() {
        $object  = self::getArgs(func_get_args());
        $entity  = \Str::studly(self::getEntity($object));
        $vendor  = self::vendorToStudly(self::getVendor($object));
        $section = \Str::studly(self::getSection($object));

        return $vendor . '\FormFilter\\FormFilter' . $entity . $section;
    }

    static function getArgs($arguments) {
        if (count($arguments) == 1) {
            return array_shift($arguments);
        }

        $vendor  = Arr::get($arguments, 0);
        $entity  = Arr::get($arguments, 1);
        $section = Arr::get($arguments, 2);

        return compact('vendor', 'entity', 'section');
    }

}