<?php
namespace Larakit\Page;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Larakit\Event\Event;
use Larakit\Html\Base;
use Larakit\Html\Body;
use Larakit\Html\LHtml;
use Larakit\Route\Route;
use Larakit\Widget\WidgetBreadcrumbs;
use Larakit\Widget\WidgetH1;

class Page {

    protected $title;
    protected $breadcrumbs = [];
    /**
     * @var Body
     */
    protected $body;
    /**
     * @var LHtml
     */
    protected $html;

    /**
     * @var Base
     */
    protected $base;

    protected $favicon      = '/favicon.ico';
    protected $dns_prefetch = [];
    protected $apple_icons  = [];
    protected $image;
    protected $charset      = 'utf-8';

    function __construct() {
        $this->body = new Body();
        $this->html = new LHtml();
        $this->base = new Base();
    }

    /**
     * Добавим собранный layout
     *
     * @param $content
     */
    function setContent($content) {
        $this->body->setContent($content);

        return $this;
    }

    function body() {
        return $this->body;
    }

    function html() {
        return $this->html;
    }

    /**
     * Установим базовый href страницы
     *
     * @param $href
     *
     * @return $this
     */
    function setBaseHref($href) {
        $this->base->setHref($href);

        return $this;
    }

    /**
     * Установим базовый target страницы
     *
     * @param $target
     *
     * @return $this
     */
    function setBaseTarget($target) {
        $this->base->setTarget($target);

        return $this;
    }

    function getBreadCrumbs() {
        return $this->breadcrumbs;
    }

    /**
     * @param       $url
     * @param array $replacements
     *
     * @return $this
     */
    function addBreadCrumb($route_name, $params = [], $replacements = []) {
        $url        = route($route_name, $params, false);
        $title                   = \LaraPage::pageTitle($route_name, $replacements);
        $h1                      = \LaraPage::pageH1($route_name, $replacements);
        $h1_ext                  = \LaraPage::pageH1Ext($route_name, $replacements);
        $description             = \LaraPage::pageH1Ext($route_name, $replacements);
        $icon                    = Route::routeIcons($route_name);
        $this->breadcrumbs[$url] = compact('title', 'icon');
        $this->setUrl($url);
        $_title = [];
        foreach($this->breadcrumbs as $url => $breadcrumb) {
            $_title[] = Arr::get($breadcrumb, 'title');
        }
        $_title = array_reverse($_title);
        $this->setTitle(implode(', ', $_title))->setDescription($description);

        return $this;
    }

    function addDnsPrefetch($url) {
        $url                      = parse_url($url, PHP_URL_HOST);
        $this->dns_prefetch[$url] = $url;

        return $this;
    }

    function getDnsPrefetch() {
        return $this->dns_prefetch;
    }

