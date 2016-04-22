<?php
namespace Larakit\Base;

use Larakit\Exception;
use Larakit\Manager\ManagerRowType;

Trait TraitControllerEntityItemToggle {

    protected function traitEntityItemToggle_switchRow($field, $on, $off, $type_on = 'success', $type_off = 'success') {
        $this->model->{$field} = (bool)(!$this->model->{$field});
        $this->traitAjax_set('result', $this->model->{$field} ? $type_on : $type_off);
    }

    function traitEntityItemToggle_messageDisableOffLast($field) {
        $method = \Str::camel('disable_off_last_' . $field);
        if (method_exists($this, $method)) {
            return call_user_func([
                $this,
                $method
            ]);
        }
        return 'Нельзя выключить единственную запись';
    }

    function traitEntityItemToggle_switch($field, $on, $off, $type_on = 'success', $type_off = 'success') {
        //        dump($this->model->toArray());
        //        dump(in_array($field, $this->traitEntityItemToggle_mass()));
        //        dump(in_array($field, $this->traitEntityItemToggle_requireOne()));
        //        dump(in_array($field, $this->traitEntityItemToggle_onlyOne()));
        //        dd(1);
        $model_name = get_class($this->model);
        //если требуется хотя бы одна обязательно включенная
        if (in_array($field, $this->traitEntityItemToggle_requireOne())) {
            if ($this->model->{$field}) {
                if ($model_name::where($field, '=', true)->count() < 2
                ) {
                    throw new Exception($this->traitEntityItemToggle_messageDisableOffLast($field));
                }
            }
        }
        if (in_array($field, $this->traitEntityItemToggle_mass())) {
            $this->traitEntityItemToggle_switchRow($field, $on, $off, $type_on, $type_off);
            //если включена может быть ТОЛЬКО одна - выключим остальные
            if (in_array($field, $this->traitEntityItemToggle_onlyOne())) {
                if ($this->model->{$field}) {
                    $model_name::where('id', '!=', $this->model->id)->update([
                            $field => 0
                        ]);
                }
            }
        } else {
            $this->traitEntityItemToggle_switchRow($field, $on, $off, $type_on, $type_off);
        }

        return $this->model->{$field} ? $on : $off;
    }

    static function getEntitySuffix() {
        return 'item_toggle';
    }

    /**
     * Требуется ли массовое обновление всех полей
     *
     * @return array
     */
    function traitEntityItemToggle_mass() {
        return [];
    }

    /**
     * Требуется ли хотя бы один включенный
     *
     * @return array
     */
    function traitEntityItemToggle_requireOne() {
        return [];
    }


    /**
     * если включена может быть ТОЛЬКО одна - выключим остальные
     * @return array
     */
    function traitEntityItemToggle_onlyOne() {
        return [];
    }


    function traitEntityItemToggle() {
        $field = \Input::get('field');
        $this->traitEntity_assertReason($this->model, 'toggle_' . $field);
        $method = \Str::camel('switch_' . $field);
        if (!is_callable([$this, $method])) {
            throw new Exception('Поле не является тумблером');
        }
        $message = call_user_func([
            $this,
            $method
        ]);
        $this->model->save();

        $row_types = ManagerRowType::get(ManagerRowType::makeKey(static::getVendor(), static::getEntity()));
        $rows      = [];
        foreach ($row_types as $row_type) {
            $rows[$row_type] = (string)Accessor::factory($this->model)->row($row_type);
        }
        $data = [
            'models'  => [
                [
                    'rows'  => $rows,
                    'model' => $this->model->toArray(),
                    'id'    => $this->model->id,
                ]
            ],
            'vendor'  => (string)static::getVendor(),
            'entity'  => \Str::snake(static::getEntity()),
            'message' => $message ? sprintf($message, $this->model) : 'Значение поля изменено на противоположное',
        ];

        if (in_array($field, $this->traitEntityItemToggle_mass())) {
            $rows       = [];
            $model_name = get_class($this->model);
            foreach ($model_name::get() as $model) {

                $rows = [];
                foreach ($row_types as $row_type) {
                    $rows[$row_type] = (string)Accessor::factory($model)->row($row_type);
                }
                $data['models'][] = [
                    'rows'  => $rows,
                    'model' => $model->toArray(),
                    'id'    => $model->id,
                ];

            }
        }
        $this->traitAjax_set($data);
        return $this->traitAjax_response();
    }

}