<?php
namespace Larakit\Base;

Class NestableObserve {

    public function creating($model) {
        $model->order = (int)$model->order;
        if (!$model->order) {
            /** @var Model $model_class */
            $model_class  = get_class($model);
            $model->order = (int)$model_class::where('parent_id', '=', 0)->max('order')+1;
        }

        $model->calc_path = $this->calcFlatPath($model);
        $model->calc_name = (string)$model;
        $model->parent_id = (int)$model->parent_id;

        return $model;
    }

    //    public function updating($model) {
    //        $model->parent_id = (int)$model->parent_id;
    //        $model->order     = (int)$model->order;
    //        if ($model->parent_id > 0) {
    //            /** @var Model $model_class */
    //            $model_class      = get_class($model);
    //            $parent           = $model_class::find($model->parent_id);
    //            $model->calc_path = $parent->calc_path . '.' . $this->calcFlatPath($model);
    //        } else {
    //            $model->calc_path = $this->calcFlatPath($model);
    //        }
    //
    //        return $model;
    //    }

    public function deleting($model) {
        /** @var Model $model_class */
        $model_class = get_class($model);
        $children    = $model_class::where('parent_id', '=', $model->id)->get();
        foreach ($children as $child) {
            $child->parent_id = 0;
            $child->save();
        }
    }


    protected function calcFlatPath($model) {
        /** @var Model $model_class */
        $length = 5;
        $path   = str_pad((int)$model->parent_id, $length, '0', STR_PAD_LEFT);
        $path .= str_pad((int)$model->order, $length, '0', STR_PAD_LEFT);
        return $path;
    }


}