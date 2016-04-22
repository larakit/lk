<?php
namespace Larakit\Base;

use Illuminate\Support\Arr;

trait TraitControllerAjax {
    protected $traitAjaxParams = [
        'result' => 'success'
    ];

    /**
     * @param $k
     * @param $v
     *
     * @return $this
     */
    function traitAjax_set($k, $v = null) {
        if (is_null($v) && is_array($k)) {
            foreach ($k as $key => $value) {
                $this->traitAjax_set($key, $value);
            }
        } else {
            Arr::set($this->traitAjaxParams, $k, $v);
        }
        return $this;
    }

    function traitAjax_response() {
        $data    = [
            'vendor'  => static::getVendor(),
            'package' => static::getEntity()
        ];
        $ret     = array_merge($data, $this->traitAjaxParams);
        $message = Arr::get($ret, 'message');
        $message = htmlspecialchars($message);
        Arr::set($ret, 'message', $message);
        return $ret;
    }


}