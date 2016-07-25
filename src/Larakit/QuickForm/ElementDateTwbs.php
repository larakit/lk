<?php
namespace Larakit\QuickForm;

use Carbon\Carbon;

class ElementDateTwbs extends ElementDatetimepickerTwbs {

    public function getType() {
        return 'date_twbs';
    }

    /**
     * @param        $name
     *
     * @return ElementDateTwbs
     */
    static function laraform($name) {
        $el = new ElementDateTwbs($name);
        $el->addClass('form-control js-datepicker-twbs');
        return $el->setFormat('DD.MM.YYYY');
    }

    function setValue($value) {
        if(''==$value){
            return $this;
        }
        $value = Carbon::parse($value)->format('d.m.Y');
        return parent::setValue($value);
    }

}