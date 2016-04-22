<?php
namespace Larakit\Base;

use Illuminate\Support\Arr;
use Larakit\Event;
use Larakit\Manager\ManagerRowType;
use Larakit\Widget\WidgetFlash;

trait TraitControllerEntityAdd {

    function traitEntityAdd_construct() {
        parent::__construct();
        $this->traitEntity_assertReason($this->model, 'add');
        $this->addBreadCrumb($this->traitEntity_route() . '.add');
    }

    static function getEntitySuffix() {
        return 'add';
    }


    function traitEntityAdd_btnText() {
        return static::translateAction('add.button');
    }

    function traitEntityAdd_btnClass() {
        return 'success';
    }

    function traitEntityAdd_successMessage() {
        return static::translateAction('add.success', ['model' => $this->model]);
    }

    function traitEntityAdd_success() {
        if (\Request::ajax()) {
            return $this->traitEntityAddJson_success();
        } else {
            return $this->traitEntityAddHtml_success();
        }
    }

    function traitEntityAdd_formBuild() {
        if (\Request::ajax()) {
            $this->traitEntityAddJson_formBuild();
        } else {
            $this->traitEntityAddHtml_formBuild();
        }
    }

    function traitEntityAdd_formBuildAfter() {
    }

    function traitEntityAdd() {
        //соберем форму
        $this->traitEntityAdd_formBuild();
        $this->traitEntityAdd_formBuildAfter();

        /** @var Model $model_class */
        $model_class = static::getClassModel();
        if ($this->form->isSubmitted()) {
            $validator = $model_class::getDataValidator($this->form->getValue(), $this->form);
            if ($validator->validate()) {
                //запись успешно сохранена
                $this->traitEntity_save($validator);
                //выведем сообщение об успешной вставке
                return $this->traitEntityAdd_success();
            } else {
                //                dd($validator->getErrors());
                \Debugbar::addMessage($validator->getErrors());
                $this->form->setErrors($validator->getErrors());
            }
        } else {
            $fill_labels = $this->model->getFillable();
            foreach ($fill_labels as $fillable) {
                if (null !== \Input::get($fillable)) {
                    $this->form->initValues([
                        $fillable => \Input::get($fillable)
                    ]);
                }
            }

        }


        //форма показана в первый раз или с ошибками
        if (\Request::ajax()) {
            return $this->traitEntityAddJson();
        } else {
            return $this->traitEntityAddHtml();
        }
    }

    //************************************************************
    // HTML
    //************************************************************
    function traitEntityAddHtml_formBuild() {
        $this->traitEntity_formBuild();
        $this->form->putTbSubmit($this->traitEntityAdd_btnText())
                   ->addClass('btn btn-' . $this->traitEntityAdd_btnClass());
    }

    function traitEntityAddHtml_Success() {
        WidgetFlash::success(sprintf($this->traitEntityAdd_successMessage(), $this->model));
        return \Redirect::route($this->traitEntity_route());
    }

    function traitEntityAddHtml() {
        return $this->response(['body' => $this->form]);
    }

    //************************************************************
    // JSON
    //************************************************************
    function traitEntityAddJson_formBuild() {
        $this->traitEntity_formBuild();
    }

    function traitEntityAddJson_success() {
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
            'entity'  => \Str::snake($this->getEntity()),
            'message' => sprintf($this->traitEntityAdd_successMessage(), $this->model),
        ];

        Event::notify('larakit::model_add-admin', $data);
        $this->traitAjax_set($data);
        return $this->traitAjax_response();
    }

    function traitEntityAdd_header() {
        return laralang(static::getVendor() . '::seo/title.admin|' . static::getEntity() . '|add');
    }

    function traitEntityAdd_body() {
        return $this->form;
    }

    function traitEntityAdd_footer() {
        return \HtmlButton::addClass('js-curtain-submit btn btn-' . $this->traitEntityAdd_btnClass())
                          ->setContent($this->traitEntityAdd_btnText());
    }

    function traitEntityAddJson() {
        $this->traitAjax_set('id', $this->model->id)
             ->traitAjax_set('result', 'curtain')
             ->traitAjax_set('vendor', (string)static::getVendor())
             ->traitAjax_set('entity', (string)\Str::snake($this->getEntity()))
             ->traitAjax_set('model', $this->model->toArray())
             ->traitAjax_set('header', (string)$this->traitEntityAdd_header())
             ->traitAjax_set('body', (string)$this->traitEntityAdd_body())
             ->traitAjax_set('footer', (string)$this->traitEntityAdd_footer());
        return $this->traitAjax_response();
    }


}