<?php
/**
 * Created by Larakit.
 * Link: http://github.com/larakit
 * User: Alexey Berdnikov
 * Date: 19.07.16
 * Time: 16:48
 */

namespace Larakit\Controllers;

use Larakit\Controller;
use Larakit\CRUD\CrudRow;

class AdminCodegenController extends Controller {

    protected $layout = 'larakit::admin.generator';

    function index() {
//        $models = rglob('*.php', 0, base_path('vendor/*/*/*/*/Models/'));
//        dd(111, $models);
        return $this->response([
            'rows' => CrudRow::$rows,
        ]);
    }
}