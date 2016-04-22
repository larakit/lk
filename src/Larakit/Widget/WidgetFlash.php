<?php
namespace Larakit\Widget;

class WidgetFlash extends \Larakit\Base\Widget {
    static function success($msg) {
        \Session::flash('success', $msg);
    }

    static function info($msg) {
        \Session::flash('info', $msg);
    }

    static function warning($msg) {
        \Session::flash('warning', $msg);
    }

    static function danger($msg) {
        \Session::flash('danger', $msg);
    }

    function tpl() {
        return 'larakit::!.widgets.flash';
    }

}