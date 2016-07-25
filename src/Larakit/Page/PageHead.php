<?php
namespace Larakit\Page;

class PageHead {

    protected $group;
    protected $items;

    function to($group) {
        $this->group = $group;

        return $this;
    }

    /**
     * @return \HtmlLink
     */
    function &addLink($url) {
        $key                             = __METHOD__ . $url;
        $this->items[$this->group][$key] = \HtmlLink::setHref($url);

        return $this->items[$this->group][$key];
    }

    /**
     * @return \HtmlMeta
     */
    function &addMetaPlain($k, $v) {
        $key                             = __METHOD__ . $k;
        $this->items[$this->group][$key] = \HtmlMeta::setAttribute($k, $v);

        return $this->items[$this->group][$key];
    }

    /**
     * @return \HtmlMeta
     */
    function &addMetaName($k, $v) {
        $key                             = __METHOD__ . $k;
        $this->items[$this->group][$key] = \HtmlMeta::setAttribute('name', $k)->setAttribute('content', $v);

        return $this->items[$this->group][$key];
    }

    /**
     * @return \HtmlMeta
     */
    function &addMetaHttpEquiv($k, $v) {
        $key                             = __METHOD__ . $k;
        $this->items[$this->group][$key] = \HtmlMeta::setAttribute('http-equiv', $k)->setAttribute('content', $v);

        return $this->items[$this->group][$key];
    }

    /**
     * @return \HtmlMeta
     */
    function &addMetaProperty($property, $content) {
        $key                             = __METHOD__ . $property;
        $this->items[$this->group][$key] = \HtmlMeta::setAttribute('property', $property)->setAttribute('content', $content);

        return $this->items[$this->group][$key];
    }

    function addLinkDnsPrefetch($url) {
        $url = parse_url($url, PHP_URL_HOST);

        return $this->addLink($url)->setRel('dns-prefetch');
    }

    function __toString() {
        $ret = '';
        foreach($this->items as $group=>$items){
            foreach($items as $item){
                $ret .= '        ';
            }
        }
        return $ret;
    }

    function metaTwitterCard(){
        return $this->addMetaProperty('', '');
    }
}

$head = new PageHead();
$head->to('123123')
    ->addLink('');