<?php
namespace Larakit\QuickForm;

class ElementPasswordTwbs extends \HTML_QuickForm2_Element_InputPassword {

    use TraitNode;

    public function getType() {
        return 'password_twbs';
    }

    /**
     * @param string $name
     *
     * @return ElementTbPassword
     */
    static function laraform($name, $min = 6, $lower = true, $upper = true, $digits = true) {
        $el = new ElementPasswordTwbs($name);
        return $el->setAppend('<i class="fa fa-eye js-laraform-password pointer"></i>')
            ->setPrepend('<i class="fa fa-lock"></i>')
            ->addClass('form-control');
    }

}