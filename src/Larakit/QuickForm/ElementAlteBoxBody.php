<?php
namespace Larakit\QuickForm;
class ElementAlteBoxBody extends ElementGroupTwbs {


    public function getType() {
        return 'alte_box_body';
    }

    /**
     * @param null $name
     *
     * @return ElementAlteBoxBody
     */
    static function laraform($name = null) {
        return new ElementAlteBoxBody($name);
    }

}