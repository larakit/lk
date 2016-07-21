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

class FilterBetween extends Filter {

    function element(\HTML_QuickForm2_Container $form) {
        $gr = $form->putGroupTwbs($this->form_field)
            ->setLabel($this->label . ($this->units ? ', ' . $this->units : ''));
        $gr->putNumberTwbs('from')
            ->setPrepend('от')
            ->setWrapClass('col-lg-6');
        $gr->putNumberTwbs('to')
            ->setPrepend('до')
            ->setWrapClass('col-lg-6');
    }

    function query($model) {
        $min = Arr::get($this->value, 'from');
        $max = Arr::get($this->value, 'to');
        if('' != $min || '' != $max) {
            if($this->relation) {
                $model->whereHas($this->relation, function ($query) use ($min, $max) {
                    if('' != $min) {
                        $query->where($this->db_field, '>=', $min);
                    }
                    if('' != $max) {
                        $query->where($this->db_field, '<=', $max);
                    }
                });
            } else {
                if('' != $min) {
                    $model->where($this->db_field, '>=', $min);
                }
                if('' != $max) {
                    $model->where($this->db_field, '<=', $max);
                }
            }
        }
    }
}