<?php
namespace Larakit\QuickForm;
class ElementTextTwbs extends \HTML_QuickForm2_Element_InputText {
    use TraitNode;

    public function getType() {
        return 'text_twbs';
    }

    /**
     * @param null $name
     *
     * @return ElementTextTwbs
     */
    static function laraform($name) {
        $el = new ElementTextTwbs($name);
        $el->addClass('form-control');
        return $el;
    }

}
