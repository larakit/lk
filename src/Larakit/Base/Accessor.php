<?php
namespace Larakit\Base;

use Illuminate\Support\Arr;
use Larakit\Helper\HelperDate;
use Larakit\Helper\HelperModel;
use Larakit\Helper\HelperText;
use Larakit\Html\Toggler;
use Larakit\Manager\ManagerRowType;
use Larakit\Thumb;
use Larakit\Widget\WidgetHasMany;
use Larakit\Widget\WidgetThumbName;

class Accessor {
    use TraitEntity;
    /**
     * @var Model
     */
    protected $model;

    protected $switchers = [];
    const SWITCH_MULTI       = 0;
    const SWITCH_ONE_MAYBE   = 1;
    const SWITCH_ONE_REQUIRE = 2;
    protected $route_admin_item_restore = null;
    protected $route_admin_item_edit    = null;
    protected $route_admin_item_delete  = null;

    function getSwitchMode($attribute) {
        return Arr::get($this->switchers, $attribute, false);
    }


    static function getEntityPrefix() {
        return 'accessor';
    }

    /**
     * @var Model
     */

    function __construct($model) {
        $this->model = $model;
    }

    /**
     * @param $model
     *
     * @return Accessor
     */
    static function factory($model) {
        $class = str_replace('\Model\\', '\Accessor\\Accessor', get_class($model));
        if (!class_exists($class)) {
            $class = __CLASS__;
        }
        return new $class($model);

    }

    function __get($name) {
        return $this->get($name);
    }

