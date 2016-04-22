<?php
namespace Larakit\Base;

use Illuminate\Support\Arr;

class DataFilter {

    /**
     * Массив данных для фильтрации
     *
     * @var array
     */
    protected $data;

    /**
     * Массив фильтров
     *
     * @var array
     */
    protected $filters = [];


    /**
     * @var \Eloquent
     */

    function __construct($data) {
        $this->data = $data;
        $this->init();
    }

    /**
     * @param $model
     *
     * @return Validator
     */
    static function factory($data) {
        $class = get_called_class();
        return new $class($data);
    }


    function getClean() {
        $ret = $this->data;
        foreach ($this->filters as $field => $filters) {
            foreach ($filters as $filter) {
                $filtered = call_user_func($filter, Arr::get($ret, $field));
                Arr::set($ret, $field, $filtered);
            }
        }
        return $ret;
    }

    function init() {
    }
    /**
     * @param $field
     * @param $callback
     *
     * @return $this
     */
    function addFilter($field, $callback) {
        $this->filters[$field][serialize($callback)] = $callback;
        return $this;
    }

}