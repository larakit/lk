<?php
namespace Larakit\Base;

use Illuminate\Support\Arr;
use Larakit\Widget\WidgetFlash;

trait TraitControllerEntityItem {

    function traitEntityItem_construct() {
        $this->traitEntity_assertReason($this->model, 'item');
        $this->addBreadCrumb($this->traitEntity_route() . '.item',
            [
                'model' => $this->model,
                'id'    => $this->model->id,
            ]);
    }

    static function getEntitySuffix() {
        return 'item';
    }

    function traitEntityItem_card() {
        return Accessor::factory($this->model)->card($this->traitEntityItem_fields(),
            static::getSection());
    }

    function traitEntityItem_fields() {
        return array_keys($this->model->toCard());
    }

    function traitEntityItem_responseHtml() {
        return $this->response([
            'body' => $this->traitEntityItem_card()
        ]);
    }

    function traitEntityItem_header() {
        return laralang(static::getVendor() . '::seo/title.admin|' . static::getEntity() . '|item',
            ['model' => $this->model]);
    }

    function traitEntityItem_body() {
        return $this->traitEntityItem_card();
    }

    function traitEntityItem_footer() {
        return \HtmlButton::addClass('js-curtain-close btn btn-default')
                          ->setContent(static::translateAction('item.button'));
    }

    function traitEntityItem_responseJson() {
        $this->traitAjax_set('id', $this->model->id)
             ->traitAjax_set('result', 'curtain')
             ->traitAjax_set('vendor', (string)\Str::snake($this->getVendor()))
             ->traitAjax_set('entity', (string)\Str::snake($this->getEntity()))
             ->traitAjax_set('model', $this->model->toArray())
             ->traitAjax_set('header', (string)$this->traitEntityItem_header())
             ->traitAjax_set('body', (string)$this->traitEntityItem_card())
             ->traitAjax_set('footer', (string)$this->traitEntityItem_footer());
        return $this->traitAjax_response();

    }

    function traitEntityItem_response() {
        if (\Request::ajax()) {
            return $this->traitEntityItem_responseJson();
        }
        return $this->traitEntityItem_responseHtml();
    }

}