<?php
namespace Larakit\Widget;

use Illuminate\Support\Arr;
use Larakit\Base\Widget;
use Larakit\Thumb;

class WidgetThumbName extends Widget {
    protected $entity;
    protected $vendor;
    protected $id        = 0;
    protected $name      = Thumb::DEFAULT_NAME;
    protected $size      = 'normal';
    protected $img_class = 'img-rounded pointer img-responsive img-thumbnail';


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

    function setImgClass($class) {
        $this->img_class = $class;

        return $this;
    }

    function toHtml() {
        $thumb = Thumb::factory($this->vendor, $this->entity, $this->id, $this->name);
        $url   = $thumb->getUrl($this->size);
        $img   = \HtmlImg::addClass($this->img_class);
        //получим размеры
        $config_w = Arr::get($thumb->getConfig(), $this->size . '.size.w');
        $config_h = Arr::get($thumb->getConfig(), $this->size . '.size.h');
        if (!$url) {
            $img->setAttribute('data-src', 'holder.js/' . $config_w . 'x' . $config_h . '?theme=sky');
        }
        else {
            $img->setSrc($url . '?' . microtime(true));
        }
        $span = WidgetOverlay::factory()
                             ->setIcon('fa fa-folder-open-o');

        //        $span = \HtmlSpan::setContent('<i class="fa fa-folder-open-o"></i>');

        return \HtmlDiv::addClass(
            'js-btn box-overlay__wrap box-overlay__wrap--fade box-overlay__wrap--blue box-overlay__wrap--inline'
        )
                       ->setAttribute(
                           'data-thumb-name',
                           $this->name
                       )//                       ->setAttribute('data-vendor', $this->vendor)
            //                       ->setAttribute('data-entity', $this->entity)
                       ->setAttribute('data-thumb-size', $this->size)
                       ->setAttribute('data-action', 'thumb_name')
                       ->setContent($span . $img)
                       ->__toString();

    }


}