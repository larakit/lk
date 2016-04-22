<?php
namespace Larakit\Base;

use Larakit\Exception;
use Larakit\Helper\HelperEntity;
use Larakit\Model\Tag;
use Larakit\Model\User;
use Larakit\QuickForm\LaraForm;
use Larakit\Widget\WidgetSimpleAjaxUploader;

trait TraitControllerEntity {
    use TraitEntity;

    function traitEntity_notFoundException() {
        return 'Запись не найдена';
    }

    static function getEntityPrefix() {
        return 'controller_admin';
    }

    static function getRoutePrefix() {
        return 'admin';
    }

    function traitEntity_modelById($model_name, $id) {
        if (static::isSoftDelete()) {
            return $model_name::withTrashed()->find($id);
        } else {
            return $model_name::find($id);
        }
    }

    function traitEntity_construct() {
        parent::__construct();
        $id          = (int)\Route::input('id');
        $model = HelperEntity::getModelClass($this);

        $model_name  = $this->getClassModel();
        $this->model = ($id ? $this->traitEntity_modelById($model_name, $id) : new $model_name);
        if ($id && !$this->model) {
            throw new Exception($this->traitEntity_notFoundException());
        }
        $route = $this->traitEntity_route();
        $this->addBreadCrumb($route,
            [
                'model' => isset($this->model->id) ? (string)$this->model : '',
                'id'    => $this->model->id,
            ]);

    }

    function traitEntity_route() {
        $route = static::getVendor();
        $route .= '::';
        //        if(substr_count(static::getEntityPrefix(), 'admin')){
        //            $route .= 'admin.';
        //        }
        $route .= static::getRoutePrefix() ? (static::getRoutePrefix() . '.') : '';
        $route .= static::getEntity();

        return $route;
    }


    function traitEntity_save(DataValidator $validator) {
        $this->model->fill($validator->getData());
        $this->model->save();
        $this->model->touch();
    }

    function traitEntity_fieldTags() {
        $name     = Tag::fieldName($this->model);
        $options  = $this->model->tags->lists('name', 'name');
        $examples = Tag::examples();
        $this->form->putTbSelect2($name)
                   ->setLabel('Метки')
                   ->setDesc('Кратко опишите объект несколькими словами, например: ' . implode(', ', $examples))
                   ->setPrepend('<i class="fa fa-tags"></i>')
                   ->setAutoCompleteUrl(route('larakit::ajax.tags'))
                   ->setTags()
                   ->loadOptions($options);
        $this->form->initValues([
            $name => $options
        ]);
    }

    function traitEntity_fieldUpload($name) {
        $thumb_data = [
            'vendor' => static::getVendor(),
            'entity' => static::getEntity(),
            'id'     => 0,
            'name'   => $name
        ];
        $widget     = WidgetSimpleAjaxUploader::factory($name)
                                              ->setIdentity('tmp_upload_' . $name)
                                              ->setUrlUpload(\URL::route('larakit::thumb_name_tmp', $thumb_data))
                                              ->setMaxSize('500000')
                                              ->setMaxUploads(1);
        $this->form->putTbStatic($widget);
    }

    /*
        function traitEntity_fieldUploadMp3($name) {
            $thumb_data = [
                'vendor' => static::getVendor(),
                'entity' => static::getEntity(),
                'id'     => 0,
                'name'   => $name
            ];
            $widget     = WidgetSimpleAjaxUploader::factory($name)
                                                  ->setIdentity('tmp_upload_' . $name)
                                                  ->setUrlUpload(\URL::route('larakit::thumb_name_tmp', $thumb_data))
                                                  ->setMaxSize('500000')
                                                  ->setAccept('audio/mpeg')
                                                  ->setAllowedExt('mp3')
                                                  ->setMaxUploads(1);
            $this->form->putTbStatic($widget);
        }*/


    function traitEntity_assertReason($model, $action) {
        $reason = Acl::factory($model)->reason($action);
        if ($reason) {
            $e = new Exception($reason);
            throw $e->setTitle('Действие запрещено!');
        }
    }

    function traitEntity_formFields() {
        $type   = \Input::get('type');
        $method = 'traitEntity_formFields' . \Str::studly($type);
        \Debugbar::addMessage($method);
        if ($type && method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }
        $hiddens = $this->model->getHidden();
        $ret     = [];
        foreach ($this->model->getFillable() as $f) {
            if (!in_array($f, $hiddens)) {
                $ret[] = $f;
            }
        }
        return $ret;
    }


    /**
     * Метод для переопределения - добавляем в форму поля, доступные для заполнения в модели
     *
     * @param $form_builder \Larakit\QuickForm\LaraFormBuilder
     */
    function traitEntity_formAddFields($form) {
        foreach ($this->traitEntity_formFields() as $k => $v) {
            if (!is_array($v)) {
                $form->{\Str::camel('add_' . $v)}($form);
            } else {
                $f = $form->putTbFieldset($k);
                foreach ($v as $field) {
                    $form->{\Str::camel('add_' . $field)}($f);
                }
            }
        }
        //        if(method_exists($this->model, 'tags')){
        //            $form->putTbSelect2('tags')->setLabel('Метки')->setDesc('Через запятую')->loadOptions(\Larakit\Model\Tag::lists('name', 'name'));
        //        }
    }

    /**
     * Сборка формы для модели
     */
    function traitEntity_formBuild() {
        $form_class = $this->getClassForm();
        $form       = new $form_class(static::getEntity());
        $form->setModel($this->model);
        $form->setAction($_SERVER['REQUEST_URI']);
        $form->setAction(\Request::getUri());
        //        dd($form);
        //::factory($this->model)
        //билдим форму, если надо - переопределяем метод "traitEntityFormBuild"
        $this->traitEntity_formAddFields($form);
        $form->addRecursiveFilter('trim');
        //        $form_builder->form->setVendor($this->getVendor())
        //                           ->setEntity($this->getEntity())
        //                           ->setDataId(isset($this->model->id) ? $this->model->id : 0);
        $this->form = $form;
        $this->traitEntity_formBuildAfter();
    }

    function traitEntity_formBuildAfter() {
    }


}