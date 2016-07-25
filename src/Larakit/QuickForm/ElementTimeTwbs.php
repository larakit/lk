<?php
namespace Larakit\QuickForm;
use Carbon\Carbon;

class ElementTimeTwbs extends ElementDatetimepickerTwbs{

    public function getType() {
        return 'time_twbs';
    }


    /**
     * @param        $name
     *
     * @return ElementDateTwbs
     */
    static function laraform($name) {
        $el = new ElementTimeTwbs($name);
        $el->addClass('form-control js-datepicker-twbs');

        return $el->setFormat('HH:mm:ss');
    }

    function setValue($value) {
        $value = Carbon::parse($value)->format('H:i:s');

        return parent::setValue($value); 
    }

}