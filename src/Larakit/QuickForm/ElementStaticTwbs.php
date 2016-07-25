<?php
namespace Larakit\QuickForm;

class ElementStaticTwbs extends \HTML_QuickForm2_Element_Static {

    use TraitNode;

    public function getType() {
        return 'text_twbs';
    }

    /**
     * @param      $html
     * @param null $name
     *
     * @return ElementStaticTwbs
     */
    static function laraform($html, $name = null) {
        $el = new ElementStaticTwbs($name ? $name : uniqid());
        $el->setValue($html);

        return $el;
    }

}
