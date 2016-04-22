<?php
namespace Larakit\Base;

use Bmmaket\Core\FormFilter\FormFilterMaket;
use Bmmaket\Core\Model\Maket;
use Illuminate\Support\Arr;
use Larakit\Event;
use Larakit\Exception;
use Larakit\Manager\ManagerRowType;
use Larakit\QuickForm\LaraForm;
use Larakit\User\Me;
use Larakit\Widget\WidgetFlash;
use Realplexor\Rpl;

trait TraitControllerEntityItemEdit {

    /**
     * @var LaraForm
     *
     */

    function traitEntityEdit_construct() {
        $this->traitEntity_assertReason($this->model, 'edit');
        $this->addBreadCrumb($this->traitEntity_route() . '.item.edit', ['model' => $this->model]);
    }

    static function getEntitySuffix() {
        return 'item_edit';
    }


    function traitEntityEdit_btnText() {
        return static::translateAction('item|edit.button', ['model' => $this->model]);
    }

    function traitEntityEdit_btnClass() {
        return 'primary';
    }

    function traitEntityEdit_successMessage() {
        return static::translateAction('item|edit.success', ['model' => $this->model]);
    }

    function traitEntityEdit_success() {
        if (\Request::ajax()) {
            return $this->traitEntityEditJson_success();
        } else {
            return $this->traitEntityEditHtml_success();
        }
    }

    function traitEntityEdit_formBuild() {
        if (\Request::ajax()) {
            $this->traitEntityEditJson_formBuild();
        } else {
            $this->traitEntityEditHtml_formBuild();
        }
    }

    function traitEntityEdit_formBuildAfter(){

    }

    function traitEntityEdit() {
        //соберем форму
        $this->traitEntityEdit_formBuild();
        $this->form->initValues($this->model->toForm());
        $this->traitEntityEdit_formBuildAfter();
        if ($this->form->isSubmitted()) {

            $model_class = static::getClassModel();

            $data       = $this->form->getValue();
            $data['id'] = $this->model->id;
            $validator  = $model_class::getDataValidator($data, $this->form);
//            dd($data);
            if ($validator->validate()) {
                //запись успешно сохранена
                $this->traitEntity_save($validator);
                //выведем сообщение об успешной вставке
                return $this->traitEntityEdit_success();
            } else {
                \Debugbar::addMessage($validator->getErrors());
                $this->form->setErrors($validator->getErrors());
                //$this->form->putTbStatic(\HtmlDiv::addClass('alert alert-danger')->setContent($validator->getErrors(true)));
            }


            //запись успешно сохранена
            //            $this->traitEntity_save();
            //            выведем сообщение об успешной вставке
            //            return $this->traitEntityEdit_success();
        }
        //форма показана в первый раз или с ошибками
        if (\Request::ajax()) {
            return $this->traitEntityEditJson();
        }
        return $this->traitEntityEditHtml();
    }

    //************************************************************
    // HTML
    //************************************************************
    function traitEntityEditHtml_formBuild() {
        $this->traitEntity_formBuild();
        $this->form->putTbSubmit($this->traitEntityEdit_btnText())
                   ->addClass('btn btn-' . $this->traitEntityEdit_btnClass());
    }

    function traitEntityEditHtml_Success() {
        WidgetFlash::success(sprintf($this->traitEntityEdit_successMessage(), $this->model));
        return \Redirect::route($this->traitEntity_route());
    }

    function traitEntityEditHtml() {
        return $this->response(['body' => (string)$this->form]);
    }

    //************************************************************
    // JSON
    //************************************************************
    function traitEntityEditJson_formBuild() {
        $this->traitEntity_formBuild();
    }

    function traitEntityEditJson_success() {
        $class_model = get_class($this->model);
        $model       = $class_model::findOrFail($this->model->id);

        $row_types = ManagerRowType::get(ManagerRowType::makeKey(static::getVendor(), static::getEntity()));
        $rows = [];
        foreach($row_types as $row_type){
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
            'message' => sprintf($this->traitEntityEdit_successMessage(), $model),
        ];

        Event::notify('larakit::model_edit-admin', $data);
        $this->traitAjax_set($data);
        return $this->traitAjax_response();
    }

    function traitEntityEdit_header() {
        return laralang(static::getVendor() . '::seo/title.admin|' . static::getEntity() . '|item_edit');
    }

    function traitEntityEdit_body() {
        return $this->form;
    }

    function traitEntityEdit_footer() {
        return \HtmlButton::addClass('js-curtain-submit btn btn-' . $this->traitEntityEdit_btnClass())
                          ->setContent($this->traitEntityEdit_btnText());
    }


    function traitEntityEditJson() {
        $this->traitAjax_set('id', $this->model->id)
             ->traitAjax_set('result', 'curtain')
             ->traitAjax_set('vendor', (string)static::getVendor())
             ->traitAjax_set('entity', (string)\Str::snake($this->getEntity()))
             ->traitAjax_set('model', $this->model->toArray())
             ->traitAjax_set('header', (string)$this->traitEntityEdit_header())
             ->traitAjax_set('body', (string)$this->traitEntityEdit_body())
             ->traitAjax_set('footer', (string)$this->traitEntityEdit_footer());
        return $this->traitAjax_response();
    }


}