<?php
namespace Larakit\Base;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Larakit\Manager\ManagerPackage;


/**
 * Class Model
 * @package Larakit\Base
 * @method Model listsExt ()
 * @method Model sorted ()
 * @method Model toObjects ()
 */
class Model extends \Eloquent {
    use TraitEntity;
    use \Laravelrus\LocalizedCarbon\Traits\LocalizedEloquentTrait;
    use TraitModelThumbTmp;
    const NAME_CASE_I = 1;
    const NAME_CASE_R = 2;
    const NAME_CASE_D = 3;
    const NAME_CASE_V = 4;
    const NAME_CASE_T = 5;
    const NAME_CASE_P = 6;
    static $name_cases = [
        Model::NAME_CASE_I => 'именительный',
        Model::NAME_CASE_R => 'родительный',
        Model::NAME_CASE_D => 'дательный',
        Model::NAME_CASE_V => 'винительный',
        Model::NAME_CASE_T => 'творительный',
        Model::NAME_CASE_P => 'предложный',
    ];
    const GENDER_MALE   = 1;
    const GENDER_FEMALE = -1;
    const GENDER_MIDDLE = 0;
    static $name_genders = [
        self::GENDER_MALE   => 'мужской род',
        self::GENDER_FEMALE => 'женский род',
        self::GENDER_MIDDLE => 'средний род',
    ];

    const FILTER_SOFT_DELETE_YES = 'soft-delete-yes';
    const FILTER_SOFT_DELETE_NO  = 'soft-delete-no';
    static protected $labels            = [];
    static protected $desc              = [];
    static protected $larakit_relations = [
        'author' => \Larakit\Model\User::class,
    ];

    static $gender = self::GENDER_MALE;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->appends[] = 'js_filter';
        $this->appends[] = 'js_sort';
    }

    //    static function boot() {
    //        parent::boot();
    //    }

    function toCard() {
        $ret = $this->toArray();
        unset($ret['id']);
        unset($ret['created_at']);
        unset($ret['updated_at']);
        unset($ret['deleted_at']);
        unset($ret['js_filter']);
        unset($ret['js_sort']);
        unset($ret['order']);
        return Arr::except($ret, $this->getHidden());
    }

    function toForm() {
        $ret = $this->toArray();
        foreach ($this->getDates() as $dt) {
            if ($this->{$dt}) {
                $ret[$dt] = Carbon::parse($this->{$dt})
                                  ->format('d.m.Y');
            }
        }

        return $ret;
    }

    function getChangedAttributes() {
        $ret = [];
        foreach ($this->getOriginal() as $k => $v) {
            $new = Arr::get($this->getAttributes(), $k);
            if ($v != $new) {
                $ret[$k] = [
                    'new' => $new,
                    'old' => $v,
                ];
            }
        }

        return $ret;
    }

    //    public function getMorphClass() {
    //        return static::getVendor() . '::' . static::getEntity();
    //    }

    protected $default_sort = [];

    function scopeSorted(Builder $query) {
        foreach ($this->default_sort as $k => $v) {
            $query->orderBy($k, $v);
        }
    }

    function scopeListsExt(Builder $query) {
        $ret = [];
        foreach ($query->get() as $model) {
            $ret[$model->id] = $model->__toString();
        }

        return $ret;
    }

    function scopeToObjects(Builder $query) {
        $ret = [];
        foreach ($query->get() as $model) {
            $ret[$model->id] = $model->toArray();
        }

        return $ret;
    }

    function isJustCreated() {
        return ($this->created_at == $this->updated_at);
    }


    /**
     * Фильтр для LarakitManager
     * @return int
     */
    function    getJsFilterAttribute() {
        $filters = [];
        if ($this->deleted_at) {
            $filters[] = self::FILTER_SOFT_DELETE_YES;
        } else {
            $filters[] = self::FILTER_SOFT_DELETE_NO;
        }

        return $filters;
    }

    /**
     * Поле для сортировки для LarakitManager
     * @return int
     */
    function getJsSortAttribute() {
        $len  = 6;
        $sort = [];
        if (!$this->default_sort) {
            return str_pad($this->id, 10, '0', STR_PAD_LEFT);
        }
        foreach ($this->default_sort as $k => $v) {
            $val = $this->{$k};
            if ('desc' == $v) {
                if (is_numeric($val)) {
                    $val = pow(10, $len) - intval($val);
                }
            }
            $sort[] = str_pad(mb_substr($val, 0, $len + 1), $len + 1, '0', STR_PAD_LEFT);
        }

        return implode('_', $sort);
    }

    function reload() {
        if (method_exists($this, 'withTrashed')) {
            return $this->withTrashed()
                        ->find($this->id);
        }
        return $this->find($this->id);
    }


    public function hasMany($related, $foreignKey = null, $localKey = null) {
        return parent::hasMany($related, $foreignKey, $localKey)
                     ->sorted();
    }


//    function __call($method, $args = []) {
//        $model_class = get_called_class();
//        list($namespace, $class_name) = explode('\Model\\', $model_class);
//
//        $namespaces = array_map(function ($vendor) {
//                return \Str::studly(str_replace('-', '\_', $vendor));
//            }, array_values(ManagerPackage::get()));
//
//        $singular = \Str::singular($method);
//        if ($singular == $method) {
//            foreach ($namespaces as $namespace) {
//                $relation_class = $namespace . '\Model\\' . \Str::studly($singular);
//                if (class_exists($relation_class)) {
//                    $key = \Str::snake($singular) . '_id';
//                    return $this->belongsTo($relation_class, $key);
//
//                }
//            }
//        } else {
//            foreach ($namespaces as $namespace) {
//                $has_many_class = $namespace . '\Model\\' . \Str::studly($singular);
//
//                if (class_exists($has_many_class)) {
//                    $related_model = new $has_many_class();
//                    $fillable_key  = \Str::snake($class_name) . '_id';
//                    if (in_array($fillable_key, $related_model->getFillable())) {
//                        return $this->hasMany($related_model, $fillable_key);
//                    }
//                }
//            }
//        }
//        return parent::__call($method, $args);
//    }

}