<?php
namespace Larakit\Widget;

use Larakit\Base\Map;

class WidgetMenuTop extends \Larakit\Base\Widget {
    /**
     * @param $access_name
     * @param $text
     * @param $url
     *
     * @return $this
     */
    function addItem($access_name, $text, $url) {
        Map::instance()
           ->add(
               $access_name, $text, $url
           );
        return $this;
    }

    function tpl() {
        return 'larakit::!.widgets.menu_top';
    }


}