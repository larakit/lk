<?php
namespace Larakit\Base;

use Illuminate\Support\Arr;
use Larakit\Event;
use Larakit\Manager\ManagerRowType;
use Larakit\QuickForm\LaraForm;
use Larakit\User\Me;
use Larakit\Widget\WidgetFlash;
use Realplexor\Rpl;

trait TraitControllerEntityItemAttach {

    /**
     * @var LaraForm
     *
     */

    function traitEntityAttach_construct() {
        $this->traitEntity_assertReason($this->model, 'attach');
        $this->addBreadCrumb($this->traitEntity_route() . '.item.attach', ['model' => $this->model]);
    }

    static function getEntitySuffix() {
        return 'item_attach';
    }


    function traitEntityAttach_btnText() {
        return static::translate('actions.item|edit.button', ['model' => $this->model]);
    }

    function traitEntityAttach_btnClass() {
        return 'primary';
    }

    function traitEntityAttach_successMessage() {
        return static::translate('actions.item|edit.success', ['model' => $this->model]);
    }

    function traitEntityAttach_success() {
        if (\Request::ajax()) {
            return $this->traitEntityAttachJson_success();
        } else {
            return $this->traitEntityAttachHtml_success();
        }
    }

    function traitEntityAttach_formBuild() {
        if (\Request::ajax()) {
            $this->traitEntityAttachJson_formBuild();
        } else {
            $this->traitEntityAttachHtml_formBuild();
        }
    }

    function traitEntityAttach() {
        //соберем форму
        $this->traitEntityAttach_formBuild();
        $this->form->initValues($this->model->toForm());
        if ($this->form->isSubmitted()) {

            $model_class = static::getClassModel();

            $data       = $this->form->getValue();
            $data['id'] = $this->model->id;
            $validator  = $model_class::getDataValidator($data, $this->form);
            if ($validator->validate()) {
                //запись успешно сохранена
                $this->traitEntity_save($validator);
                //выведем сообщение об успешной вставке
                return $this->traitEntityAttach_success();
            } else {
                $this->form->setErrors($validator->getErrors());
            }


            //запись успешно сохранена
            //            $this->traitEntity_save();
            //            выведем сообщение об успешной вставке
            //            return $this->traitEntityAttach_success();
        }
        //форма показана в первый раз или с ошибками
        if (\Request::ajax()) {
            return $this->traitEntityAttachJson();
        }
        return $this->traitEntityAttachHtml();
    }

    //************************************************************
    // HTML
    //************************************************************
    function traitEntityAttachHtml_formBuild() {
        $this->traitEntity_formBuild();
        $this->form->putTbSubmit($this->traitEntityAttach_btnText())
                   ->addClass('btn btn-' . $this->traitEntityAttach_btnClass());
    }

    function traitEntityAttachHtml_Success() {
        WidgetFlash::success(sprintf($this->traitEntityAttach_successMessage(), $this->model));
        return \Redirect::route($this->traitEntity_route());
    }

    function traitEntityAttachHtml() {
        return $this->response(['body' => (string)$this->form]);
    }

    //************************************************************
    // JSON
    //************************************************************
    function traitEntityAttachJson_formBuild() {
        $this->traitEntity_formBuild();
    }

    function traitEntityAttachJson_success() {
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
            'message' => sprintf($this->traitEntityAttach_successMessage(), $model),
        ];
        Event::notify('larakit::model_edit-admin', $data);
        $this->traitAjax_set($data);
        return $this->traitAjax_response();
    }

    function traitEntityAttach_header() {
        return laralang(static::getVendor() . '::seo/title.admin|' . static::getEntity() . '|item_edit');
    }

    function traitEntityAttach_body() {
        return $this->form;
    }

    function traitEntityAttach_footer() {
        return \HtmlButton::addClass('js-curtain-submit btn btn-' . $this->traitEntityAttach_btnClass())
                          ->setContent($this->traitEntityAttach_btnText());
    }


    function traitEntityAttachJson() {
        $this->traitAjax_set('id', $this->model->id)
             ->traitAjax_set('result', 'curtain')
             ->traitAjax_set('vendor', (string)static::getVendor())
             ->traitAjax_set('entity', (string)\Str::snake($this->getEntity()))
             ->traitAjax_set('model', $this->model->toArray())
             ->traitAjax_set('header', (string)$this->traitEntityAttach_header())
             ->traitAjax_set('body', (string)$this->traitEntityAttach_body())
             ->traitAjax_set('footer', (string)$this->traitEntityAttach_footer());
        return $this->traitAjax_response();
    }


}