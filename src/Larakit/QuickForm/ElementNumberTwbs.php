<?php
namespace Larakit\QuickForm;

class ElementNumberTwbs extends ElementTextTwbs {

    protected $attributes = ['type' => 'number'];

    public function getType() {
        return 'number_twbs';
    }

    /**
     * @param null $name
     *
     * @return ElementNumberTwbs
     */
    static function laraform($name) {
        $el = new ElementNumberTwbs($name);
        $el->addClass('form-control');

        return $el;
    }

    function setMin($min) {
        return $this->setAttribute('min', $min);
    }

    function setMax($max) {
        return $this->setAttribute('max', $max);
    }

    function ruleMin($min) {
        $this->getForm()->ruleApply($this, 'gte', 'min.numeric', $min, [':min' => $min]);
    }

    function ruleMax($max) {
        $this->getForm()->ruleApply($this, 'lte', 'max.numeric', $max, [':max' => $max]);
    }

}
