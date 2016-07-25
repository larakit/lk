<?php
namespace Larakit\QuickForm;

class ElementSwitchTwbs extends \HTML_QuickForm2_Element_InputCheckbox {

    use TraitNode;

    public function getType() {
        return 'switch_twbs';
    }

    /**
     * @param        $value
     * @param null   $name
     * @param string $desc
     * @param array  $data
     * @param int    $r
     *
     * @return ElementSwitchTwbs
     */
    static function laraform($name) {
        $el = new ElementSwitchTwbs($name);
        $el->addClass('js-bootstrap-switch')
            ->setOffClass('danger')
            ->setOffLabel('Нет')
            ->setOnClass('success')
            ->setOnLabel('Да')
            ->setSizeSmall();

        return $el;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setOnClass($value) {
        return $this->setAttribute('data-on-color', $value);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setOffClass($value) {
        return $this->setAttribute('data-off-color', $value);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setOnLabel($value) {
        return $this->setAttribute('data-on-text', $value);
    }
    function setInverse($value) {
        return $this->setAttribute('data-inverse', (bool)$value);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setOffLabel($value) {
        return $this->setAttribute('data-off-text', $value);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setSizeLarge() {
        return $this->setAttribute('data-tb-size', 'lg');
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setSizeSmall() {
        return $this->setAttribute('data-tb-size', 'sm');
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setSizeExtraSmall() {
        return $this->setAttribute('data-tb-size', 'xs');
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setSizeMiddle() {
        return $this->setAttribute('data-tb-size', 'md');
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setSizeNormal() {
        return $this->setAttribute('data-tb-size', '');
    }

    function setChecked($v){

    }

    public function getRawValue() {
        if(!empty($this->attributes['checked']) && empty($this->attributes['disabled'])) {
            return $this->getAttribute('value');
        } else {
            return false;
        }
    }

}
