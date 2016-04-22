<?php

namespace Larakit\Widget;

use Larakit\Base\Widget;
use Larakit\User\Me;

class WidgetXpanel extends Widget {
    function tpl() {
        return 'larakit::!.widgets.xpanel';
    }

    function toHtml() {
//        if (!Me::is_admin())
//            return '';
        return \View::make($this->tpl(),
            $this->values)->__toString();

    }


}