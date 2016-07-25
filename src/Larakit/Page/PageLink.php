<?php
/**
 * Created by PhpStorm.
 * User: berdnikov
 * Date: 27.04.16
 * Time: 20:06
 */

namespace Larakit\Page;

use Larakit\Html\Link;

class PageLink {
    static $links      = [];

    /**
     * @return Link
     */
    static function &add($name=null){
        if(!$name){
            $name               = uniqid(microtime(true), true);
        }
        self::$links[$name] = \HtmlLink::setContent('');
        return self::$links[$name];
    }
}