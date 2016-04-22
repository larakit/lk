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

class WidgetBox extends Widget {

    /**
     * @param $value
     *
     * @return WidgetSmallBox
     */
    function setClass($value) {
        return $this->_(__FUNCTION__,
            (string)$value);
    }

    /**
     * @param $value
     *
     * @return WidgetSmallBox
     */
    function setTitle($value) {
        return $this->_(__FUNCTION__,
            (string)$value);
    }

    function setTools($value) {
        return $this->_(__FUNCTION__,
            (string)$value);
    }

    function setCollapse($value=1) {
        return $this->_(__FUNCTION__,
            (string)$value);
    }

    /**
     * @param $value
     *
     * @return WidgetSmallBox
     */
    function setBody($value) {
        return $this->_(__FUNCTION__,
            (string)$value);
    }

    /**
     * @param $value
     *
     * @return WidgetSmallBox
     */
    function setIcon($value = 'fa fa-lock') {
        return $this->_(__FUNCTION__,
            (string)$value);
    }

    function setFooterUrl($value) {
        return $this->_(__FUNCTION__,
            (string)$value);
    }

    function setFooterText($value) {
        return $this->_(__FUNCTION__,
            (string)$value);
    }

    function tpl() {
        return 'larakit::!.widgets.box';
    }


}