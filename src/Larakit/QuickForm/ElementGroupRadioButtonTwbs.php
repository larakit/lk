<?php
namespace Larakit\QuickForm;
class ElementGroupRadioButtonTwbs extends \HTML_QuickForm2_Container_Group {
    use TraitNode, TraitContainer;

    public function getType() {
        return 'group_radio_button_twbs';
    }

    /**
     * @param string $name
     * @param        $options
     *
     * @return ElementGroupRadioButtonTwbs
     */
    static function laraform($name = '', $options) {
        $gr = new ElementGroupRadioButtonTwbs();
        $gr->setAttribute('data-toggle', 'buttons');
        foreach ($options as $k => $v) {
            $gr->putRadioTwbs($name, $k)
                ->setAttribute('autocomplete', 'off')
                ->setLabel($v);
        }
        return $gr;
    }

}