<?php
namespace Larakit\Base;

Class ObserverModelNestable {

    //    public function creating($model) {
    /*$model->order = (int)$model->order;
    if (!$model->order) {
        $model_class  = get_class($model);
        $model->order = (int)$model_class::where('parent_id', '=', 0)->max('order') + 1;
    }

    $model->calc_path = $model_class::calcPath($model->parent_id, $model->order);
    $model->calc_name = (string)$model;
    $model->parent_id = (int)$model->parent_id;
//        $model->save();*/

    //        $model->calc_name = (string)$model->name;
    //
    //        return $model;
    //    }

    function reCalcTree($model) {
        $model->parent_id = (int)$model->parent_id;
        $model->order     = (int)$model->order;
        /** @var Model $model_class */
        $model_class = get_class($model);
        if ($model->parent_id > 0) {
            $parent           = $model_class::find($model->parent_id);
            $model->calc_path = $parent->calc_path . '.' . $model_class::calcPath($model->parent_id, $model->order);
            $model->calc_name = $parent->calc_name . ' / ' . $model->name;
            $model->calc_slug = $parent->calc_slug . '/' . $model->slug;
        }
        else {
            $model->calc_path = $model_class::calcPath($model->parent_id, $model->order);
            $model->calc_name = $model->name . '';
            $model->calc_slug = $model->slug . '';
        }


        $models = $model_class::lists('calc_slug', 'id');
        $name = $model_class::getVendor() . '__'.$model_class::getEntity();
        file_put_contents(storage_path('_nestable'.$name), '<?php '.PHP_EOL.'return '.var_export($models, true).';');

        return $model;
    }

    public function saving($model) {
        $this->reCalcTree($model);
        //        $model_class =
    }

    //    public function created($model) {
    //        $class = get_class($model);
    //        $url   = route(
    //            'larakit::ajax.admin.nestable',
    //            [
    //                'vendor' => $class::getVendor(),
    //                'entity' => $class::getEntity()
    //            ]
    //        );
    //        file_get_contents($url);
    //    }

    /*   public function updatin1g($model) {
           $model->parent_id = (int)$model->parent_id;
           $model->order     = (int)$model->order;
           $model_class      = get_class($model);
           if ($model->parent_id > 0) {
               $parent           = $model_class::find($model->parent_id);
               $model->calc_path = $parent->calc_path . '.' . $model_class::calcPath($model->parent_id, $model->order);
               $model->calc_name = $parent->calc_name . ' / ' . $model;
           } else {
               $model->calc_path = $model_class::calcPath($model->parent_id, $model->order);
               $model->calc_name = $model . '';
           }

           return $model;
       }*/

    public function deleting($model) {
        /** @var Model $model_class */
        $model_class = get_class($model);
        $children    = $model_class::where('parent_id', '=', $model->id)
                                   ->get();
        foreach ($children as $child) {
            $child->parent_id = 0;
            $child->save();
        }
    }

}