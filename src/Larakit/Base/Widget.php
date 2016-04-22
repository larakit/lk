<?php
namespace Larakit\Base;

use Illuminate\Support\Arr;

class Widget {
    static    $instances = [];
    protected $values    = [];

    /**
     * @param string $name
     *
     * @return $this
     */
    static function factory($name = 'default') {
        $class = get_called_class();
        if (!isset(self::$instances[$class][$name])) {
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
        if (!isset($this->values[$k]) || !is_array($this->values[$k])) {
            $this->values[$k] = [];
        }
        $this->values[$k][] = $v;
        return $this;
    }

    function get($k) {
        return Arr::get($this->values, $k);
    }

    function tpl() {
        $c           = explode('Widget\Widget', get_called_class());
        $widget_name = Arr::get($c, 1);
        $namespace   = Arr::get($c, 0);
        $namespace   = str_replace('\\', '', $namespace);
        return ($namespace ? snake_case($namespace, '-') . '::' : '') . '!.widgets.' . snake_case($widget_name);
    }

    function toHtml() {
        return \View::make($this->tpl(),
            $this->values)->__toString();

    }

    function __toString() {
        try {
            return $this->toHtml();
        } catch (\Exception $e) {
            laratrace();
            return '<div class="alert alert-danger">
                            <strong>Ошибка виджета ' . get_called_class() . ':</strong> '
            .$e->getMessage()
                .'<br>'
            .$e->getFile()
            .':'
            .$e->getLine()
            . '</div>';
        }
    }
}