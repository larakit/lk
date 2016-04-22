<?php
namespace Larakit\Base;

trait TraitControllerEntityIndexNestable {

    function traitEntityIndex() {
        $this->traitEntity_assertReason($this->model, 'list');

        return $this->layout(true)->response($this->traitEntityIndex_params());
    }

    function traitEntityIndexNestable_params_ext() {
        return [];
    }
    function traitEntityIndex_params() {
        $formfilter_class = $this->getClassFormFilter();
        $filter           = new $formfilter_class($this->model);

        $data = [
            'vendor' => static::getVendor(),
            'model'  => $this->model,
            'entity' => static::getEntity()
        ];

        return array_merge($data, $filter->toArray(), $this->traitEntityIndexNestable_params_ext());
    }

}