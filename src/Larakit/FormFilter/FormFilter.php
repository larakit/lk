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
        $this->form     = new LaraForm('filter_' . static::getEntitySnake(), 'get');
    }

    function addFilter($filter) {
        $this->filters[] = $filter;
    }

    function toArray() {
        $this->init();
        $box = $this->form->putAlteBox('Фильтры списка');
        $body = $box->putAlteBoxBody()->removeClass('box-primary');
        foreach($this->filters as $filter) {
            /* @var $filter FilterSelfLike */
            $filter->element($body);
            if($filter->value){
                $filter->query($this->model);
            }
        }
        if($this->form->isSubmitted()){
            $box->addClass('box-solid box-success');
        } else {
            $box->addClass('box-default');
        }
        $footer = $box->putAlteBoxFooter();
        $footer->putSubmitTwbs('Применить')->addClass('btn-success');
        $footer->putLinkTwbs('Сбросить')->addClass('btn-default');
//        ->removeClass('box-primary');

        return [
            'form_filter' => $this->form,
            'models' => $this->model->paginate($this->per_page),
        ];
    }

}