<?php
namespace Larakit\CRUD;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Larakit\Exception;
use Larakit\QuickForm\LaraForm;

class FormFilter {

    /**
     * @var \Eloquent
     */
    protected $model;
    /**
     * @var LaraForm
     */
    protected $form;
    protected $js_filters      = [];
    protected $title           = 'Фильтр';
    protected $base_url        = '';
    protected $per_page        = 20;
    protected $tpl             = 'larakit::!.formfilter';
    protected $with_trash      = false;
    protected $query_with      = [];
    protected $query_where_has = [];

    /**
     * @param $model
     *
     * @return FormFilter
     */
    //    static function factory($model) {
    //        if (is_string($model)) {
    //            $model = new $model();
    //        }
    //        $model_name = get_class($model);
    //        $class = $model_name::getClassFormFilter();
    //        $class = str_replace('\Model\\', '\FormFilter\\FormFilter', get_class($model));
    //        if (!class_exists($class)) {
    //            throw new Exception('Отсутствует класс фильтров ' . $class);
    //        }
    //        return new $class($model);
    //    }

    function __construct($model) {
        $this->model    = $model::select();
        $this->base_url = \URL::current();
        $this->form     = new LaraForm('filter_' . $this->model->getModel()->getEntity(), 'get');
    }

//    function useWith($path, $callback) {
//        $args                      = func_get_args();
//        $params                    = array_splice($args, 2);
//        $this->query_with[$path][] = [
//            'callback' => $callback,
//            'params'   => $params,
//        ];
//    }

//    protected $raw_with = [];
//
//    function addWith($with) {
//        $with = (array) $with;
//        foreach($with as $w) {
//            $this->raw_with[$w] = $w;
//        }
//
//        return $this;
//    }
//
//    function useWhereHas($path, $callback = null) {
//        $args                           = func_get_args();
//        $params                         = array_splice($args, 2);
//        $this->query_where_has[$path][] = [
//            'callback' => $callback,
//            'params'   => $params,
//        ];
//    }

    function init() {
        //        $this->addFilterLike('name', 'Название');
    }

    function addJsFilter($filter) {
        $this->js_filters[$filter] = $filter;

        return $this;
    }

    function addFilterInCheckboxButton($form_field, $label, array $options, array $js_filters, array $default = [1 => 1], $db_field = null) {
        if(!$db_field) {
            $db_field = $form_field;
        }
        $this->form->putTbGroupCheckboxButton($form_field, $options)
            ->setLabel($label);
        $this->form->initValues([$form_field => $default]);
        $values = (array) \Input::get($form_field, $default);
        if(count($values)) {
            $this->model->where(function ($query) use ($values, $options, $db_field, $js_filters) {
                $filters = [];
                foreach($options as $k => $label) {
                    if(in_array($k, $values)) {
                        $filters[] = Arr::get($js_filters, $k);
                        if(is_array($db_field)) {
                            $condition = Arr::get($db_field, $k);
                            $query->orWhere($condition);
                        } else {
                            $query->orWhere($db_field, '=', $k);
                        }
                    }
                }
                $this->addJsFilter(implode('|', $filters));
            });
        }

    }

    function addFilterInCheckboxGroup($form_field, $label, array $options, array $default = [], $db_field = null) {
        if(!$db_field) {
            $db_field = $form_field;
        }
        $this->form->putTbGroupCheckbox($form_field, $options)
            ->setLabel($label);
        $this->form->initValues([$form_field => $default]);
        $values = (array) \Input::get($form_field, $default);
        if(count($values)) {
            $this->model->where(function ($query) use ($values, $options, $db_field) {
                $filters = [];
                foreach($options as $k => $label) {
                    if(in_array($k, $values)) {
                        $filters[] = $db_field . '_' . $k;
                        if(is_array($db_field)) {
                            $condition = Arr::get($db_field, $k);
                            $query->orWhere($condition);
                        } else {
                            $query->orWhere($db_field, '=', $k);
                        }
                    }
                }
                $this->addJsFilter(implode('|', $filters));
            });
        }

    }

    function addFilterInSelect2($form_field, $label, array $options, array $default = [], $db_field = null) {
        if(!$db_field) {
            $db_field = $form_field;
        }
        $this->form->putTbSelect2($form_field)
            ->loadOptions($options)
            ->setMultiple()
            ->setLabel($label);
        $this->form->initValues([$form_field => $default]);
        $values = (array) \Input::get($form_field, $default);
        if(count($values)) {
            $this->model->where(function ($query) use ($values, $options, $db_field) {
                $filters = [];
                foreach($options as $k => $label) {
                    if(in_array($k, $values)) {
                        $filters[] = $db_field . '_' . $k;
                        if(is_array($db_field)) {
                            $condition = Arr::get($db_field, $k);
                            $query->orWhere($condition);
                        } else {
                            $query->orWhere($db_field, '=', $k);
                        }
                    }
                }
                $this->addJsFilter(implode('|', $filters));
            });
        }

    }

