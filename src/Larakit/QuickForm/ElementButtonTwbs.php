<?php
namespace Larakit\QuickForm;

class ElementButtonTwbs extends \HTML_QuickForm2_Element_Static {

    use TraitNode;

    public function getType() {
        return 'button_twbs';
    }

    /**
     * @param      $html
     * @param null $name
     *
     * @return ElementButtonTwbs
     */
    static function laraform($title, $name=null) {
        $el = new ElementButtonTwbs($name, ['value' => $title]);
        $el->addClass('btn');
        return $el;
    }

}
