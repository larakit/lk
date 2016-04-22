<?php
namespace Larakit\Widget;

use Larakit\Base\Widget;
use Larakit\Model\LarakitSeo;

class WidgetLarakitSeo extends Widget {

    function __construct(){
        $this->page = LarakitSeo::where('url', '=');
    }

    function getSiteName(){
        return \Config::get('larakit_seo::site_name');
    }

}