    function get() {
        try {
            $args = func_get_args();
            $name = array_shift($args);
            if (!$name) {
                return (string)\HtmlSpan::addClass('label label-danger')->setContent('Accessor:get()');
            }
            $field = $this->model->{$name};
            if (isset($field) && is_object($field) && $field instanceof Model) {
                return Accessor::factory($field);
            }
            $method = 'get' . studly_case($name) . 'Attribute';
            if ('getAttribute' == $method) {
                return null;
            }
            if (method_exists($this, $method)) {
                return call_user_func_array([
                    $this,
                    $method
                ], $args);
            }

            return $this->model->{$name};
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    function model() {
        return $this->model;
    }

    function __toString() {

        return (string)$this->model->__toString();
    }

    function thumb($size, $name = Thumb::DEFAULT_NAME) {
        $class = get_class($this->model);
        return WidgetThumbName::factory()
                              ->setEntity($class::getEntity())
                              ->setVendor($class::getVendor())
                              ->setSize($size)
                              ->setId($this->model->id)
                              ->setName($name);
    }

    function actionCard($text = null, $title = 'Карточка объекта') {
        if (!$text) {
            $text = \HtmlSpan::addClass('text-lg')
                             ->setContent($this->model->__toString());
        }
        return \HtmlSpan::setAttribute('data-action', 'card')
                        ->addClass('ajax js-btn')
                        ->setTitle($title)
                        ->setContent($text);
    }

    function actionRestore() {
        if (!Acl::factory($this->model)
                ->reason('restore')
        ) {
            return \HtmlButton::setAttribute('data-action', 'restore')
                              ->addClass('btn btn-success btn-xs js-btn')
                              ->setTitle('Восстановить запись')
                              ->setContent('<i class="fa fa-leaf"></i>');
        }

        return '';
    }

    function actionEdit($type = null) {
        if (!Acl::factory($this->model)
                ->reason('edit')
        ) {
            $ret = \HtmlButton::setAttribute('data-action', 'edit')
                              ->addClass('btn btn-primary btn-xs js-btn')
                              ->setTitle('Редактировать запись')
                              ->setContent('<i class="fa fa-edit"></i>');
            if ($type) {
                $ret->setAttribute('data-type', $type);
            }
            return $ret;
        }

        return '';
    }

    function actionAttach() {
        if (!Acl::factory($this->model)
                ->reason('edit')
        ) {
            //            <div class="btn-group btn-group-block">
            //              <button type="button" class="btn btn-default"><i class="fa fa-paperclip"></i></button>
            //              <button type="button" class="btn btn-default"><i class="fa fa-file-pdf-o"></i> &nbsp; брендбук_на_согласование.pdf</button>
            //              <button type="button" class="btn btn-default"><i class="fa fa-remove"></i></button>
            //            </div>
            $btn_paperclip = \HtmlButton::setAttribute('data-action', 'attach')
                                        ->addClass('btn btn-default js-btn')
                                        ->setTitle('Прикрепить файл')
                                        ->setContent('<i class="fa fa-paperclip"></i>');
            $btn_remove    = '';
            $btn_file      = '';
            $btn_size      = '';
            $btn_mime      = '';
            $btn_img       = '';
            $name          = $this->model->attach_name;
            if ($name) {
                $btn_size   = \HtmlButton::addClass('btn btn-default')
                                         ->setContent(HelperText::fileSize($this->model->attach_size));
                $btn_mime   = \HtmlButton::addClass('btn btn-default')
                                         ->setContent($this->model->attach_mime);
                $btn_file   = \HtmlButton::setAttribute('data-action', 'attach_name')
                                         ->addClass('btn btn-default js-btn')
                                         ->setContent($name);
                $btn_remove = \HtmlButton::setAttribute('data-action', 'attach_clear')
                                         ->addClass('btn btn-default js-btn')
                                         ->setTitle('Удалить вложение')
                                         ->setContent('<i class="fa text-danger fa-remove"></i>');
            }
            $ret = \HtmlDiv::addClass('btn-group')
                           ->setContent($btn_paperclip . $btn_file . $btn_size . $btn_remove);
            return $ret;
        }
        return '';
    }

    function actionBelongsTo($relation, $icon_class = 'fa fa-sitemap') {
        if (!Acl::factory($this->model)
                ->reason('edit')
        ) {
            $btn = \HtmlButton::setAttribute('data-action', 'belongs_to')
                              ->setAttribute('data-relation', $relation)
                              ->addClass('btn btn-primary btn-xs js-btn mr10')
                              ->setTitle('Редактировать связь')
                              ->setContent('<i class="' . $icon_class . '"></i>');;
            $rel = $this->model->{$relation};
            /* TODO: сделать получение "нет привязки к группе" через laralang модели */
            if (!$rel) {
                $rel = \HtmlSpan::addClass('text-muted')
                                ->setContent('- не выбрано -');
            }

            return \HtmlDiv::addClass('row-fluid')
                           ->setContent(\HtmlDiv::addClass('pull-left')
                                                ->setContent($btn) . \HtmlDiv::setContent($rel));
        }

        return '';
    }

    function actionHasMany($relation) {
        if (!Acl::factory($this->model)
                ->reason('edit')
        ) {
            $list      = \HtmlOl::addClass('js-hasmany');
            $relations = $this->model->{$relation};
            if ($relations) {
                foreach ($relations as $item) {
                    $list->addItem($item);
                }
            } else {
                $list->addItem('Связей не установлено');
            }
            $btn = \HtmlButton::setAttribute('data-action', 'has_many')
                              ->setAttribute('data-relation', $relation)
                              ->addClass('btn btn-primary btn-xs js-btn mr10')
                              ->setTitle('Редактировать связь')
                              ->setContent('<i class="fa fa-sitemap"></i>');;

            return \HtmlDiv::addClass('row-fluid')
                           ->setContent(\HtmlDiv::addClass('pull-left')
                                                ->setContent($btn) . \HtmlDiv::setContent($list));
        }

        return '';
    }

    function actionBelongsToMany($relation, $icon = 'fa fa-sitemap', $title = null) {
        if (!Acl::factory($this->model)
                ->reason('edit')
        ) {
            $list = \HtmlOl::addClass('js-hasmany');
            foreach ($this->model->{$relation} as $item) {
                $list->addItem($item);
            }
            $model_class = get_class($this->model);
            if ($title) {
                $translated_title = $model_class::translate($title);
            } else {
                $translated_title = \Lang::get('larakit::relations.hint.belongs_to_many');
            }
            $btn = \HtmlButton::setAttribute('data-action', 'belongs_to_many')
                              ->setAttribute('data-relation', $relation)
                              ->addClass('btn btn-primary btn-xs js-btn ')
                              ->setTitle($translated_title)
                              ->setContent('<i class="' . $icon . '"></i>');;

            return \HtmlDiv::addClass('row-fluid')
                           ->setContent(\HtmlDiv::addClass('pull-left')
                                                ->setContent($btn) . \HtmlDiv::setContent($list));
        }

        return '';
    }


    function actionDelete() {
        if (!Acl::factory($this->model)
                ->reason('delete')
        ) {
            return \HtmlButton::setAttribute('data-action', 'delete')
                              ->addClass('btn btn-danger btn-xs js-btn')
                              ->setTitle('Удалить запись')
                              ->setContent('<i class="fa fa-trash"></i>');
        }

        return '';
    }

    /**
     * @param       $field
     * @param array $field_extend
     *
     * @return Toggler
     */
    function toggle($field, $field_extend = []) {
        $toggler = new Toggler();
        $toggler->setState($this->model->{$field})
                ->setLabel($this->model->getEntityLabel($field))
                ->setAttribute('data-action', 'toggle')
                ->setAttribute('data-field', $field)
                ->setAttribute('data-field-extend', http_build_query($field_extend));

        return $toggler;
    }

    function toggleInverse($field, $field_extend = []) {
        return $this->toggle($field, $field_extend)
                    ->setOffClass('btn-success')
                    ->setOnClass('btn-danger');
    }

    /**
     * @param       $field
     * @param array $field_extend
     *
     * @return Toggler
     */
    function toggleFrozen($field, $field_extend = []) {
        return $this->toggle($field, $field_extend)
                    ->removeClass('pointer')
                    ->setLabel('');

    }

    function row($type = null, $ext = []) {
        $vendor    = (string)$this->getVendor();
        $entity    = (string)$this->getEntity();
        $row_types = (array)ManagerRowType::get(ManagerRowType::makeKey($vendor, $entity));
        $append    = ($type && in_array($type, $row_types) ? $type : '');

        if ('attach' == $type) {
            $tpl = 'larakit::!.partials.attach_row';
        } else {
            if (!$append)
                return '';
            $tpl = ($vendor ? $vendor . '::' : '') . '!.partials.' . $entity . '.row.' . $append;
        }
        return (string)\View::make($tpl, array_merge((array)$ext, [
            'model'  => $this->model,
            'vendor' => $vendor,
            'entity' => $entity,
        ]));
    }

    function tags() {
        $ret = [];
        foreach ($this->model->tags as $tag) {
            $ret[] = \HtmlSpan::addClass('tag')
                              ->setContent(\HtmlI::addClass('fa fa-tag') . ' ' . $tag);
        }

        return \HtmlDiv::addClass('tags text-sm')
                       ->setContent(implode(' ', $ret));
    }

    function comments($limit = null) {
        //        $this->model->load('comments.author');
        $realplexor_channel = '';
        if (class_exists('Realplexor\Helper')) {
            $realplexor_channel = \Realplexor\Helper::channel([
                'vendor' => static::getVendor(),
                'entity' => static::getEntity(),
                'id'     => $this->model->id

            ]);
        }
        return \View::make('larakit::!.partials.comments', [
            'comments'           => $this->model->comments()
                                                ->with('author')
                                                ->orderBy('created_at', 'desc'),
            'vendor'             => static::getVendor(),
            'realplexor_channel' => $realplexor_channel,
            'limit'              => $limit,
            'entity'             => static::getEntity(),
            'id'                 => $this->model->id,
        ]);
    }

    function publicUrl() {
        return '/' . $this->model->id . '/';
    }

    function publicLink() {
        return \HtmlA::setHref($this->publicUrl())
                     ->setContent($this->model);
    }

    function card($fields) {
        return \View::make('larakit::!.partials..card', [
            'fields' => $fields,
            'model'  => $this->model
        ]);
    }

    function widgetHasMany($relations) {
        $model_name = get_class($this->model);
        if (!$relations) {
            $relations = array_keys(HelperModel::getHasMany($model_name));
        }
        return WidgetHasMany::factory($model_name . $this->model->id)
                            ->setModel($this->model)
                            ->setRelations($relations) . '';
    }


    function toArray() {
        $ret = [];
        foreach ($this->model->toArray() as $k => $v) {
            $ret[$k] = $this->get($k);
        }

        return $ret;
    }

    function itemToggler() {
        return \HtmlButton::addClass('btn btn-default btn-xs js-collapse-item mr10 t-3')
                          ->setAttribute('data-widget', 'collapse')
                          ->setAttribute('type', 'button')
                          ->setContent('<i class="fa fa-plus"></i>');
    }

    function itemDrag() {
        return \HtmlSpan::addClass('btn btn-default btn-xs sortable-handle mr10 t-3')
                        ->setContent('<i class="fa fa-bars"></i>');
    }

    function isDeletedClass() {
        return (isset($this->model->deleted_at) && $this->model->deleted_at) ? 'deleted_record' : '';
    }

    function getUpdatedAtAttribute() {
        return \HtmlAbbr::setContent($this->getUpdatedAtDiffAttribute())
                        ->setTitle($this->getUpdatedAtDateAttribute());
    }

    function getUpdatedAtDateAttribute() {
        return HelperDate::fromDatetime($this->model->updated_at);
    }

    function getUpdatedAtDiffAttribute() {
        return \LocalizedCarbon::parse($this->model->updated_at)
                               ->diffForHumans();
    }

    function getCreatedAtAttribute() {
        return \HtmlAbbr::setContent($this->getCreatedAtDiffAttribute())
                        ->setTitle($this->getCreatedAtDateAttribute());
    }

    function getCreatedAtDiffAttribute() {
        return \LocalizedCarbon::parse($this->model->created_at)
                               ->diffForHumans();
    }

    function getCreatedAtDateAttribute() {
        return HelperDate::fromDatetime($this->model->created_at);
    }

    function getRowClassAttribute() {
        return 'primary';
    }

}