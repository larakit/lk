<?php
namespace Larakit\Base;

use Larakit\Thumb;

Class ObserverModelThumbTmp {

    public function created($model) {
        $model_class = get_class($model);
        $session = 'tmp_upload_' . $model_class::getEntity();
        $file = \Session::get($session);
        if ($file) {
            if (file_exists($file)) {
                Thumb::fromModel($model, 'item')
                     ->processing($file);
                unlink($file);
            }
            \Session::forget($session);
        }
    }

}