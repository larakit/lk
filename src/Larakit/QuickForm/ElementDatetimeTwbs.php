<?php
namespace Larakit\QuickForm;

use Carbon\Carbon;

class ElementDatetimeTwbs extends ElementDatetimepickerTwbs {

    public function getType() {
        return 'datetime_twbs';
    }

    /**
     * @param        $name
     *
     * @return ElementDateTwbs
     */
    static function laraform($name) {
        $el = new ElementDatetimeTwbs($name);
        $el->addClass('form-control js-datepicker-twbs');

        return $el->setFormat('DD.MM.YYYY HH:mm:ss');
    }

    function setValue($value) {
        $value = Carbon::parse($value)->format('d.m.Y H:i:s');

        return parent::setValue($value);
    }

}