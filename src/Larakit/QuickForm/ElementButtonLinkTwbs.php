<?php
namespace Larakit\QuickForm;

class ElementButtonLinkTwbs extends ElementStaticTwbs {

    public function getType() {
        return 'button_link_twbs';
    }

    /**
     * @param      $html
     * @param null $name
     *
     * @return ElementStaticTwbs
     */
    static function laraform($href, $text=null) {
        $el = new ElementButtonLinkTwbs(
            null, null, [
                'tagName' => 'a'
            ]
        );
        return $el->setContent($text)
            ->addClass('btn')
            ->setHref($href);
    }

    function setHref($href) {
        return $this->setAttribute('href', $href);
    }

}