    function addFilterManyToMany($relation, $label, array $options, array $default = []) {
        $this->form->putTbSelect2($relation)
            ->loadOptions($options)
            ->setMultiple()
            ->setLabel($label);
        $this->form->initValues([$relation => $default]);
        $values = (array) \Input::get($relation, $default);
        $key    = $this->model->getRelation($relation)
            ->getOtherKey();
        if(count($values)) {
            $this->model->with($relation);
            $this->model->whereHas($relation, function ($query) use ($values, $options, $relation, $key) {
                $filters = [];
                $query->whereIn($key, $this->ids($values));
                foreach($options as $k => $label) {
                    if(in_array($k, $values)) {
                        $filters[] = $relation . '_' . $k;
                        //                        $query->orWhere('id', '=', $k);
                    }
                }
                //                dd();
                //                dd($this->model->listsExt());
                //                dd($key, $this->ids($values), $values, $options, $relation);
                $this->addJsFilter(implode('|', $filters));
            });
        }

    }

    function addFilterLike($form_field, $label, $db_field = null) {
        if(!$db_field) {
            $db_field = $form_field;
        }
        $this->form->putTbText($form_field)
            ->setLabel($label);
        $value = \Input::get($form_field);
        if($value) {
            $this->model->where(function ($query) use ($db_field, $value) {
                $query->where($db_field, 'like', '%' . $value . '%');
            });
        }
    }

    function addFilterEqual($form_field, $label, $db_field = null) {
        if(!$db_field) {
            $db_field = $form_field;
        }
        $this->form->putTbText($form_field)
            ->setLabel($label);
        $value = \Input::get($form_field, '');
        if('' != $value) {
            $this->model->where(function ($query) use ($db_field, $value) {
                $query->where($db_field, '=', $value);
            });
        }
    }

    /**
     * Фильтр: диапазон чисел
     *
     * @param      $form_field
     * @param      $label
     * @param null $db_field
     */
    function addFilterRangeNumeric($form_field, $label, $db_field = null) {
        if(!$db_field) {
            $db_field = $form_field;
        }
        $gr = $this->form->putTbGroup($form_field)
            ->setLabel($label);
        $gr->putTbInt('from')
            ->setLabel('С')
            ->setWrapClass('col-md-6');
        $gr->putTbInt('to')
            ->setLabel('по')
            ->setWrapClass('col-md-6');
        $from = (float) \Input::get($form_field . '.from');
        $to   = (float) \Input::get($form_field . '.to');
        if($from) {
            $this->model->where(function ($query) use ($db_field, $from) {
                $query->where($db_field, '>=', $from);
            });
        }
        if($to) {
            $this->model->where(function ($query) use ($db_field, $to) {
                $query->where($db_field, '<=', $to);
            });
        }
    }

    /**
     * Фильтр: диапазон чисел
     *
     * @param      $form_field
     * @param      $label
     * @param null $db_field
     */
    function addFilterRangeNumericSlider($form_field, $label, $min = null, $max = null, $db_field = null) {
        if(!$db_field) {
            $db_field = $form_field;
        }
        larastatic('bootstrap-slider');
        $value     = \Input::get($form_field, $min . ',' . $max);
        $el        = $this->form->putTbSlider($form_field)
            ->setLabel($label)
            ->setValue($value)
            ->setOptionMax($max)
            ->setOptionMin($min);
        $gr        = $this->form->putTbGroup()
            ->setAttribute('rel', $el->getId())
            ->addClass('js-slider-group');
        $from_name = 'from_' . $form_field;
        $to_name   = 'to_' . $form_field;
        $gr->putTbInt($from_name)
            ->setWrapClass('col-md-6')
            ->addClass('js-slider-min');
        $gr->putTbInt($to_name)
            ->setWrapClass('col-md-6')
            ->addClass('js-slider-max');
        $values = explode(',', $value);
        $this->form->initValues([
            $from_name => Arr::get($values, 0),
            $to_name   => Arr::get($values, 1),
        ]);
        \LaraJs::addInline('');

        $value = \Input::get($form_field);
        if($value) {
            $values = explode(',', $value);
            $values = $this->ids($values);
            $this->model->whereBetween($db_field, $values);
        }

    }

