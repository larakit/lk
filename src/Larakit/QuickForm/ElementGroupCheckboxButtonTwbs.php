<?php
namespace Larakit\QuickForm;
class ElementGroupCheckboxButtonTwbs extends \HTML_QuickForm2_Container_Group {
    use TraitNode, TraitContainer;


    public function getType() {
        return 'group_checkbox_button_twbs';
    }

    /**
     * @param string $name
     * @param        $options
     * @param bool   $is_inline
     *
     * @return ElementGroupCheckboxTwbs
     */
    static function laraform($name = '', $options) {
        $gr = new ElementGroupCheckboxButtonTwbs($name);
        $gr->setAttribute('data-toggle', 'buttons');
        foreach ($options as $k => $v) {
            $gr->putCheckboxTwbs($k, $k)
                ->setAttribute('autocomplete', 'off')
                ->setLabel($v);
        }
        return $gr;
    }

}
