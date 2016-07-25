<?php
namespace Larakit\QuickForm;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait TraitNode {

    protected $larakit = [];

    protected function laraGet($k, $default = null) {
        if(mb_strpos($k, 'get') !== false) {
            $k = mb_substr($k, 3);
        }
        $k = Str::snake($k);

        return \Illuminate\Support\Arr::get($this->larakit, $k, $default);
    }

    protected function laraSet($k, $v) {
        if(mb_strpos($k, 'set') !== false) {
            $k = mb_substr($k, 3);
        }
        $k                 = Str::snake($k);
        $this->larakit[$k] = $v;

        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setDesc($value) {
        return $this->laraSet(__FUNCTION__, $value);
    }

    /**
     * @return mixed
     */
    function getDesc() {
        return $this->laraGet(__FUNCTION__);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setExample($value, $title = null) {
        return $this->laraSet(__FUNCTION__, $title? [[$value=>$title]] : $value);
    }

    function getExample() {
        return $this->laraGet(__FUNCTION__);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setExampleIsAppend($value) {
        return $this->laraSet(__FUNCTION__, $value);
    }

    function getExampleIsAppend() {
        return $this->laraGet(__FUNCTION__);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setPlaceholder($value) {
        $this->setAttribute('placeholder', $value);

        return $this->laraSet(__FUNCTION__, $value);
    }

    function getPlaceholder() {
        return $this->laraGet(__FUNCTION__);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setPrepend($value) {
        return $this->laraSet(__FUNCTION__, $value);
    }

    function getPrepend() {
        return $this->laraGet(__FUNCTION__);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setAppend($value) {
        return $this->laraSet(__FUNCTION__, $value);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setAppendClear() {
        return $this->setAppend('<i class="js-laraform-clean pointer fa fa-eraser"></i>');
    }

    function getAppend() {
        return $this->laraGet(__FUNCTION__);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setWrapClass($value) {
        return $this->laraSet(__FUNCTION__, $value);
    }

    function getWrapClass() {
        return $this->laraGet(__FUNCTION__);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setIsInline($value) {
        return $this->laraSet(__FUNCTION__, $value);
    }

    function getIsInline() {
        return $this->laraGet(__FUNCTION__);
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setTpl($value) {
        return $this->laraSet(__FUNCTION__, $value);
    }

    function getTpl() {
        $type      = $this->getType();
        $namespace = Register::getNamespace($type);
        $tpl       = $this->laraGet(__FUNCTION__);
        if(!$tpl) {
            return $namespace . '::!.quickform.' . $type;
        }

        return $tpl;
    }

    /**
     * @return LaraForm
     */
    function getForm() {
        $container = $this->getContainer();
        if(is_a($container, \HTML_QuickForm2::class)) {
            return $container;
        }

        return $container->getForm();
    }

    function ruleMin($min) {
        $this->getForm()->ruleApply($this, 'minlength', 'min.string', $min, [':min' => $min]);
    }

    function ruleRegex($regex) {
        $this->getForm()->ruleApply($this, 'regex', 'regex', $regex);
    }

    function ruleConfirmed() {
        $form = $this->getForm();
        if(']' == mb_substr($this->getName(), -1)) {
            $confirm_name = mb_substr($this->getName(), 0, -1) . '_confirmation]';
        } else {
            $confirm_name = $this->getName() . '_confirmation';
        }
        $repeat = Arr::get($form->getElementsByName($confirm_name), 0);
        $this->getForm()->ruleApply($repeat, 'required', 'required', null);
        $this->getForm()->ruleApply($this, 'eq', 'confirmed', $repeat);
    }

    function ruleMax($max) {
        $this->getForm()->ruleApply($this, 'maxlength', 'min.string', $max, [':max' => $max]);
    }

    function ruleRequired() {
        $this->getForm()->ruleApply($this, 'required', 'required', null);
    }

    function getNameDot() {
        return str_replace(['][', '[', ']'],['.', '.', ''],  $this->getName());
    }

}