<?php
namespace Larakit\QuickForm;
class ElementAlteBoxFooter extends ElementGroupTwbs {

    public function getType() {
        return 'alte_box_footer';
    }

    /**
     * @param null $name
     *
     * @return ElementAlteBoxFooter
     */
    static function laraform($name = null) {
        $el = new ElementAlteBoxFooter(null);
        $el->addClass('box-footer');
        return $el;
    }

}