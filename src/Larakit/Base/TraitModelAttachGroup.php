<?php
namespace Larakit\Base;

use Illuminate\Support\Arr;
use Larakit\Attach;
use Larakit\Exception;
use Larakit\User\Me;
use Symfony\Component\HttpFoundation\File\File;

Trait TraitModelAttachGroup {

    function attachFile($model_file) {
        $this->attaches()->save($model_file);
        return $this;
    }

    public function attaches() {
        return $this->morphMany('Larakit\Model\Attach', 'attachable');
    }

}