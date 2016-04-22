<?php
namespace Larakit\Base;

use Larakit\Attach;

Trait TraitModelTaggable {


    static function bootTraitModelTaggable() {
        /** @var Model $class */
        $class = get_called_class();
        $class::observe(new ObserverModelTaggable());
    }


    function tags() {
        /** @var Model $this */
        return $this->morphToMany('Larakit\Model\Tag', 'taggable', 'larakit__taggables');
    }

}