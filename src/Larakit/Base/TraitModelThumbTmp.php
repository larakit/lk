<?php
namespace Larakit\Base;

use Illuminate\Database\Schema\Blueprint;

Trait TraitModelThumbTmp {

    static function bootTraitModelThumbTmp() {
        /** @var Model $class */
        $class = get_called_class();
        $class::observe(new ObserverModelThumbTmp());
    }


}