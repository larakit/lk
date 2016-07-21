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

class FilterSelfLike {

    protected $db_field;
    protected $form_field;
    protected $label;
    public $value;

    function __construct($form_field, $label, $db_field = null) {
        $this->form_field = $form_field;
        $this->label      = $label;
        $this->db_field   = $db_field ? : $form_field;
        $this->value      = \Request::input($this->form_field);
    }

    /**
     * @param      $form_field
     * @param      $label
     * @param null $db_field
     *
     * @return $this
     */
    static function factory($form_field, $label, $db_field = null) {
        $class = get_called_class();

        return new $class($form_field, $label, $db_field);
    }

    function element(\HTML_QuickForm2_Container $form) {
        $form->putTextTwbs($this->form_field)
            ->setLabel($this->label);

    }

    function query($model) {
        $model->where(function ($query) {
            $query->where($this->db_field, 'like', '%' . $this->value . '%');
        });
    }
}