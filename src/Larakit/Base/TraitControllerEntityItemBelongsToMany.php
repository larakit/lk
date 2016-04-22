<?php
namespace Larakit\Base;

use Larakit\Event;
use Larakit\Manager\ManagerRowType;
use Larakit\QuickForm\LaraForm;

Trait TraitControllerEntityItemBelongsToMany {

    protected $relation;
    protected $traitEntityBelongsToMany_form;

    static function getEntitySuffix() {
        return 'item_belongs_to_many';
    }

    function ids($key = 'id') {
        $ids = [];
        foreach ($this->model->{$this->relation} as $model) {
            $ids[$model->{$key}] = $model->{$key};
        }

        return $ids;
    }

    function orderByModel() {
        $method = \Str::camel('order_by_' . $this->relation);
        if (method_exists($this, $method)) {
            return call_user_func([
                $this,
                $method
            ]);
        }
        return 'order';
    }


    function buildForm() {
        $form = new LaraForm($this->relation);
        $form->putTbHidden('relation')->setValue($this->relation);
        $options = [];
        $model   = $this->traitEntityBelongsToMany_getBelongsModel();
        if ($model) {
            $options = $model::orderBy($this->orderByModel())->listsExt();
            if (!is_array($options)) {
                $options = [];
            }
        }
        $form->putTbGroupCheckbox('belongs_to_many', $options, false);
        $ids = $this->ids();
        $form->initValues([
            'belongs_to_many' => array_combine($ids, $ids)
        ]);
        if ($form->validate()) {
            $many = $form->getFieldValue('belongs_to_many');

            $filtered = array_filter($many,
                function ($value) {
                    return $value;
                });
            $ids      = array_keys($filtered);

            $this->model->{$this->relation}()->sync($ids);

        }
        $this->traitEntityBelongsToMany_form = $form;
    }

    function traitEntityBelongsToMany_getBelongsModel() {
        $method = \Str::camel('relation_' . $this->relation);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return false;
    }

    function traitEntityBelongsToMany_body() {

        return $this->traitEntityBelongsToMany_form;
    }

    function traitEntityBelongsToMany_header() {
        $method = \Str::camel('header_' . $this->relation);
        $header = '';
        if (method_exists($this, $method)) {
            $header = call_user_func([
                $this,
                $method
            ]);
        }

        return $header ? $header : \Lang::get('larakit::relations.header.belongs_to_many');;
    }

    function traitEntityBelongsToMany_btnText() {
        $method = \Str::camel('btn_text_' . $this->relation);
        $header = '';
        if (method_exists($this, $method)) {
            $header = call_user_func([
                $this,
                $method
            ]);
        }

        return $header ? $header : 'Сохранить';
    }

    function traitEntityBelongsToMany_footer() {
        return \HtmlButton::addClass('js-curtain-submit btn btn-primary')
                          ->setContent($this->traitEntityBelongsToMany_btnText());
    }

    function traitEntityBelongsToMany_successMessage() {
        $method          = \Str::camel('success_message_' . $this->relation);
        $success_message = '';
        if (method_exists($this, $method)) {
            $success_message = call_user_func([$this, $method]);
        }
        return $success_message ? $success_message : \Lang::get('larakit::relations.success.belongs_to_many');
    }

    function traitEntityItemBelongsToMany() {
        $this->relation = \Input::get('relation');
        $this->buildForm();
        if ($this->traitEntityBelongsToMany_form->validate()) {
            //переоткроем модель
            $model_name = static::getClassModel();
            if($model_name::isSoftDelete()){
                $model      = $model_name::withTrashed()->with($this->relation)->find(\Route::input('id'));
            } else {
                $model      = $model_name::with($this->relation)->find(\Route::input('id'));
            }
            $row_types = ManagerRowType::get(ManagerRowType::makeKey(static::getVendor(), static::getEntity()));
            $rows      = [];
            foreach ($row_types as $row_type) {
                $rows[$row_type] = (string)Accessor::factory($model)->row($row_type);
            }
            $data = [
                'models'  => [
                    [
                        'rows'  => $rows,
                        'model' => $model->toArray(),
                        'id'    => $model->id,
                    ]
                ],
                'vendor'  => (string)static::getVendor(),
                'entity'  => \Str::snake(static::getEntity()),
                'message' => sprintf($this->traitEntityBelongsToMany_successMessage(), $model),
            ];

            Event::notify('larakit::model_edit-admin', $data);
            $this->traitAjax_set($data);
            return $this->traitAjax_response();
        }
        $this->traitAjax_set('id', $this->model->id)
             ->traitAjax_set('result', 'curtain')
             ->traitAjax_set('vendor', (string)\Str::snake($this->getVendor()))
             ->traitAjax_set('entity', (string)\Str::snake($this->getEntity()))
             ->traitAjax_set('model', $this->model->toArray())
             ->traitAjax_set('header', (string)$this->traitEntityBelongsToMany_header())
             ->traitAjax_set('body', (string)$this->traitEntityBelongsToMany_body())
             ->traitAjax_set('footer', (string)$this->traitEntityBelongsToMany_footer());

        return $this->traitAjax_response();
    }

}