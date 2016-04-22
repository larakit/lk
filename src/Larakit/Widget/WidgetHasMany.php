<?php
namespace Larakit\Widget;

use Illuminate\Support\Arr;
use Larakit\Base\Accessor;
use Larakit\Base\TraitEntity;
use Larakit\Route\Route;

class WidgetHasMany extends \Larakit\Base\Widget {
    use TraitEntity;

    protected $model;

    function setModel($model) {
        $this->model         = $model;
        $belongsto_class     = get_class($model);
        $belongsto['model']  = $model;
        $belongsto['class']  = $belongsto_class;
        $belongsto['entity'] = $belongsto_class::getEntity();
        $belongsto['vendor'] = $belongsto_class::getVendor();
        $this->set('belongsto', $belongsto);;
        return $this;
    }

    function setRelations($relations) {
        if (false === $relations) {
            return $this;
        }
        $ret = [];
        foreach ($relations as $relation) {
            Arr::set($ret, $relation, []);
        }
        $relations = $ret;
        $accessor  = Accessor::factory($this->model);
        foreach ($relations as $relation => $subrelation) {
            $method = \Str::camel('hasmany_' . $relation);
            if (method_exists($accessor, $method)) {
                $data = call_user_func([$accessor, $method]);
                $this->addRelation($relation, $data, $subrelation);
            }
        }
        return $this;
    }

    function addRelation($relation, $data, $subrelation) {
        $has_many   = Arr::get($data, 'has_many', $relation);
        $title      = Arr::get($data, 'title');
        $js_filters = (array)Arr::get($data, 'js_filters');
        $base_route = Arr::get($data, 'base_route');
        $row_type   = Arr::get($data, 'row_type', 'admin');
        try {
            $r = $this->model->{$has_many}();
            if (!is_object($r)) {
                throw new \Exception('Нет связи ' . $has_many);
            }
            $relation_model_class = get_class($r->getRelated());
            //            $this->add('errors', $relation_model_class);

            $ret['relation_model_class'] = $relation_model_class;
            $ret['models']               = $this->model->{$relation};

            $ret['title'] = $title ? $title : $relation_model_class::getEntityNameCasePlural();
            //            $this->add('errors', $ret['name']);

            $ret['js_filters'] = implode(' ', $js_filters);
            //            $this->add('errors', $section);
//            if($base_route){
//                $ret['entity']     = $relation_model_class::getEntity();
//                $ret['vendor']     = $relation_model_class::getVendor();
//            } else {
                $ret['entity']   = Arr::get($data, 'entity', $relation_model_class::getEntity());
                $ret['vendor']   = Arr::get($data, 'vendor', $relation_model_class::getVendor());
//            }
            $ret['with']       = array_keys($subrelation);
            $ret['base_route'] = $base_route;
            $ret['row_type']   = $row_type;
            $belongsto_class   = $this->get('belongsto.class');
            $model             = $this->get('belongsto.model');
            $route_name        = $base_route . '.add';
            if (\Route::has($route_name)) {
                if (!$relation_model_class::getAcl()->reason('add')) {
                    $ret['add_url'] = \URL::route($route_name,
                        [
                            $belongsto_class::getEntity() . '_id' => $model->id
                        ]);
                }
            }
            $this->add('relations', $ret);
        } catch (\Exception $e) {
            $this->add('errors', [$relation, $data, $subrelation]);
            $this->add('errors', $e->getMessage());
        }
        return $this;
    }

    function tpl() {
        return 'larakit::!.widgets.has_many';
    }


}