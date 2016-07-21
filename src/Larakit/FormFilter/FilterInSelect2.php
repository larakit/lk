<?php
/**
 * Created by Larakit.
 * Link: http://github.com/larakit
 * User: Alexey Berdnikov
 * Date: 20.07.16
 * Time: 19:42
 */

namespace Larakit\FormFilter;

use Illuminate\Support\Arr;
use Larakit\QuickForm\LaraForm;

class FilterInSelect2 extends Filter {

    function element(\HTML_QuickForm2_Container $form) {
        $form->putSelect2Twbs($this->form_field)
            ->setMultiple(true)
            ->setLabel($this->label)
            ->loadOptions($this->options);
    }

    function query($model) {
        if($this->value) {
            $values = array_map('intval', (array)$this->value);
            if($this->relation) {
                $model->whereHas($this->relation, function ($query) use ($values) {
                    $query->whereIn($this->db_field, $values);
                });
            } else {
                $model->whereIn($this->db_field, $values);
            }
        }
    }
}