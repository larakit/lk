<?php
namespace Larakit\QuickForm;
class ElementTextareaTwbs extends \HTML_QuickForm2_Element_Textarea {
    use TraitNode;

    public function getType() {
        return 'textarea_twbs';
    }

    /**
     * @param null $name
     *
     * @return ElementTextareaTwbs
     */
    static function laraform($name) {
        $el = new ElementTextareaTwbs($name);
        $el->addClass('form-control');

        return $el;
    }

    /**
     * @param $val
     *
     * @return ElementTextareaTwbs
     */
    function setCols($val) {
        return $this->setAttribute('cols', $val);
    }

    /**
     * @param $val
     *
     * @return ElementTextareaTwbs
     */
    function setRows($val) {
        return $this->setAttribute('rows', $val);
    }
}