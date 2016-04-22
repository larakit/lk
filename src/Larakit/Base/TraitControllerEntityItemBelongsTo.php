<?php
namespace Larakit\Base;

use Larakit\Event;
use Larakit\Manager\ManagerRowType;
use Larakit\QuickForm\LaraForm;

Trait TraitControllerEntityItemBelongsTo {

    static function getEntitySuffix() {
        return 'item_belongs_to';
    }

    function traitEntity_modelById($model_name, $id) {
        $this->relation = \Input::get('relation');
        if (static::isSoftDelete()) {
            return $model_name::withTrashed()->with($this->relation)->find($id);
        } else {
            return $model_name::with($this->relation)->find($id);
        }
    }

    protected $relation;

    function ids($key = 'id') {
        return $this->model->{$this->getRelationKey()};
    }

    function getRelationKey() {
        return \Str::singular($this->relation) . '_id';
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

    function getForm() {
        $form = new LaraForm($this->relation);
        $form->putTbHidden('relation')->setValue($this->relation);
        $method  = \Str::camel('relation_' . $this->relation);
        $model   = call_user_func([
            $this,
            $method
        ]);
        $options = $model::orderBy($this->orderByModel())->listsExt();
        if (!is_array($options)) {
            $options = [];
        }
        $method = \Str::camel('empty_' . $this->relation);
        if (method_exists($this, $method)) {
            $empty   = call_user_func([
                $this,
                $method
            ]);
            $options = [0 => $empty] + $options;
        }
        $form->putTbGroupRadio('belongs_to', $options, false);
        $ids = $this->ids();
        $form->initValues([
            'belongs_to' => $ids
        ]);

        return $form;
    }


    function traitEntityBelongsTo_btnText() {
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

    function traitEntityBelongsTo_header() {
        $method = \Str::camel('header_' . $this->relation);
        $header = '';
        if (method_exists($this, $method)) {
            $header = call_user_func([
                $this,
                $method
            ]);
        }

        return $header ? $header : 'Установка связей';
    }

    function traitEntityBelongsTo_body() {
        return $this->getForm();
    }

    function traitEntityBelongsTo_successMessage() {
        $method          = \Str::camel('success_message_' . $this->relation);
        $success_message = '';
        if (method_exists($this, $method)) {
            $success_message = call_user_func([
                $this,
                $method
            ]);
        }

        return $success_message ? $success_message : 'Связи для записи "%s" успешно установлены';
    }

    function traitEntityBelongsTo_footer() {
        return \HtmlButton::addClass('js-curtain-submit btn btn-primary')
                          ->setContent($this->traitEntityBelongsTo_btnText());
    }

    function traitEntityBelongsTo_options($model_name) {
        $options = $model_name::listsExt();
        if (!$options) {
            $options = [];
        }

        return $options;
    }

    function traitEntityBelongsTo_item() {
        if (\Request::method() == 'POST') {
            $id = (int)\Input::get('belongs_to');

            $this->model->{$this->getRelationKey()} = $id;
            $this->model->save();
            //                        ->sync($ids);
            //переоткроем модель

            $model_name = $this->getClassModel();
            /** @var Model $model */
            if($model_name::isSoftDelete()){
                $model      = $model_name::withTrashed();
            } else {
                $model      = $model_name::select();
            }
            //откроем со всеми связями, которые могут потребоваться для шаблона ROW
            foreach ($model->getModel()->getRelations() as $relation) {
                $model->with($relation);
            }
            $model     = $model->find(\Route::input('id'));
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
                'message' => sprintf($this->traitEntityBelongsTo_successMessage(), $model),
            ];
            Event::notify('larakit::model_edit-admin', $data);
            $this->traitAjax_set($data);
            return $this->traitAjax_response();
        }

        $this->traitAjax_set('id', $this->model->id)
             ->traitAjax_set('result', 'curtain')
             ->traitAjax_set('vendor', static::getVendor())
             ->traitAjax_set('entity', $this->getEntity())
             ->traitAjax_set('model', $this->model->toArray())
             ->traitAjax_set('header', (string)$this->traitEntityBelongsTo_header())
             ->traitAjax_set('body', (string)$this->traitEntityBelongsTo_body())
             ->traitAjax_set('footer', (string)$this->traitEntityBelongsTo_footer());

        return $this->traitAjax_response();
    }

}