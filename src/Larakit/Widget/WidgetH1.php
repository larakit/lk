<?php
namespace Larakit\Widget;

class WidgetH1 extends \Larakit\Base\Widget {
    /**
     * @param $value
     *
     * @return $this
     */
    function setH1($value) {
        return $this->_(
            __FUNCTION__, $value
        );
    }

    function setH1Ext($value) {
        return $this->_(
            __FUNCTION__, $value
        );
    }

    function tpl() {
        return 'larakit::!.widgets.h1';
    }


}