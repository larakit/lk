<?php
/**
 * Created by PhpStorm.
 * User: berdnikov_ay
 * Date: 16.03.2015
 * Time: 14:51
 */

namespace Larakit\Widget;


class WidgetAlert extends \Larakit\Base\Widget {

    function setTitle($val) {
        return $this->_(__FUNCTION__, $val);
    }

    function setContent($val) {
        return $this->_(__FUNCTION__, $val);
    }

    function asDanger() {
        return $this->set('class', 'danger');
    }

    function asSuccess() {
        return $this->set('class', 'success');
    }

    function asInfo() {
        return $this->set('class', 'info');
    }

    function asWarning() {
        return $this->set('class', 'warning');
    }

    function tpl() {
        return 'larakit::!.widgets.alert';
    }


}