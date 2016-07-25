<?php
namespace Larakit\QuickForm;
class ElementSelect2Twbs extends ElementSelectTwbs {
    protected $lara_options = null;

    public function getType() {
        return 'select2_twbs';
    }

    /**
     * @param        $value
     * @param null   $name
     * @param string $desc
     * @param array  $data
     * @param int    $r
     *
     * @return ElementTbSelect2
     */
    static function laraform($name, $attributes = null, array $data = []) {
        $el = new ElementSelect2Twbs($name);
        $el->addClass('js-larakit-select2 form-control');
        return $el;
    }

    function laraOption($k, $v) {
        if (!$this->lara_options) {
            $this->lara_options = [];
        }
        $this->lara_options[$k] = $v;
    }

    function setAutoCompleteUrl($url) {
        $this->setAttribute('data-url', $url);
        return $this;
    }

    function setTemplate($val) {
        $this->setAttribute('data-template', $val);
        return $this;
    }

    /**
     * @param $js_callback
     *
     * @return $this
     */
    function setTemplateResult($js_callback) {
        $this->laraOption('templateResult', $js_callback);
        return $this;
    }
    /**
     * @param $js_callback
     *
     * @return $this
     */
    function setTemplateSelection($js_callback) {
        $this->setAttribute('data-templateSelection', $js_callback);
        return $this;
    }

    function setTags($value = true, $separators = [',', ' ']) {
        $this->setMultiple($value);
        $value = (bool)$value;
        $this->setAttribute('data-s2-tags', $value);
//        $this->setAttribute('data-s2-tokenSeparators', '['']');
        return $this;
    }

    function setWidth($width) {
        $this->setAttribute('data-s2-width', $width);
        return $this;
    }


    function setLoadMorePadding($loadMorePadding) {
        $this->setAttribute('data-s2-loadMorePadding', $loadMorePadding);
        return $this;
    }


    function setCloseOnSelect($closeOnSelect) {
        $this->setAttribute('data-s2-closeOnSelect', $closeOnSelect);
        return $this;
    }


    function setOpenOnEnter($openOnEnter) {
        $this->setAttribute('data-s2-openOnEnter', $openOnEnter);
        return $this;
    }


    function setContainerCss($containerCss) {
        $this->setAttribute('data-s2-containerCss', $containerCss);
        return $this;
    }


    function setDropdownCss($dropdownCss) {
        $this->setAttribute('data-s2-dropdownCss', $dropdownCss);
        return $this;
    }


    function setContainerCssClass($containerCssClass) {
        $this->setAttribute('data-s2-containerCssClass', $containerCssClass);
        return $this;
    }


    function setDropdownCssClass($dropdownCssClass) {
        $this->setAttribute('data-s2-dropdownCssClass', $dropdownCssClass);
        return $this;
    }


    function setMinimumResultsForSearch($minimumResultsForSearch) {
        $this->setAttribute('data-s2-minimumResultsForSearch', $minimumResultsForSearch);
        return $this;
    }


    function setMinimumInputLength($minimumInputLength) {
        $this->setAttribute('data-s2-minimumInputLength', $minimumInputLength);
        return $this;
    }


    function setMaximumInputLength($maximumInputLength) {
        $this->setAttribute('data-s2-maximumInputLength', $maximumInputLength);
        return $this;
    }


    function setMaximumSelectionSize($maximumSelectionSize) {
        $this->setAttribute('data-s2-maximumSelectionSize', $maximumSelectionSize);
        return $this;
    }


    function setSeparator($separator) {
        $this->setAttribute('data-s2-separator', $separator);
        return $this;
    }


    function setTokenSeparators($tokenSeparators) {
        $this->setAttribute('data-s2-tokenSeparators', $tokenSeparators);
        return $this;
    }


    function setBlurOnChange($blurOnChange) {
        $this->setAttribute('data-s2-blurOnChange', $blurOnChange);
        return $this;
    }


    function setSelectOnBlur($selectOnBlur) {
        $this->setAttribute('data-s2-selectOnBlur', $selectOnBlur);
        return $this;
    }

}
