<?php

namespace Larakit\Widget;

use Larakit\Base\Widget;

class WidgetOverlay extends Widget {
    function tpl() {
        return 'larakit::!.widgets.overlay';
    }

    function setText($text) {
        return $this->set('text', $text);
    }
    function setIcon($text) {
        return $this->set('icon', $text);
    }
}