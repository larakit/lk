<?php
/**
 * Created by PhpStorm.
 * User: berdnikov
 * Date: 27.04.16
 * Time: 20:03
 */

namespace Larakit\Page;

class PageMeta {

    static protected $meta_plain      = [
        'charset' => 'utf-8',
    ];
    static protected $meta_http_equiv = [
        'X-UA-Compatible' => 'IE=edge,chrome=1',
    ];
    static protected $meta_name       = [
        'viewport'  => 'width=device-width, initial-scale=1.0',
        'generator' => 'Larakit (https://github.com/larakit)',
    ];
    static protected $meta_property   = [
        'og:locale' => 'ru_RU',
    ];

    static function meta_plain($k, $v) {
        self::$meta_plain[$k] = $v;
    }

    static function meta_property($k, $v) {
        self::$meta_property[$k] = $v;
    }

    static function meta_name($k, $v) {
        self::$meta_name[$k] = $v;
    }

    static function meta_http_equiv($k, $v) {
        self::$meta_http_equiv[$k] = $v;
    }

    static function toArray() {
        $ret = [];
        foreach(\Larakit\Page\PageMeta::$meta_plain as $k => $v) {
            $ret[] = (string) \HtmlMeta::setAttribute($k, $v);
        }
        $ret[] = '';
        foreach(\Larakit\Page\PageMeta::$meta_name as $k => $v) {
            $ret[] = (string) \HtmlMeta::setAttribute('name', $k)->setAttribute('content', $v);
        }
        $ret[] = '';
        foreach(\Larakit\Page\PageMeta::$meta_property as $k => $v) {
            $ret[] = (string) \HtmlMeta::setAttribute('property', $k)->setAttribute('content', $v);
        }
        $ret[] = '';
        foreach(\Larakit\Page\PageMeta::$meta_http_equiv as $k => $v) {
            $ret[] = (string) \HtmlMeta::setAttribute('http-equiv', $k)->setAttribute('content', $v);
        }

        return '        ' . implode(PHP_EOL . '        ', $ret);
    }

    /**
     * Кодировка
     * Чтобы сообщить браузеру, в какой кодировке находятся символы веб-страницы
     *
     * @param $v
     */
    static function charset($v) {
        self::meta_plain('charset', $v);
    }

    /**
     * SEO: description
     * Большинство поисковых серверов отображают содержимое поля description (пример 1) при выводе результатов поиска. Если этого тега нет на странице, то поисковый
     * движок просто перечислит первые встречающиеся слова на странице, которые, как правило, оказываются не очень-то и в тему.
     *
     * @param $v
     */
    static function description($v) {
        self::meta_name('description', $v);
    }

    /**
     * Тег позволяет управлять частотой индексации документа в поисковой системе. Для переиндексации сайта раз в две недели используется тег следующего вида:
     *
     * @param $v
     */
    static function revisit($v) {
        self::meta_name('revisit', $v);
    }

    /**
     * Тег формирует информацию о гипертекстовых документах, которая поступает к роботам поисковых систем. Значения тега могут быть следующими: Index (страница должна
     * быть проиндексирована), Noindex (документ не индексируется), Follow (гиперссылки на странице отслеживаются), Nofollow (гиперссылки не прослеживаются), All
     * (включает значения index и follow, включен по умолчанию), None (включает значения noindex и nofollow).
     *
     * @param $v
     */
    static function robots($v) {
        self::meta_name('robots', $v);
    }

    /**
     * Пример 1. Разрешить индексирование страницы и использование размещённых на ней ссылок для последующей индексации.
     */
    static function robotsAll() {
        self::robots('all');
    }

    /**
     * Пример 2. Запретить индексирование страницы, разрешить использование размещённых на ней ссылок для последующей индексации.
     */
    static function robotsNoindexFollow() {
        self::robots('noindex,follow');
    }

    /**
     * Пример 3. Разрешить индексирование страницы, запретить использование размещённых на ней ссылок для последующей индексации.
     */
    static function robotsIndexNofollow() {
        self::robots('index,nofollow');
    }

    /**
     * Пример 4. Запретить индексирование страницы и использование размещённых на ней ссылок для последующей индексации.
     */
    static function robotsNoindexNofollow() {
        self::robots('none');
    }

