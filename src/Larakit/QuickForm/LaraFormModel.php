<?php
namespace Larakit\QuickForm;

use Larakit\Base\TraitEntity;

class LaraFormModel extends LaraForm {
    use TraitEntity;

    protected $data_id;
    protected $validator;

    static function getEntityPrefix() {
        return 'Form';
    }

    public function __construct($id, $method = 'post', $attributes = null, $trackSubmit = true) {
        parent::__construct($id, $method, $attributes, $trackSubmit);
        $this->build();
    }

    function build() {
        //        $this->addEmail($this);
    }

}