<?php
namespace Larakit\Widget;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Larakit\Base\Model;
use Larakit\Base\Widget;
use Larakit\Thumb;

class WidgetThumbNameSize extends Widget {
    protected $entity;
    protected $vendor;
    protected $id   = 0;
    protected $name = Thumb::DEFAULT_NAME;
    protected $size = 'normal';


    /**
     * @param $entity
     *
     * @return $this
     */
    function setEntity($entity) {
        $this->entity = $entity;
        return $this;
    }

    function setVendor($vendor) {
        $this->vendor = $vendor;
        return $this;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    function setId($id) {
        $this->id = $id;
        return $this;
    }


    /**
     * @param $name
     *
     * @return $this
     */
    function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @param $size
     *
     * @return $this
     */
    function setSize($size) {
        $this->size = $size;
        return $this;
    }

    function toHtml() {
        $thumb       = Thumb::factory($this->vendor, $this->entity, $this->id, $this->name);
        $url         = $thumb->getUrl($this->size);
        $img         = \HtmlImg::addClass('img-rounded pointer img-responsive');
        //получим размеры
        $config_w = Arr::get($thumb->getConfig(), $this->size . '.size.w');
        $config_h = Arr::get($thumb->getConfig(), $this->size . '.size.h');
        if (!$url) {
            $img->setAttribute('data-src', 'holder.js/' . $config_w . 'x' . $config_h . '?theme=sky');
        }
        else {
            $img->setSrc($url . '?' . microtime(true));
        }
        $span = WidgetOverlay::factory()->setIcon('fa fa-crop')->setText('Обрезать миниатюру');

        return \HtmlDiv::addClass('thumb-cropper js-btn')
                       ->setAttribute('data-thumb-name', $this->name)
                       ->setAttribute('data-thumb-size', $this->size)
                       ->setAttribute('data-vendor', $this->vendor)
                       ->setAttribute('data-action', 'thumb_size')
                       ->setContent($span . $img)
                       ->__toString();

    }

}