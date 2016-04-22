<?php

namespace Larakit\Command;

class LarakitCommand extends \Illuminate\Console\Command {

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
//        $route = new \Illuminate\Routing\Route([], '/', []);
//        $request = new \Illuminate\Http\Request();
//        \Route::callRouteFilter('larakit_booted', [], $route, $request);
        boot_larakit();
    }

}