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

class FilterBoolean extends Filter {

    function element(\HTML_QuickForm2_Container $form) {
        $this->options = [
            -1 => 'Нет',
            0 => 'ALL',
            1 => 'YES',
        ];
        $form->put($this->form_field, $this->options)->setLabel($this->label);
    }

    function query($model) {
        $v = (int)$this->value;
        if(0!=$v){
            if($this->relation) {
                $model->whereHas($this->relation, function ($query) use ($v) {
                    $query->where($this->db_field, '=',(1==$v));
                });
            } else {
                $model->where($this->db_field, '=',(1==$v));
            }
        }
    }
}