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

class FilterIn extends Filter {

    function element(\HTML_QuickForm2_Container $form) {
        $cnt = count($this->options);
        switch(true) {
            case ($cnt <= 5):
                $form->putGroupCheckboxButtonTwbs($this->form_field, $this->options)->setLabel($this->label);
                break;
            case ($cnt <= 15):
                $form->putGroupCheckboxTwbs($this->form_field, $this->options, false)->setLabel($this->label);
                break;
            default:
                $form->putSelect2Twbs($this->form_field)
                    ->setMultiple(true)
                    ->setLabel($this->label)
                    ->loadOptions($this->options);
                break;
        }
    }

    function query($model) {
        if($this->value) {
            $values = array_map('intval', $this->value);
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