<?php
namespace Larakit\Command;

use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Larakit\Model\LangItem;
use Larakit\Model\LangItemValue;
use Symfony\Component\Console\Helper\TableSeparator;

class Readiness extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larakit:readiness';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Готовность проекта';

    /**
     * Пусть для сохранения сгенерированных данных
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire() {
        $rows          = [];
        $real_progress = 0;
        $idx           = 0;
        $last          = null;
        $items         = [];
        foreach (\Route::getRoutes() as $r => $route) {
//            if (!\Larakit\Route\Route::isEnable($route->getName())) {
//                continue;
//            }
            /**
             * @var Route $route
             */
            $item    = [];
            $action  = $route->getActionName();
            $_action = explode('@', $action);
            $method  = Arr::get($_action, 1);
            $class   = Arr::get($_action, 0);
            //            if (($action != 'Closure') && (strpos($action, 'Larakit') === false))
            //                continue;
            if ('Closure' == $action) {
                $progress = 100;
            }
            else {
                if (is_callable(explode('@', $action))) {
                    $readiness = (int)\Larakit\Route\Route::_($route->getName(), 'readiness');
                    if (!$readiness) {
                        $readiness = 1;
                    }
                    $progress = $readiness;
                }
                else {
                    $progress = 0;
                }
            }
            $idx++;
            $real_progress += $progress;
            switch (true) {
                case $progress < 50:
                    $style = 'error';
                    break;
                case $progress < 100:
                    $style = 'question';
                    break;
                default:
                    $style = 'info';
                    break;
            }
            $item[] = $this->wrap($route->domain(), $style);
            $item[] = $this->wrap(implode('|', $route->getMethods()), $style);
            $item[] = $this->wrap($route->getUri(), $style);
            $item[] = $this->wrap($route->getName(), $style);
            if ('Closure' == $action) {
                $item[] = $this->wrap('\Closure', $style);
                $item[] = '';
            }
            else {
                $item[] = $this->wrap($class, $style);
                $item[] = $this->wrap($method, $style);
            }
            $item[] = $this->wrap($idx, $style, 3);
            $item[] = $this->wrap($progress . '%', $style, 9);
            $v      = mb_strpos($route->getName(), '::') ? explode('::', $route->getName()) : [];
            $vendor = Arr::get($v, 0, 'app');
            //            $item[]      = $vendor;

            if (($last != $vendor)) {
                if (!(is_null($last))) {
                    $rows[] = new TableSeparator();
                }
                $last = $vendor;
            }
            $items[$route->getName()] = $item;
        }

        $last = null;
        $rows = [];
        $idx = 0;
        foreach ($items as $route => $row) {
            $idx++;
            $v      = mb_strpos($route, '::') ? explode('::', $route) : [];
            $vendor = Arr::get($v, 0, 'app');
            if (($last != $vendor)) {
                if (!(is_null($last))) {
                    $rows[] = new TableSeparator();
                }
                $last = $vendor;
            }
            $rows[] = [$idx] + $row;
        }
        //        foreach($)
        $this->table(
            [
                'Domain',
                'Method',
                'URI',
                'RouteName',
                'Controller',
                'Action',
                '#',
                'Readiness',
            ],
            $rows
        );
        $progress_all  = count($rows) * 100;
        $real_progress = $real_progress / $progress_all * 100;
        $this->info('Общая готовность: ' . number_format($real_progress, 2) . '%');
        $this->info(
            '[' . str_pad(
                str_repeat('#', (int)$real_progress) . ($real_progress != 100 ? '>' : ''),
                100,
                '-',
                STR_PAD_RIGHT
            ) . ']'
        );
    }

    function wrap($text, $style, $pad_length = 0, $pad_style = STR_PAD_LEFT) {
        if ($pad_length) {
            $text = str_pad($text, $pad_length, ' ', $pad_style);
        }

        return '<' . $style . '>' . $text . '</' . $style . '>';
    }

    public function getArguments() {
        return [
        ];
    }


}
