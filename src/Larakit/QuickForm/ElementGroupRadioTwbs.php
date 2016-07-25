<?php
namespace Larakit\QuickForm;
class ElementGroupRadioTwbs extends \HTML_QuickForm2_Container_Group {
    use TraitNode, TraitContainer;

    public function getType() {
        return 'group_radio_twbs';
    }

    /**
     * @param string $name
     * @param        $options
     * @param bool   $is_inline
     *
     * @return ElementGroupRadioTwbs
     */
    static function laraform($name = '', $options, $is_inline = true) {
        $gr = new ElementGroupRadioTwbs();
        if ($is_inline) {
            $gr->setIsInline(true);
        }
        foreach ($options as $k => $v) {
            $gr->putRadioTwbs($name, $k)
               ->setLabel($v);
        }
        return $gr;
    }

}