    /**
     * Указать автора
     *
     * @param $value
     *
     * @return $this
     */
    function setAuthor($value) {
        \LaraPageHead::addMetaName('author', $value);

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setDescription($value) {
        PageMeta::meta_name('description', $value);
        PageMeta::meta_property('og:description', $value);
        PageMeta::meta_name('twitter:description', $value);

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setKeywords($value) {
        PageMeta::meta_name('keywords', $value);
        PageMeta::meta_property('og:keywords', $value);

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setUrl($value) {
        PageMeta::meta_property('og:url', $value);
        PageMeta::meta_name('twitter:keywords', $value);
        PageLink::add(__METHOD__)->setRel('canonical')->setAttribute('href', $value);

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setSitename($value) {
        PageMeta::meta_property('og:site_name', $value);

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setVerificationYandex($value) {
        PageMeta::meta_name('yandex-verification', $value);

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setVerificationGoogle($value) {
        PageMeta::meta_name('google-site-verification', $value);

        return $this;
    }

    function getAppleTouchs() {
        return $this->apple_icons;
    }

    function setAppleTouchIcon76($value) {
        $this->setAppleTouchIcon($value, 76);
    }

    function setAppleTouchIcon($value, $size = null) {
        $this->apple_icons[$size] = $value;

        return $this;
//        return PageLink::add(__METHOD__ . $size)->setRel('apple-touch-icon')->setHref($value);
    }

    function setAppleTouchIcon120($value) {
        $this->setAppleTouchIcon($value, 120);
    }

    function setAppleTouchIcon152($value) {
        $this->setAppleTouchIcon($value, 152);
    }

    /**
     * @return mixed
     */
    function getFavicon() {
        return $this->favicon;
    }

    function setFavicon($value) {
        $this->favicon = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getCharset() {
        return $this->charset;
    }

    /**
     * @param string $charset
     *
     * @return Page;
     */
    public function setCharset($charset) {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * @param mixed $image
     *
     * @return Page;
     */
    public function setImage($image) {
        $this->image = $image;

        return $this;
    }

    protected $x_ua_compatible = 'IE=edge,chrome=1';
    protected $viewport        = 'width=device-width, initial-scale=1.0';
    protected $generator       = 'Larakit (https://github.com/larakit)';

    /**
     * @return string
     */
    public function getGenerator() {
        return $this->generator;
    }

    /**
     * @param string $generator
     *
     * @return Page;
     */
    public function setGenerator($generator) {
        $this->generator = $generator;

        return $this;
    }

    /**
     * @return string
     */
    public function getViewport() {
        return $this->viewport;
    }

    /**
     * @param string $viewport
     *
     * @return Page;
     */
    public function setViewport($viewport) {
        $this->viewport = $viewport;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getXUaCompatible() {
        return $this->x_ua_compatible;
    }

    /**
     * @param mixed $x_ua_compatible
     *
     * @return Page;
     */
    public function setXUaCompatible($x_ua_compatible) {
        $this->x_ua_compatible = $x_ua_compatible;

        return $this;
    }

    static protected $body_appends = '';

    static function bodyAppend($content) {
        self::$body_appends .= $content . PHP_EOL;
    }

    function __toString() {
        try {
            $base            = $this->base;
            $body_appends    = self::$body_appends;
            $layout          = $this->body->getContent();
            $body_attributes = $this->body->getAttributes(true);
            $html_attributes = $this->html->getAttributes(true);
            $meta_tags       = Event::filter('lk-page::meta-tags', []);

            return \View::make('lk-page::page', compact(
                'base',
                'body_attributes',
                'body_appends',
                'html_attributes',
                'layout',
                'meta_tags'
            ))->render();
        }
        catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    public function pageTitleValues() {
        static $values;
        if(!isset($values)) {
            $values = Event::filter('lk-page::title', (array) trans('page.titles'));
        }

        return $values;
    }

    public function pageH1Values() {
        static $values;
        if(!isset($values)) {
            $values = Event::filter('lk-page::h1', (array) trans('page.h1'));
        }

        return $values;
    }

    public function pageH1ExtValues() {
        static $values;
        if(!isset($values)) {
            $values = Event::filter('lk-page::h1_ext', (array) trans('page.h1_ext'));
        }

        return $values;
    }

    public function pageDescriptionValues() {
        static $values;
        if(!isset($values)) {
            $values = Event::filter('lk-page::description', (array) trans('page.description'));
        }

        return $values;
    }

    function applyReplacement($line, $replacements) {
        foreach($replacements as $key => $value) {
            $line = str_replace(
                [':' . Str::upper($key), ':' . Str::ucfirst($key), ':' . $key],
                [Str::upper($value), Str::ucfirst($value), $value],
                $line
            );
        }

        return $line;
    }

    /**
     * TITLE страницы
     *
     * @param null $route
     *
     * @return mixed
     */
    public function pageTitle($route = null, array  $replacements = []) {
        if(!$route) {
            $route = \Route::currentRouteName();
        }

        return $this->applyReplacement(Arr::get(self::pageTitleValues(), $route, 'title'), $replacements);
    }

    /**
     * Заголовок H1, может отличаться от TITLE, но если не задан - берется TITLE
     *
     * @param null $route
     *
     * @return mixed
     */
    public function pageH1($route = null, array $replacements = []) {
        if(!$route) {
            $route = \Route::currentRouteName();
        }
        $h1 = Arr::get(self::pageH1Values(), $route);

        return $this->applyReplacement($h1 ? : self::pageTitle($route), $replacements);
    }

    /**
     * Строка возле заголовка H1
     *
     * @param null $route
     *
     * @return mixed
     */
    public function pageH1Ext($route = null, array $replacements = []) {
        if(!$route) {
            $route = \Route::currentRouteName();
        }

        return $this->applyReplacement(Arr::get(self::pageH1ExtValues(), $route), $replacements);
    }

    /**
     * Описание сраницы
     *
     * @param null  $route
     * @param array $replacements
     *
     * @return mixed
     */
    public function pageDescription($route = null, array $replacements = []) {
        if(!$route) {
            $route = \Route::currentRouteName();
        }

        return $this->applyReplacement(Arr::get(self::pageDescriptionValues(), $route), $replacements);
    }

    /**
     * @param mixed $title
     *
     * @return Page;
     */
    public function setTitle($title) {
        $this->title = $title;
        \LaraPageHead::addMetaProperty('og:title', $title);
        \LaraPageHead::addMetaName('twitter:title', $title);

        return $this;
    }

    public function getTitle() {
        return $this->title;
    }

    protected $og_title;
    protected $og_locale = 'ru_RU';

}
