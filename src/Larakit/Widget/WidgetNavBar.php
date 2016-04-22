<?php
/**
 * Created by PhpStorm.
 * User: groove
 * Date: 07.03.15
 * Time: 8:41
 */

namespace Larakit\Widget;


use Illuminate\Support\Arr;
use Larakit\Base\Map;
use Larakit\Route\Route;
use Larakit\Base\Widget;

class WidgetNavBar extends Widget {

    /**
     * @param $value
     *
     * @return $this
     */
    function setBrand($value) {
        return $this->_(__FUNCTION__,
            $value);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setUrl($value) {
        return $this->_(__FUNCTION__,
            $value);
    }

    /**
     * @var Map
     */
    protected $map;

    function __construct($name) {
        $this->map = Map::instance(get_called_class() . $name);
    }

    /**
     * @param       $access_name
     * @param       $text
     * @param       $url
     * @param array $attrs
     *
     * @return $this
     */
    function addItem($as, $key, $attrs = []) {
        if (!Route::isEnable($as)) {
            return $this;
        }
        if (is_array($as)) {
            $text = Arr::get($as, 'title');
            $icon = Arr::get($as, 'icon');
            $url  = Arr::get($as, 'url', '#');
            $as   = Arr::get($as,
                'as');
        }
        else {
            $text = Route::get_title($as);
            $icon = Route::get_icon($as);
            $url  = Route::get_url($as);
        }
        if ($icon) {
            $text = \HtmlI::setTitle($text)->addClass($icon) . ' &nbsp;' . \HtmlSpan::setContent($text);
        }
        $this->map->add($key,
            $as,
            $text,
            $url,
            $attrs);
        return $this;
    }

    function __toString() {
        $this->values['items']               = $this->map->getItems();
        $this->values['current_access_name'] = $this->map->getCurrent();
        return parent::__toString();
    }

    function tpl() {
        return 'larakit::!.widgets.nav_bar';
    }


}