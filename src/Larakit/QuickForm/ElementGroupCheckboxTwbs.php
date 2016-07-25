<?php
namespace Larakit\QuickForm;
class ElementGroupCheckboxTwbs extends \HTML_QuickForm2_Container_Group {
    use TraitNode, TraitContainer;


    public function getType() {
        return 'group_checkbox_twbs';
    }

    /**
     * @param string $name
     * @param        $options
     * @param bool   $is_inline
     *
     * @return ElementGroupCheckboxTwbs
     */
    static function laraform($name = '', $options, $is_inline = true) {
        $gr = new ElementGroupCheckboxTwbs($name);
        if ($is_inline) {
            $gr->setIsInline(true);
        }
        foreach ($options as $k => $v) {
            $gr->putCheckboxTwbs($k, $k)
                ->setLabel($v);
        }
        return $gr;
    }

}
