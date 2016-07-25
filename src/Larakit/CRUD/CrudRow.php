<?php
/**
 * Created by Larakit.
 * Link: http://github.com/larakit
 * User: Alexey Berdnikov
 * Date: 14.07.16
 * Time: 21:47
 */

namespace Larakit\CRUD;

use Illuminate\Support\Arr;

class CrudRow {

    static $rows = [];

    static function register($model_class, $base_url, $name = 'admin') {
        static::$rows[$model_class][$name] = $base_url;
    }

    protected $name;
    protected $model;
    protected $tpl;
    protected $base_url;

    function __construct($model, $name = 'admin', $tpl = null) {
        $this->base_url = rtrim(Arr::get(static::$rows, get_class($model) . '.' . $name),'/').'/';
        if(!$this->base_url) {
            throw  new \Exception('CRUD-ROW not registered!');
        }

        $this->model = $model;
        $this->tpl   = $tpl;
        if(!$this->tpl) {
            $this->tpl = $tpl ? : 'larakit::!.crud.row';
        }
    }

    function __toString() {
        try {
            return \View::make($this->tpl, [
                'base_url'        => $this->base_url,
                'model'            => $this->model,
            ])->render();
        }
        catch(\Exception $e) {
            print '<pre>';
            echo $e->getMessage();
            echo $e->getTraceAsString();
            print '</pre>';
            exit;
        }
    }
}