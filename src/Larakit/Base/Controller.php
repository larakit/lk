<?php
namespace Larakit\Base;

use Illuminate\Support\Arr;
use Larakit\Route\Route;
use Larakit\Widget\WidgetH1;
use Larakit\Widget\WidgetMetaTags;

class Controller extends \Illuminate\Routing\Controller{

    protected $model_name;
    protected $base_url;
    protected $layout   = 'larakit::!.layouts.default';
    protected $page     = 'lk-page::page';
    protected $replaced = [];

    function addBreadCrumb($as = null, $replaced = []) {
        $this->replaced = array_merge($this->replaced, $replaced);

        $h1_ext = Route::get_h1_ext($as);
        $title  = Route::get_title($as);
        $url    = Route::get_url($as);
        foreach ($this->replaced as $k => $v) {
            $k      = '{' . $k . '}';
            $h1_ext = str_replace($k, $v, $h1_ext);
            $title  = str_replace($k, $v, $title);
            $url    = str_replace($k, $v, $url);
        }
        WidgetH1::factory()
                ->setH1($title)
                ->setH1Ext($h1_ext);
        WidgetMetaTags::factory()
                      ->setDescription($h1_ext);
        \Larakit\Page\Page::addBreadCrumb($title, $url);
    }

    function response($vars = []) {
        if (!isset($vars['base_url'])) {
            $vars['base_url'] = $this->base_url;
        }
        $layout = \View::make($this->layout, $vars);

        return \View::make($this->page, [
            'layout' => $layout
        ]);
    }

    /**
     * @param $tpl
     *
     * @return $this
     */
    function layout($tpl = null) {
        //        $template = explode('::', $as);
        //        if (count($template) > 1) {
        //            $namespace = Arr::get($template, 0);
        //            if (substr_count($namespace, '-') && substr_count(Arr::get($template, 1), '.')) {
        //                $package   = explode('.', Arr::get($template, 1), 3);
        //                $end       = Arr::get($package, 2, "");
        //                if ($end) {
        //                    $end = '.' . $end;
        //                }
        //                $package = Arr::get($package, 1) . '.' . Arr::get($package, 0) . $end;
        //
        //                $as = $namespace . '::' . trim($package, '.');
        //                dd($as );
        //            }
        //        }
        if (true === $tpl) {
            $this->layout = \Route::currentRouteName();
        } elseif (null !== $tpl) {
            $this->layout = $tpl;
        }
        //        dd($this->layout);

        return $this;
    }

}
