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

class AdminCodegenModelController extends Controller {

    protected $layout = 'lk-adminlte::!.layouts.default';

    function index() {
        $model = base64_decode(\Route::input('model'));
        dump($model);
        $r       = new \ReflectionClass($model);
        dd($r->getTraits());
        $methods = [];
        foreach($r->getMethods() as $method) {
            if($method->class == $model) {
                $methods[] = $this->sourceMethod($method);
            };
        }
        dump($methods);
        dump($methods = $r->getMethod('messageNotFound'));
        dump($methods = $r->getMethod('messageNotFound')->getDocComment());
        dump($this->sourceMethod($r->getMethod('messageNotFound')));
        dump($methods = $r->getMethod('messageNotFound')->class);

//        $models = rglob('*.php', 0, base_path('vendor/*/*/*/*/Models/'));
//        dd(111, $models);

        return $this->response([
            'rows' => CrudRow::$rows,
        ]);
    }

    function sourceMethod(\ReflectionMethod $method) {
        $filename   = $method->getFileName();
        $start_line = $method->getStartLine() - 1; // it's actually - 1, otherwise you wont get the function() block
        $end_line   = $method->getEndLine();
        $length     = $end_line - $start_line;

        $source = file($filename);
        $body   = implode("", array_slice($source, $start_line, $length));

        return $method->getDocComment() . PHP_EOL . $body;
    }
}