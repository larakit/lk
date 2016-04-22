<?php
namespace Larakit\Base;

use Larakit\Attach;

Trait TraitModelCommentable {

    static function bootTraitModelCommentable() {
        /** @var Model $class */
        $class = get_called_class();
        //        $class::observe(new ObserverModelComments());
    }

    public function comments() {
        /** @var Model $this */
        return $this->morphMany('Larakit\Model\Comment', 'commentable');
    }


    public function lastcomments($limit = 3) {
        return $this->comments()->with('author')->latest('created_at')->limit($limit)->get()->reverse();
    }

}