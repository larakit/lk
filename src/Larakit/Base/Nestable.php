<?php

namespace Larakit\Base;

use Illuminate\Support\Arr;
use Larakit\Base\Model;
use Larakit\Base\TraitModelNestable;
use Larakit\Exception;
use Larakit\Route\RouteNestable;

/**
 * Class Nestable
 * @package Larakit\Base
 */
Class Nestable {
    /**
     * Список страниц которые есть в модели
     * @var array
     */
    static protected $pages = [];
    /** @var Model */
    static protected $model_class;

    /*
     * Плоский список страниц по ID
     */
    static protected $pages_by_id = [];
    /**
     * Плоский список страниц по пути
     *
     * [
     *      ['calc_path' => 'id'],
     * ]
     *
     * @var array
     */
    static protected $pages_by_path = [];
    /**
     * Плоский список страниц по SLUG
     *
     * [
     *      ['calc_slug' => 'id'],
     * ]
     *
     * @var array
     */
    static protected $pages_by_slug = [];
    /**
     * Плоский список страниц по SLUG
     *
     * [
     *      ['parent_id' => ['id', 'id']],
     * ]
     *
     * @var array
     */
    static protected $pages_by_parent_id = [];


    /**
     * Идентификатор текущей страницы
     *
     * @var int
     */
    static protected $current_id = 0;
    /**
     * SLUG текущей страницы
     * @var string
     */
    static protected $current_slug = '';


    /**
     * Задать текущую страницу (можно передать slug страницы или ID)
     *
     * @param $slug_or_id
     */
    static function setCurrent($slug_or_id) {
        if (is_integer($slug_or_id)) {
            self::$current_id = $slug_or_id;
        } else {
            self::$current_slug = trim($slug_or_id, '/');
        }
    }

    /**
     * ОБЯЗАТЕЛЬНАЯ ФУНКЦИЯ, задать модель от которой будет выстраиваться список страниц
     *
     * @param $model
     *
     * @throws Exception
     */
    static function setModelClass($model) {
        if (!in_array(TraitModelNestable::class, class_uses($model))) {
            $e = new Exception('Модель должена наследоваться от TraitModelNestable');
            throw $e->setTitle('Ошибка назначения модели');
        }
        self::$model_class = $model;
    }

    /**
     * Инициализация класса
     *
     * @throws Exception
     */
    static protected function init() {
        if (self::$pages) {
            return;
        }
        if (!self::$model_class) {
            throw new Exception('Необходимо задать модель');
        }
        $class_name  = self::$model_class;
        self::$pages = $class_name::orderBy('calc_path', 'asc')
                                  ->get();
        self::initFlatArray();
    }

    /**
     * Предустановка
     */
    static protected function initFlatArray() {
        foreach (self::$pages as $page) {
            self::$pages_by_id[$page->id]                 = $page;
            self::$pages_by_path[$page->calc_path]        = $page->id;
            self::$pages_by_slug[$page->calc_slug]        = $page->id;
            self::$pages_by_parent_id[$page->parent_id][] = $page->id;
        }
        if (self::$current_id || self::$current_slug) {
            if (self::$current_slug) {
                self::$current_id = self::getPageBySlug(self::$current_slug);
            }
            if (self::$current_id) {
                if (!isset(self::$pages_by_id[self::$current_id])) {
                    self::$current_id = 0;
                } else {
                    self::$current_slug = self::getPageSlug(self::$current_id);
                }
            }

        }
    }

    /**
     * Получить хлебные крошки
     *
     * @param bool $with_current - добавлять текущую страницу в крошки
     *
     * @return array
     * @throws Exception
     */
    static function getBreadCrumbs($with_current = true) {
        self::init();
        $parents = self::getParentsIds(self::getCurrentId());
        if ($with_current) {
            $parents[] = self::getCurrentId();
        }

        if ($parents) {
            $breadcrumbs = [];
            foreach ($parents as $child) {
                $breadcrumbs[$child] = [
                    'page'       => self::getPageById($child),
                    'slug'       => self::getPageSlug($child),
                    'full_url'   => self::getPageFullUrl($child),
                    'is_current' => (self::getCurrentId() == $child),
                ];
            }
            return $breadcrumbs;
        }
        return [];
    }

    /**
     * Получить список идентификаторов родительских страниц
     *
     * @param null $page_id
     *
     * @return array
     * @throws Exception
     */
    static function getParentsIds($page_id = null) {
        self::init();
        if (!$page_id) {
            return [];
        }
        $page      = self::getPageById($page_id);
        $calc_path = explode('.', $page->calc_path);
        $ids       = [];
        foreach ($calc_path as $path) {
            $len       = mb_strlen($path);
            $parent_id = (int)mb_substr($path, 0, $len / 2);
            if ($parent_id) {
                $ids[] = $parent_id;
            }
        }
        return $ids;
    }


    /**
     * Получить текущую страницу
     *
     * @return Model|null
     * @throws Exception
     */
    static function getCurrentPage() {
        self::init();
        return self::getPageById(self::getCurrentId());
    }

    /**
     * Получить идентификатор страницы по SLUG
     *
     * @param $slug
     *
     * @return int|null
     * @throws Exception
     */
    static function getPageBySlug($slug) {
        self::init();
        return Arr::get(self::$pages_by_slug, $slug);
    }

    /**
     * Получить SLUG страницы по ID
     *
     * @param $id
     *
     * @return string|null
     * @throws Exception
     */
    static function getPageSlug($id) {
        self::init();
        $page = self::getPageById($id);
        if ($page) {
            return $page->calc_slug;
        }
        return null;
    }

    /**
     * Получить SLUG страницы по ID
     *
     * @param $id
     *
     * @return string|null
     * @throws Exception
     */
    static function getPageFullUrl($id) {
        self::init();
        $model = self::$model_class;
        return RouteNestable::getUrl($model::getVendor(), $model::getEntity(), $id);
    }

    /**
     * Получить список идентификаторов дочерних страниц
     *
     * @param int $parent_id
     *
     * @return mixed
     * @throws Exception
     */
    static function getChildren($parent_id = 0) {
        self::init();
        return Arr::get(self::$pages_by_parent_id, (int)$parent_id, []);
    }

    /**
     * Получить текущий идентификатор страницы
     *
     * @return int
     * @throws Exception
     */
    static function getCurrentId() {
        self::init();
        return self::$current_id;
    }

    /**
     * Получить страницу по идентификатору
     *
     * @param $id
     *
     * @return Model|null
     * @throws Exception
     */
    static function getPageById($id) {
        self::init();
        return Arr::get(self::$pages_by_id, $id);
    }

    /**
     * Получить список страниц для меню
     *
     * @param int $depth - максимальная вложенность
     * @param int $parent_id - ID родителя
     * @param int $current_depth
     *
     * @return array
     * @throws Exception
     */
    static function getMenu($depth = 0, $parent_id = 0, $current_depth = 0) {
        self::init();

        $menu = [];
        if ($depth) {
            if ($current_depth >= $depth) {
                return [];
            }
        }
        $children = self::getChildren($parent_id);
        if ($children) {
            foreach ($children as $child) {
                $menu[$child] = [
                    'page'       => self::getPageById($child),
                    'slug'       => self::getPageSlug($child),
                    'full_url'   => self::getPageFullUrl($child),
                    'is_current' => (self::isCurrentNestable($child)),
                    'level'      => $current_depth,
                    'children'   => self::getMenu($depth, $child, ($current_depth + 1))
                ];
            }
        }
        return $menu;
    }

    /**
     * Является ли страница активной или ее ребенок
     *
     * @param $id
     *
     * @return bool
     * @throws Exception
     */
    static function isCurrentNestable($id) {
        self::init();
        if (self::getCurrentId() == $id) {
            return true;
        }
        return in_array($id, self::getParentsIds(self::getCurrentId()));
    }

}
