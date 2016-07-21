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

class FilterLike extends Filter {

    function element(\HTML_QuickForm2_Container $form) {
        $form->putTextTwbs($this->form_field)
            ->setLabel($this->label)
            ->setDesc($this->desc);

    }

    function query($model) {
        if($this->value) {
            if($this->relation) {
                $model->whereHas($this->relation, function ($query) {
                    $query->where($this->db_field, 'like', '%' . $this->value . '%');
                });
            } else {
                $model->where(function ($query) {
                    $query->where($this->db_field, 'like', '%' . $this->value . '%');
                });
            }
        }
    }
}