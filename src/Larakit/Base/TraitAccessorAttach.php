<?php
namespace Larakit\Base;


use Larakit\Attach\Attach;
use Larakit\Attach\Previewer;
use Larakit\Attach\PreviewerManager;
use Larakit\Helper\HelperText;

trait TraitAccessorAttach {

    function getAttachThumbListAttribute() {
        /** @var Previewer $class */
        $class = PreviewerManager::get($this->model->attach_mime);
        return $class::getList($this->model);
    }

    function getAttachThumbItemAttribute() {
        /** @var Previewer $class */
        $class = PreviewerManager::get($this->model->attach_mime);
        return $class::getItem($this->model);
    }

    function getAttachSizeAttribute() {
        return HelperText::fileSize($this->model->attach_size);
    }

    /********************************************************************************
     * ВИДЕО
     ********************************************************************************/

    /********************************************************************************
     * АУДИО
     ********************************************************************************/
    function thumbTypeAudio() {
        $url = $this->getAttachUrl();
        return \HtmlAudio::setSrc($url . '?' . microtime(true))
                         ->setControls('controls');
    }

    function getAttachUrl() {
        return Attach::fromModel($this->model)
                     ->getUrl($this->model->attach_ext);
    }

    function thumbExtMp3() {
        return $this->thumbTypeAudio();
    }

    function thumbExtWav() {
        return $this->thumbTypeAudio();
    }


}