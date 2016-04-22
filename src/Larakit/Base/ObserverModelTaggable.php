<?php
namespace Larakit\Base;

use Carbon\Carbon;
use Larakit\Model\Tag;

Class ObserverModelTaggable {

    public function created($model) {
        Tag::saveTags($model);
    }

    public function saved($model) {
        Tag::saveTags($model);
    }

    public function deleted($model) {
        if (!$model->deleted_at instanceof Carbon) {
            Tag::removeTags($model);
        }
    }

}