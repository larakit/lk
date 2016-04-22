<?php
namespace Larakit\Base;

Trait TraitModelNestable {

    static $tree = [];

    static function bootTraitModelNestable() {
        /** @var Model $class */
        $class = get_called_class();
        $class::observe(new ObserverModelNestable());
    }

    static function traitModelNestableFieldName() {
        return 'name';
    }

    static function getTree() {
        $class = get_called_class();
        if (isset(static::$tree[$class])) {
            return static::$tree[$class];
        } else {
            $models = $class::orderBy('calc_path');
            $tree   = [];
            foreach ($models->get() as $model) {
                $tree[(int)$model->parent_id][] = $model;
            }
            static::$tree[$class] = $tree;

            return $tree;
        }
    }

    static function calcPath($parent_id, $order) {
        /** @var Model $model_class */
        $length = 5;
        $path   = str_pad((int)$parent_id, $length, '0', STR_PAD_LEFT);
        $path .= str_pad((int)$order, $length, '0', STR_PAD_LEFT);

        return $path;
    }


    protected $trait_model_nestable_tree = [];
    protected $trait_model_nestable_flat = [];


    static function recalcPaths() {
        $tree = static::getTree();
        dd($tree);
    }

    function rowTpl($row) {
        $tpl = static::getVendor() . '::!.partials.' . static::getEntity() . '.row.' . $row;
        return $tpl;
    }

}