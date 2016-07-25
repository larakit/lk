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

class AdminController extends Controller {

    protected $layout = 'larakit::admin';

    function index() {
        return $this->response([
            'rows' => CrudRow::$rows,
        ]);
    }
}