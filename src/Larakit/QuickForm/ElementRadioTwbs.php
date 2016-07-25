<?php
namespace Larakit\QuickForm;
class ElementRadioTwbs extends \HTML_QuickForm2_Element_InputRadio {
    use TraitNode;

    public function getType() {
        return 'radio_twbs';
    }

    /**
     * @param     $name
     * @param int $value
     *
     * @return ElementRadioTwbs
     */
    static function laraform($name, $value = 1) {
        return new ElementRadioTwbs($name, ['value' => $value]);
    }

}