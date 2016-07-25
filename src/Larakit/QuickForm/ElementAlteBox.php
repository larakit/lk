<?php
namespace Larakit\QuickForm;
class ElementAlteBox extends \HTML_QuickForm2_Container_Group {
    use TraitNode, TraitContainer;

    public function getType() {
        return 'alte_box';
    }

    /**
     * @param null $name
     *
     * @return ElementAlteBox
     */
    static function laraform($title) {
        $el = new ElementAlteBox(null); 
        $el->setLabel($title)
            ->addClass('box box-primary');
        return $el;
    }

}