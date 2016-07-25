<?php
namespace Larakit\QuickForm;
class ElementCheckboxTwbs extends \HTML_QuickForm2_Element_InputCheckbox {
    use TraitNode;


    public function getType() {
        return 'checkbox_twbs';
    }

    /**
     * @param null   $name
     * @param        $value
     *
     * @return ElementCheckboxTwbs
     */
    static function laraform($name, $value = 1) {
        return new ElementCheckboxTwbs($name, ['value' => $value]);
    }

    public function getRawValue() {
        if (!empty($this->attributes['checked']) && empty($this->attributes['disabled'])) {
            return $this->getAttribute('value');
        }
        else {
            return false;
        }
    }

    function ruleAccepted() {
        $this->getForm()->ruleApply($this, 'gte', 'accepted', 1);
    }


}
