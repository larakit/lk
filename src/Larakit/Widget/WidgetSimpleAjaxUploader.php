<?php
namespace Larakit\Widget;

use Larakit\Base\Widget;

class WidgetSimpleAjaxUploader extends Widget {
    protected $identity             = '';
    protected $max_size             = 500;
    protected $max_uploads          = 1;
    protected $url_upload           = '';
    protected $accept               = '';
    protected $allowed_ext          = [];
    protected $url_session_progress = '/!/simpleajaxuploader/session-progress';
    protected $url_upload_progress  = '/!/simpleajaxuploader/upload-progress';

    function tpl() {
        return 'larakit::!.widgets.simpleajaxuploader';
    }

    function toHtml() {
        $this->set('identity', $this->identity);
        $this->set('url_upload', $this->url_upload);
        $this->set('url_session_progress', $this->url_session_progress);
        $this->set('url_upload_progress', $this->url_upload_progress);
        $this->set('max_uploads', $this->max_uploads);
        $this->set('max_size', $this->max_size);


        if (!$this->allowed_ext) {
            $this->setAllowedExtImages();
        }
        if (!$this->accept) {
            $this->setAcceptImages();
        }
        $this->set('accept', $this->accept);
        $this->set('allowed_ext', implode('|', $this->allowed_ext));

        return parent::toHtml();
    }

    function setAccept($accept) {
        $this->accept = $accept;

        return $this;
    }

    function setAcceptImages() {
        $this->setAccept('image/*');

        return $this;
    }

    function setAllowedExt($ext) {
        $this->allowed_ext = (array)$ext;

        return $this;
    }

    function setAllowedExtImages() {
        $this->setAllowedExt([
            'jpg',
            'jpeg',
            'png',
            'gif'
        ]);
        $this->setAcceptImages();

        return $this;
    }

    function setIdentity($identity) {
        $this->identity = $identity;

        return $this;
    }

    function setMaxSize($maxsize) {
        $this->max_size = (int)$maxsize;

        return $this;
    }

    function setMaxUploads($count) {
        $this->max_uploads = (int)$count;

        return $this;
    }

    function setUrlUpload($url_upload) {
        $this->url_upload = $url_upload;

        return $this;
    }

}