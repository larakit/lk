<?php
namespace Larakit\QuickForm;
class ElementSubmitTwbs extends \HTML_QuickForm2_Element_InputSubmit {
    use TraitNode;

    public function getType() {
        return 'submit_twbs';
    }

    /**
     * @param null $name
     *
     * @return ElementSubmitTwbs
     */
    static function laraform($title, $name=null) {
        $el = new ElementSubmitTwbs($name, ['value' => $title]);
        $el->addClass('btn');
        return $el;
    }

}
