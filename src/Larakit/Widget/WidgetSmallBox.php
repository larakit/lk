<?php
/**
 * Created by PhpStorm.
 * User: groove
 * Date: 07.03.15
 * Time: 8:41
 */

namespace Larakit\Widget;


use Larakit\Base\Map;
use Larakit\Base\Widget;

class WidgetSmallBox extends Widget {

    /**
     * @param $value
     *
     * @return WidgetSmallBox
     */
    function setClass($value) {
        return $this->_(
            __FUNCTION__, $value
        );
    }

    /**
     * @param $value
     *
     * @return WidgetSmallBox
     */
    function setDigit($value) {
        return $this->_(
            __FUNCTION__, $value
        );
    }

    /**
     * @param $value
     *
     * @return WidgetSmallBox
     */
    function setDesc($value) {
        return $this->_(
            __FUNCTION__, $value
        );
    }

    /**
     * @param $value
     *
     * @return WidgetSmallBox
     */
    function setIcon($value = 'fa fa-lock') {
        return $this->_(
            __FUNCTION__, $value
        );
    }

    function setFooterUrl($value) {
        return $this->_(
            __FUNCTION__, $value
        );
    }

    function setFooterText($value) {
        return $this->_(
            __FUNCTION__, $value
        );
    }

    function tpl() {
        return 'larakit::!.widgets.small_box';
    }


}