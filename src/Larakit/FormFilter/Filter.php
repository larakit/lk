<?php
/**
 * Created by Larakit.
 * Link: http://github.com/larakit
 * User: Alexey Berdnikov
 * Date: 20.07.16
 * Time: 19:42
 */

namespace Larakit\FormFilter;

use Larakit\QuickForm\LaraForm;

class Filter {

    protected $db_field;
    protected $form_field;
    protected $label;
    protected $desc;
    protected $value;
    protected $units;
    protected $relation;
    protected $options = [];

    function __construct($form_field) {
        $this->form_field = $this->db_field = $form_field;
        $this->value      = \Request::input($this->form_field);
    }

    /**
     * @param      $form_field
     * @param      $label
     * @param null $db_field
     *
     * @return $this
     */
    static function factory($form_field) {
        $class = get_called_class();

        return new $class($form_field);
    }

    function dbField($val) {
        $this->db_field = $val;

        return $this;
    }

    function options($val) {
        $this->options = $val;

        return $this;
    }

    function units($val) {
        $this->units = $val;

        return $this;
    }

    function label($val) {
        $this->label = $val;

        return $this;
    }

    function hasValue() {
        return $this->value;
    }

    function desc($val) {
        $this->desc = $val;

        return $this;
    }

    function relation($val) {
        $this->relation = $val;

        return $this;
    }

    function element(\HTML_QuickForm2_Container $form) {
    }

    function query($model) {
    }
}