    /**
     * SEO: keywords
     * Этот метатег был предназначен для описания ключевых слов, встречающихся на странице (пример 2). Но в результате действия людей, желающих попасть в верхние строчки
     * поисковых систем любыми средствами, теперь дискредитирован. Поэтому многие поисковики пропускают этот параметр.
     *
     * @param $v
     */
    static function keywords($v) {
        self::meta_name('keywords', str_replace(',', ' ', $v));
    }

    /**
     * Идентификатор Facebook-application
     *
     * @param $v
     */
    static function fbAppId($v) {
        self::meta_property('fb:app_id', $v);
    }

    /**
     * If you have a fan page on Facebook and you want to get more data in Facebook Insights, then you have to use this tag. It tells Facebook you are the site owner,
     * and it connects your Facebook fan page to your website.
     *
     * @param $v
     */
    static function fbAdmins($v) {
        self::meta_property('fb:admins', $v);
    }

    /**
     * тег, указывающий на тип добавляемого материала, например, «article» – статья, «movie» – кино и т.д.;
     *
     * @param $v
     */
    static function ogType($v) {
        self::meta_property('og:type', $v);
    }

    static function ogUpdatedTime($v) {
        self::meta_property('og:updated_time', $v);
    }

    /**
     * ссылка на саму веб-страницу, которая добавляется в социальную сеть.
     *
     * @param $v
     */
    static function ogUrl($v) {
        self::meta_property('og:url', $v);
    }

    /**
     * ссылка на картинку, которая должна сопровождать материал;
     *
     * @param $v
     */
    static function ogImage($v) {
        self::meta_property('og:image', $v);
    }

    /**
     * Image dimensions.
     *
     * @param $v
     */
    static function ogImageWidth($v) {
        self::meta_property('og:image:width', $v);
    }

    /**
     * Image dimensions.
     *
     * @param $v
     */
    static function ogImageHeight($v) {
        self::meta_property('og:image:height', $v);
    }

    /**
     * тег, отвечающий за название материала;
     *
     * @param $v
     */
    static function ogTitle($v) {
        self::meta_property('og:title', $v);
    }

    /**
     * Same as "og:title".
     *
     * @param $v
     */
    static function googleName($v) {
        self::meta_property('name', $v);
    }

    /**
     *  From the user (post author) Google+ profile URL.
     *
     * @param $v
     */
    static function googleAuthor($v) {
        self::meta_property('author', $v);
    }

    /**
     * Same as "og:description".
     *
     * @param $v
     */
    static function googleDescription($v) {
        self::meta_property('description', $v);
    }

    static function copyright($v) {
        self::meta_name('copyright', $v);
    }

    static function generator($v) {
        self::meta_name('generator', $v);
    }

    static function author($v) {
        self::meta_name('author', $v);
    }

    /**
     *  Same as "og:image".
     *
     * @param $v
     */
    static function googleImage($v) {
        self::meta_property('image', $v);
    }

    /**
     * тег, содержащий описание материала (причём в отличие от традиционного мета-тега description стандарт
     * Open Graph позволяет увеличить размер описания со 160 до 295 знаков);
     *
     * @param $v
     */
    static function ogDescription($v) {
        self::meta_property('og:description', $v);
    }

    /**
     * The card type
     *
     * @param $v
     */
    static function twitterCard($v) {
        self::meta_property('twitter:card', $v);
    }

    /**
     * @username of website. Either twitter:site or twitter:site:id is required.
     *
     * @param $v
     */
    static function twitterSite($v) {
        self::meta_property('twitter:site', $v);
    }

    /**
     * Same as twitter:site, but the user’s Twitter ID. Either twitter:site or twitter:site:id is required.
     *
     * @param $v
     */
    static function twitterSiteId($v) {
        self::meta_property('twitter:site:id', $v);
    }

    /**
     * @username of content creator
     *
     * @param $v
     */
    static function twitterCreator($v) {
        self::meta_property('twitter:creator', $v);
    }

    /**
     * Twitter user ID of content creator
     *
     * @param $v
     */
    static function twitterCreatorId($v) {
        self::meta_property('twitter:creator:id', $v);
    }

    /**
     * Description of content (maximum 200 characters)
     *
     * @param $v
     */
    static function twitterDescription($v) {
        self::meta_property('twitter:description', $v);
    }

    /**
     * Title of content (max 70 characters)
     *
     * @param $v
     */
    static function twitterTitle($v) {
        self::meta_property('twitter:title', $v);
    }

