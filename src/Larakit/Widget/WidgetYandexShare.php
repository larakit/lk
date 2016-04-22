<?php

namespace Larakit\Widget;

use Larakit\Base\Widget;
use Larakit\User\Me;

class WidgetYandexShare extends Widget {
    protected $type = 'icon';

    function setTypeSmall() {
        $this->type = 'small';
        return $this;
    }

    function setTypeButton() {
        $this->type = 'button';
        return $this;
    }

    function setTypeLink() {
        $this->type = 'link';
        return $this;
    }

    function setTypeIcon() {
        $this->type = 'icon';
        return $this;
    }

    function setTypeNone() {
        $this->type = 'none';
        return $this;
    }

    protected $services = [
        'vkontakte' => true,
        'facebook' => true,
        'twitter' => true,
        'odnoklassniki' => true,
        'moimir' => false,
        'lj' => false,
        'friendfeed' => false,
        'moikrug' => false,
        'gplus' => false,
        'surfingbird' => false,
        'pinterest' => false
    ];

    protected function service($method, $value) {
        $name = mb_strtolower(mb_substr($method, 7));
        if (isset($this->services[$name])) {
            $this->services[$name] = (bool)$value;
        }
        return $this;
    }

    function serviceVkontakte($val = true) {
        return $this->service(__FUNCTION__, $val);
    }

    function serviceFacebook($val = true) {
        return $this->service(__FUNCTION__, $val);
    }

    function serviceTwitter($val = true) {
        return $this->service(__FUNCTION__, $val);
    }

    function serviceOdnoklassniki($val = true) {
        return $this->service(__FUNCTION__, $val);
    }

    function serviceMoimir($val = true) {
        return $this->service(__FUNCTION__, $val);
    }

    function serviceLj($val = true) {
        return $this->service(__FUNCTION__, $val);
    }

    function serviceFriendfeed($val = true) {
        return $this->service(__FUNCTION__, $val);
    }

    function serviceMoikrug($val = true) {
        return $this->service(__FUNCTION__, $val);
    }

    function serviceGplus($val = true) {
        return $this->service(__FUNCTION__, $val);
    }

    function serviceSurfingbird($val = true) {
        return $this->service(__FUNCTION__, $val);
    }

    protected $yashareImage;

    function servicePinterest($url) {
        $this->yashareImage = $url;
        $this->service(__FUNCTION__, true);
        return $this;
    }

    //vkontakte,facebook,twitter,odnoklassniki,moimir,lj,friendfeed,moikrug,gplus,surfingbird,pinterest"
    //data-yashareImage="http://pushkaclub.bmdemo.ru/!/thumbs/event/1/1/1/item-large.jpg"></div>

    function toHtml() {
        $services = [];
        foreach ($this->services as $service => $semafor) {
            if ($semafor) {
                $services[] = $service;
            }
        }
        $ret = '<div';
        $ret .= '<div class="yashare-auto-init" ' . 'data-yashareL10n="ru" ' . 'data-yashareType="' . $this->type . '" ' . 'data-yashareQuickServices="' . implode(',',
                $services) . '"';
        if ($this->yashareImage) {
            $ret .= 'data-yashareImage="' . $this->yashareImage . '"';
        }
        $ret .= '></div>';
        return '<script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>' . $ret;
    }


}
