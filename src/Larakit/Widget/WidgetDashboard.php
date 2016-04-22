<?php
namespace Larakit\Widget;

use Larakit\Event;

class WidgetDashboard extends \Larakit\Base\Widget {
    protected $items  = [];
    protected $in_row = 2;
    protected $name   = '';

    function __construct($name = null) {
        $this->name = $name;
    }

    /**
     * @param     $item
     * @param int $priority
     *
     * @return $this
     */
    function addItem($item, $priority = 0) {
        $this->items[$priority][] = $item;
        return $this;
    }

    function set3Row() {
        $this->in_row = 3;
        return $this;
    }

    function toHtml() {
        Event::notify('dashboard_' . $this->name);
        $items = [];
        krsort($this->items);
        foreach ($this->items as $_items) {
            foreach ($_items as $item) {
                $items[] = $item;
            }

        }

        $this->set('items', $items);
        $this->set('in_row', $this->in_row);
        return \View::make(
            $this->tpl(), $this->values
        )
                    ->__toString();

    }


    function tpl() {
        return 'larakit::!.widgets.dashboard';
    }


}