    /**
     * Фильтр: диапазон дат
     *
     * @param      $form_field
     * @param      $label
     * @param null $db_field
     */
    function addFilterRangeDate($form_field, $label, $db_field = null) {
        if(!$db_field) {
            $db_field = $form_field;
        }
        $gr = $this->form->putTbGroup($form_field)
            ->setLabel($label);
        $gr->putTbDate('from')
            ->setLabel('С')
            ->setWrapClass('col-md-6')
            ->setAppendClear();
        $gr->putTbDate('to')
            ->setLabel('по')
            ->setWrapClass('col-md-6')
            ->setAppendClear();
        $from = \Input::get($form_field . '.from');
        $to   = \Input::get($form_field . '.to');
        if($from) {
            $this->model->where(function ($query) use ($db_field, $from) {
                $query->where($db_field, '>=', \Carbon\Carbon::parse($from)
                    ->format('Y-m-d H:i:s'));
            });
        }
        if($to) {
            $this->model->where(function ($query) use ($db_field, $to) {
                $query->where($db_field, '<=', \Carbon\Carbon::parse($to)
                    ->format('Y-m-d H:i:s'));
            });
        }
    }

    function getJsFilters() {
        return array_values($this->js_filters);
    }

    function setBaseUrl($base_url) {
        $this->base_url = $base_url;

        return $this;
    }

    function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    function setWithTrash($with_trash = true) {
        $this->with_trash = (bool) $with_trash;

        return $this;
    }

    function setPerpage($per_page) {
        $this->per_page = $per_page;

        return $this;
    }

    function setTemplate($tpl) {
        $this->tpl = $tpl;

        return $this;
    }


    //    function getEntity() {
    //        $r   = new \ReflectionClass($this);
    //        $str = \Str::snake(str_replace($r->getNamespaceName() . '\\', '', get_called_class()));
    //        return mb_substr($str, strpos($str, '_', 6) + 1);
    //    }

    function getForm() {
        $this->form->putTbSubmit('Применить')
            ->addClass('btn-success');
        $this->form->putTbLinkButton($this->base_url, 'Сброс')
            ->addClass('btn-default');

        return \View::make($this->tpl, [
            'title' => $this->title,
            'form'  => $this->form->__toString(),
        ])
            ->__toString();
    }

    function ids($values) {
        return array_map(function ($item) {
            return (int) $item;
        }, $values);
    }

    function applyWhereHas() {
        ksort($this->query_where_has);
        foreach($this->query_where_has as $path => $callbacks) {
            $this->model->with($path);

            $this->model->whereHas($path, function ($query) use ($callbacks) {
                foreach($callbacks as $c) {
                    $callback = Arr::get($c, 'callback');
                    $params   = Arr::get($c, 'params', []);
                    call_user_func_array($callback, array_merge([$query], (array) $params));

                }
            });
        }
    }

    function applyWith() {
        $with = $this->raw_with;
        foreach($this->query_with as $path => $callbacks) {
            $with[$path] = function ($query) use ($callbacks) {
                foreach($callbacks as $c) {
                    $callback = Arr::get($c, 'callback');
                    $params   = Arr::get($c, 'params', []);
                    call_user_func_array($callback, array_merge([$query], $params));
                }
            };
        }
        $tmp = [];
        ksort($with);
        foreach($with as $path => $callback) {
            if($path == $callback) {
                $tmp[] = $path;
            } else {
                $tmp[$path] = $callback;
            }
        }
        $this->model->with($tmp);
    }

    function getInitedList() {
        $this->model->sorted();
        $this->applyWhereHas();
        $this->applyWith();

        return $this->model;
    }

    function getList() {
        $this->getInitedList();
        if(is_null($this->per_page)) {
            return $this->model->get();
        }

        return $this->model->paginate($this->per_page);
    }

    function toArray() {
        $this->init();
        if($this->with_trash) {
            $this->model->withTrashed();
            $this->addFilterInCheckboxButton('with_trash', 'Режим поиска',
                [
                    0 => 'Не удаленные',
                    1 => 'Удаленные',
                ],
                [
                    0 => Model::FILTER_SOFT_DELETE_NO,
                    1 => Model::FILTER_SOFT_DELETE_YES,
                ],
                [0 => 0],
                [
                    0 => function ($query) {
                        $query->whereNull('deleted_at');
                    },
                    1 => function ($query) {
                        $query->whereNotNull('deleted_at');
                    },
                ]);
        }

        //        dd($this->getList()->toArray());
        return [
            'models'     => $this->getList(),
            'filter'     => $this->getForm(),
            'filters'    => $this->form->getValue(),
            'js_filters' => implode(' ', $this->getJsFilters()),
            'vendor'     => $this->getVendor(),
            'entity'     => $this->getEntity(),
        ];
    }
}