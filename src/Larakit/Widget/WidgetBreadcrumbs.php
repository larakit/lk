<?php
namespace Larakit\Widget;

use Illuminate\Support\Arr;
use Larakit\Base\Map;
use Larakit\Webconfig;

class WidgetBreadcrumbs extends Widget {

    static $breadcrumbs = [];

    /**
     * @param $access_name
     * @param $text
     * @param $url
     *
     * @return $this
     */
    function addItem($route_name, $url = '#') {
        self::$breadcrumbs[] = [
            'url'        => $url,
            'route_name' => $route_name,
        ];

        return $this;
    }

    function __toString() {
        $this->values['breadcrumbs'] = self::$breadcrumbs;

        return parent::__toString();
    }

    function getTitle() {
        $ret = [];
        $tmp = self::$breadcrumbs;
        krsort($tmp);
        foreach($tmp as $item) {
            $ret[] = Arr::get($item, 'title');
        }

        return implode(' / ', $ret);
    }

    function tpl() {
        return 'larakit::!.widgets.breadcrumbs';
    }

}