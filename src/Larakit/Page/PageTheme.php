<?php
namespace Larakit\Page;

class PageTheme {

    /**
     * Сформировать имя класса темы оформления
     *
     * @param null $theme
     *
     * @return null|string
     */
    static function getClassTheme($theme = null) {
        if(!$theme) {
            return null;
        }

        return 'theme--' . $theme;
    }

    static protected $current = null;
    static protected $themes  = [];

    /**
     * Получение списка тем оформления
     * @return array
     */
    static function getThemes() {
        return self::$themes;
    }

    /**
     * Назначить список тем оформления
     *
     * @param $themes
     */
    static function setThemes($themes) {
        self::$themes = (array) $themes;
    }

    /**
     * Получить текущую тему оформления
     * @return null|string
     */
    static function getCurrent() {
        return self::$current;
    }

    /**
     * Назначить текущую тему оформления
     *
     * @param null $theme
     */
    static function setCurrent($theme = null) {
        //удалить все невостребованные классы тем оформления
        foreach(self::getThemes() as $t) {
            \LaraPage::body()->removeClass(self::getClassTheme($t));
        }
        //добавить класс текущей темы оформления
        \LaraPage::body()->addClass(self::getClassTheme($theme));
        self::$current = $theme;
        \Twig::addGlobal('current_theme', $theme);
    }
}
