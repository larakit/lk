<?php
namespace Larakit\Base;

use Larakit\Event;
use Larakit\Manager\ManagerRowType;
use Larakit\QuickForm\LaraForm;

Trait TraitControllerEntityItemHasMany {


    protected $relation;
    /**
     * @var Model $model
     */
    //    protected $model;

    static function getEntitySuffix() {
        return 'item_has_many';
    }

    function traitEntity_modelById($model_name, $id) {
        $this->relation = \Input::get('relation');
        if ($model_name::isSoftDelete()) {
            return $model_name::withTrashed()->with($this->relation)->find($id);
        }
        return $model_name::with($this->relation)->find($id);
    }


    function ids($key = 'id') {
        $ids     = [];
        $results = $this->model->{$this->relation};
        foreach ($results as $r) {
            $ids[] = $r->$key;
        }

        return $ids;
    }

    function getRelationModel() {
        $method = "relation_" . $this->relation;

        return $this->{\Str::camel($method)}();
    }

    function getOptions() {
        $method = \Str::camel("options_" . $this->relation);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }
        $class = $this->getRelationModel();
        return $class::listsExt();
    }


    function getForm() {
        $form = new LaraForm($this->relation);
        $form->putTbHidden('relation')->setValue($this->relation);

        $options = $this->getOptions();
        if (!is_array($options)) {
            $options = [];
        }
        $form->putTbGroupCheckbox('has_many', $options, false);
        $ids = $this->ids();
        $form->initValues([
            'has_many' => array_combine($ids, $ids)
        ]);

        return $form;
    }


    function traitEntityHasMany_btnText() {
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

    function traitEntityHasMany_header() {
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

    function traitEntityHasMany_body() {
        return $this->getForm();
    }

    function traitEntityHasMany_successMessage() {
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

    function traitEntityHasMany_footer() {
        return \HtmlButton::addClass('js-curtain-submit btn btn-primary')
                          ->setContent($this->traitEntityHasMany_btnText());
    }

    function traitEntityHasMany_options($model_name) {
        $options = $model_name::listsExt();
        if (!$options) {
            $options = [];
        }

        return $options;
    }

    function traitEntityHasMany_save() {
        $method = \Str::camel('save_' . $this->relation);
        if (method_exists($this, $method)) {
            call_user_func([
                $this,
                $method
            ]);
            return;
        }

        $ids         = array_keys((array)\Input::get('has_many'));
        $model_class = $this->getRelationModel();
        $entity      = explode('\\', $this->getClassModel());
        $key         = \Str::snake(end($entity) . 'Id');

        $model_class::where($key, $this->model->id)->update([
                $key => 0
            ]);
        foreach ($ids as $id) {
            $model = $model_class::find($id);

            if ($model) {
                $model->{$key} = $this->model->id;
                $model->save();
            }
        }
    }

    function traitEntityHasMany_item() {
        if (\Request::method() == 'POST') {
            $this->traitEntityHasMany_save();

            //переоткроем модель
            $model_name = $this->getClassModel();
            if ($model_name::isSoftDelete()){
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
                'message' => sprintf($this->traitEntityHasMany_successMessage(), $model),
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
             ->traitAjax_set('header', (string)$this->traitEntityHasMany_header())
             ->traitAjax_set('body', (string)$this->traitEntityHasMany_body())
             ->traitAjax_set('footer', (string)$this->traitEntityHasMany_footer());

        return $this->traitAjax_response();
    }

}