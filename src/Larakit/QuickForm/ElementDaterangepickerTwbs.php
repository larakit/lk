<?php
namespace Larakit\QuickForm;
class ElementDaterangepickerTwbs extends ElementGroupTwbs {

    public function getType() {
        return 'daterangepicker_twbs';
    }

    /**
     * @param null $name
     *
     * @return ElementTextTwbs
     */
    static function laraform($name = null) {
        $el = new ElementDaterangepickerTwbs($name);
        $el->setWrapClass('js-daterangepicker-twbs');
        $el->putTextTwbs('from')->setAttribute('style','display:none');
        $el->putTextTwbs('to')->setAttribute('style','display:none');
        $el->putStaticTwbs('<div class="col-lg-12"><i class="fa fa-calendar"></i>&nbsp;<span></span> <b class="caret"></b></div>');
        //$el->putStaticTwbs('<i class="fa fa-calendar"></i>&nbsp;<span></span> <b class="caret"></b>');
        return $el;
    }

}
