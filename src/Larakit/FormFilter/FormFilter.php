<?php
/**
 * Created by Larakit.
 * Link: http://github.com/larakit
 * User: Alexey Berdnikov
 * Date: 20.07.16
 * Time: 14:41
 */

namespace Larakit\FormFilter;

use Larakit\CRUD\TraitEntity;
use Larakit\QuickForm\LaraForm;

class FormFilter {

    use TraitEntity;
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
    protected $with_trash      = false;
    protected $query_with      = [];
    protected $query_where_has = [];

    protected $filters = [];

    function __construct($model) {
        $this->model    = $model::select();
        $this->base_url = \URL::current();
        $this->form     = new LaraForm('filter_' . static::getEntitySnake());
    }

    function addFilter($filter) {
        $this->filters[] = $filter;
    }

    function toArray() {
        $this->init();
        $this->form->putAlteBox();
        foreach($this->filters as $filter) {
            /* @var $filter FilterSelfLike */
            $filter->element($this->form);
        }

        return [
            'form_filter' => $this->form,
        ];
    }

}