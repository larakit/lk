<?php
namespace Larakit\QuickForm;
class ElementEmailTwbs extends \HTML_QuickForm2_Element_InputText {
    use TraitNode;

    protected $attributes = ['type' => 'email'];

    public function getType() {
        return 'email_twbs';
    }

    /**
     * @param null $name
     *
     * @return ElementEmailTwbs
     */
    static function laraform($name) {
        $el = new ElementEmailTwbs($name);
        $el->addClass('form-control');
        return $el;
    }

}
