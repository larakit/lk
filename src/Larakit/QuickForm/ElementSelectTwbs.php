<?php
namespace Larakit\QuickForm;
class ElementSelectTwbs extends \HTML_QuickForm2_Element_Select {
    use TraitNode;

    public function getType() {
        return 'select_twbs';
    }

    /**
     * @param null $name
     *
     * @return ElementTbSelect
     */
    static function laraform($name, $attributes = null, array $data = []) {
        $el = new ElementSelectTwbs($name, $attributes, $data);
        $el->addClass('form-control');
        return $el;
    }

    function loadOptionsCombine($options) {
        $options_values = array_values($options);
        $options        = array_combine($options_values, $options_values);
        return $this->loadOptions($options);
    }

    function setMultiple($val = true) {
        if ($val) {
            $this->setAttribute('multiple', 'multiple');
        } else {
            $this->removeAttribute('multiple');
        }
        return $this;
    }

}