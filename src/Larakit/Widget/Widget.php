<?php
namespace Larakit\Widget;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Widget {

    static    $instances = [];
    protected $values    = [];

    static function widget_name() {
        $function_name = 'widget_';
        $r             = new \ReflectionClass(get_called_class());
        $function_name .= Str::snake(str_replace('Widget', '', $r->getShortName()));
        if('Larakit\Widget' != $r->getNamespaceName()) {
            $function_name .= '__'.Str::snake(str_replace(['\\', 'Widget'], '',$r->getNamespaceName()));
        }
        return $function_name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    static function instance($name = 'default') {
        $class = get_called_class();
        if(!isset(self::$instances[$class][$name])) {
            self::$instances[$class][$name] = new $class($name);
        }

        return self::$instances[$class][$name];
    }

    function _($method, $v) {
        $method = snake_case(substr($method, 3));

        return $this->set($method, $v);
    }

    function set($k, $v) {
        Arr::set($this->values, $k, $v);

        return $this;
    }

    function add($k, $v) {
        if(!isset($this->values[$k]) || !is_array($this->values[$k])) {
            $this->values[$k] = [];
        }
        $this->values[$k][] = $v;

        return $this;
    }

    function get($k, $default = null) {
        return Arr::get($this->values, $k, $default);
    }

    function tpl() {
        $ret = static::widget_name().'::!.widgets.'.mb_substr(static::widget_name(),7);
        return $ret;
    }

    function toHtml() {
        return \View::make($this->tpl(),
            $this->values)->__toString();

    }

    function __toString() {
        try {
            return $this->toHtml();
        }
        catch(\Exception $e) {
            laratrace();

            return '<div class="alert alert-danger">
                            <strong>Ошибка виджета ' . get_called_class() . ':</strong> '
            . $e->getMessage()
            . '<br>'
            . $e->getFile()
            . ':'
            . $e->getLine()
            . '</div>';
        }
    }
}