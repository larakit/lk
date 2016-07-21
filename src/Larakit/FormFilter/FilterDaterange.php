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

class FilterDaterange extends Filter {

    function element(\HTML_QuickForm2_Container $form) {
        $form->putDaterangepickerTwbs($this->form_field);
    }

    function query($model) {
//        if($this->value) {
//            $values = array_map('intval', $this->value);
//            if($this->relation) {
//                $model->whereHas($this->relation, function ($query) use ($values) {
//                    $query->whereIn($this->db_field, $values);
//                });
//            } else {
//                $model->whereIn($this->db_field, $values);
//            }
//        }
    }
}