    /**
     * URL of image to use in the card. Image must be less than 1MB in size
     *
     * @param $v
     */
    static function twitterImage($v) {
        self::meta_property('twitter:image', $v);
    }

    /**
     * A text description of the image conveying the essential nature of an image to users who are visually impaired
     *
     * @param $v
     */
    static function twitterImageAlt($v) {
        self::meta_property('twitter:image:alt', $v);
    }

    /**
     * HTTPS URL of player iframe
     *
     * @param $v
     */
    static function twitterPlayer($v) {
        self::meta_property('twitter:player', $v);
    }

    /**
     * Width of iframe in pixels
     *
     * @param $v
     */
    static function twitterPlayerWidth($v) {
        self::meta_property('twitter:player:width', $v);
    }

    /**
     * Height of iframe in pixels
     *
     * @param $v
     */
    static function twitterPlayerHeight($v) {
        self::meta_property('twitter:player:height', $v);
    }

    /**
     * URL to raw video or audio stream
     *
     * @param $v
     */
    static function twitterPlayerStream($v) {
        self::meta_property('twitter:player:stream', $v);
    }

    /**
     * Name of your iPhone app
     *
     * @param $v
     */
    static function twitterAppNameIphone($v) {
        self::meta_property('twitter:app:name:iphone', $v);
    }

    /**
     * Your app ID in the iTunes App Store (Note: NOT your bundle ID)
     *
     * @param $v
     */
    static function twitterAppIdIphone($v) {
        self::meta_property('twitter:app:id:iphone', $v);
    }

    /**
     * Your app’s custom URL scheme (you must include “://” after your scheme name)
     *
     * @param $v
     */
    static function twitterAppUrlIphone($v) {
        self::meta_property('twitter:app:url:iphone', $v);
    }

    /**
     * Name of your iPad optimized app
     *
     * @param $v
     */
    static function twitterAppNameIpad($v) {
        self::meta_property('twitter:app:name:ipad', $v);
    }

    /**
     * Your app ID in the iTunes App Store
     *
     * @param $v
     */
    static function twitterAppIdIpad($v) {
        self::meta_property('twitter:app:id:ipad', $v);
    }

    /**
     * Your app’s custom URL scheme
     *
     * @param $v
     */
    static function twitterAppUrlIpad($v) {
        self::meta_property('twitter:app:url:ipad', $v);
    }

    /**
     * Name of your Android app
     *
     * @param $v
     */
    static function twitterAppNameGoogleplay($v) {
        self::meta_property('twitter:app:name:googleplay', $v);
    }

    /**
     * Your app ID in the Google Play Store
     *
     * @param $v
     */
    static function twitterAppIdGoogleplay($v) {
        self::meta_property('twitter:app:id:googleplay', $v);
    }

    /**
     * Your app’s custom URL scheme
     *
     * @param $v
     */
    static function twitterAppUrlGoogleplay($v) {
        self::meta_property('twitter:app:url:googleplay', $v);
    }

    /**
     *  Article published time (for posts only)
     *
     * @param $v
     */
    static function articlePublishedTime($v) {
        self::meta_property('article:published_time', $v);
    }

    /**
     *  Article modified time (for posts only)
     *
     * @param $v
     */
    static function articleModifiedTime($v) {
        self::meta_property('article:modified_time', $v);
    }

    /**
     *  From settings on the options screen.
     *
     * @param $v
     */
    static function articlePublisher($v) {
        self::meta_property('article:publisher', $v);
    }

    /**
     * From post categories.
     *
     * @param $v
     */
    static function articleSection($v) {
        self::meta_property('article:section', $v);
    }

    /**
     * From the user (post author) Faceboook Profile URL.
     *
     * @param $v
     */
    static function articleAuthor($v) {
        self::meta_property('article:author', $v);
    }

    /**
     * Автозагрузка страниц
     * Этот метатег позволяет создавать перенаправление (редирект) на другой сайт. Если URL не указан, произойдет автоматическое обновление текущей страницы через
     * количество секунд, заданных в атрибуте content.
     *
     * @param      $delay
     * @param null $url
     */
    static function refresh($delay, $url = null) {
        $v = $delay;
        if($url) {
            $v .= '; URL=' . $url;
        }
        self::meta_http_equiv('refresh', $v);
    }
}

PageMeta::fbAdmins('123');