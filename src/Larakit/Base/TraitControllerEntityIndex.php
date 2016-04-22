<?php
namespace Larakit\Base;

use Bmmaket\Core\Model\Adv;
use Bmmaket\Core\Model\AdvType;
use Bmmaket\Core\Model\MaketVariant;
use Illuminate\Support\Arr;

trait TraitControllerEntityIndex {

    function traitEntityIndex() {
        $this->traitEntity_assertReason($this->model, 'list');
        return $this->layout(true)->response($this->traitEntityIndex_params());
    }

    function traitEntityIndex_params_ext() {
        return [];
    }

    function traitEntityIndex_params() {
        $formfilter_class = $this->getClassFormFilter();
        $filter           = new $formfilter_class($this->model);
        $data             = [
            'vendor' => static::getVendor(),
            'entity' => static::getEntity()
        ];
        //        $types = Adv::first();
        //        dd($types->adv_type);
        //
        //
        //        $models           = Arr::get($filter->toArray(), 'models');
        //        foreach ($models as $model) {
        //            dd($model->adv_type);
        //        }
        return array_merge($data, $filter->toArray(), $this->traitEntityIndex_params_ext());
    }

}