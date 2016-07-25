<?php
namespace Larakit\QuickForm;
class ElementGroupTwbs extends \HTML_QuickForm2_Container_Group {
    use TraitNode, TraitContainer;

    public function getType() {
        return 'group_twbs';
    }

    /**
     * @param null $name
     *
     * @return ElementGroupTwbs
     */
    static function laraform($name = null) {
        return new ElementGroupTwbs($name);
    }

}