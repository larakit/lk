<?php

namespace Larakit\Widget;

use Illuminate\Support\Arr;
use Larakit\Base\Widget;

class WidgetMetaTags extends Widget {
    protected $properties = [
        'image'       => [
            'og:image'          => 'name',
            'twitter:card'      => 'name',
            'twitter:image:src' => 'name',
        ],
        'url'         => [
            'og:url' => 'name',
        ],
        'type'        => [
            'og:url' => 'name',
        ],
        'creator'     => [
            'creator' => 'name',
        ],
        'site'        => [
            //@your_twitter_account
            'twitter:site' => 'name',
            'og:site_name' => 'name',
        ],
    ];

    protected $tags;

    function set($k, $v) {
        $key        = mb_strtolower(mb_substr($k, 3));
        $properties = (array)Arr::get($this->properties, $key);
        foreach ($properties as $name => $type) {
            $this->tags[$name] = [
                'content' => $v,
                'type'    => $type,
            ];
        }

        return $this;
    }

    function setTitle($value) {
        return $this->set(__FUNCTION__, $value);
    }

    function setDescription($value) {
        return $this->set(__FUNCTION__, $value);
    }

    function setImage($value) {
        return $this->set(__FUNCTION__, $value);
    }

    function setType($value = 'article') {
        return $this->set(__FUNCTION__, $value);
    }

    function setUrl($value) {
        return $this->set(__FUNCTION__, $value);
    }

    function setCreator($value) {
        return $this->set(__FUNCTION__, $value);
    }

    function setSite($value) {
        return $this->set(__FUNCTION__, $value);
    }

    function toHtml() {
        return \View::make($this->tpl(), ['tags' => $this->tags])
                    ->__toString();
    }

    function tpl() {
        return 'lk-page::!.widgets.meta_tags';
    }


}