<?php

namespace Larakit\Page;

/**
 * Доменный шардинг
 * Class PageDnsPrefetch
 * @package Larakit\Page
 */
class PageDnsPrefetch {
    static protected $domains = [];

    /**
     * Регистрация нового домена, на котором могут находиться ресурсы вашей страницы,
     * который необходимо резолвить сразу.
     * На очень медленном интернете это может сэкономить до 0.2 секунд на каждый запрос.
     *
     * @param $domain string
     */
    static function register($domain) {
        self::$domains[$domain] = $domain;
    }

    static function domains(){
        return self::$domains;
    }

}