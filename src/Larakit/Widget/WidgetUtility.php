<?php

namespace Larakit\Widget;

use Larakit\Base\Widget;

class WidgetUtility extends Widget {
    protected $icons = [];

    /**
     * @param $code
     * @param $icon
     *
     * @return \HtmlI
     */
    function addIcon($code) {
        $this->icons[$code] = \HtmlI::addClass('js-utility fa pointer');
        return $this->icons[$code];
    }

    function tpl() {
        return 'larakit::!.widgets.utility';
    }

    function toHtml() {
//        \LaraCss::add('/packages/larakit/css/utility.css');
        return \View::make($this->tpl(), ['icons' => $this->icons])
                    ->__toString();

    }


}