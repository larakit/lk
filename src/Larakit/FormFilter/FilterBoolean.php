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

    protected $no  = 'Нет';
    protected $yes = 'Да';
    protected $all = 'Все';

    /**
     * @param string $no
     *
     * @return FilterBoolean;
     */
    public function setNo($no) {
        $this->no = $no;

        return $this;
    }

    /**
     * @param string $yes
     *
     * @return FilterBoolean;
     */
    public function setYes($yes) {
        $this->yes = $yes;

        return $this;
    }

    /**
     * @param string $all
     *
     * @return FilterBoolean;
     */
    public function setAll($all) {
        $this->all = $all;

        return $this;
    }

    function element(\HTML_QuickForm2_Container $form) {
        $this->options = [
            0  => $this->all,
            -1 => $this->no,
            1  => $this->yes,
        ];
        $form->putGroupRadioButtonTwbs($this->form_field, $this->options)->setLabel($this->label);
        if(!$this->value) {
            $form->getForm()->initValues([
                $this->form_field => 0,
            ]);
        }
    }

    function query($model) {
        $v = (int) $this->value;
        if(0 != $v) {
            if($this->relation) {
                $model->whereHas($this->relation, function ($query) use ($v) {
                    $query->where($this->db_field, '=', (1 == $v));
                });
            } else {
                $model->where($this->db_field, '=', (1 == $v));
            }
        }
    }
}