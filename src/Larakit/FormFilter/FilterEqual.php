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

class FilterSelfLikeEqual extends FilterSelfLike {

    function element(LaraForm $form) {
        $form->putTbText($this->form_field)
            ->setLabel($this->label);

    }

    function query(\Eloquent $model) {
        $model->where(function ($query) {
            $query->where($this->db_field, '=', $this->value);
        });
    }
}