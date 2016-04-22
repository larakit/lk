<?php
namespace Larakit\Widget;

use Illuminate\Support\Arr;
use Larakit\Helper\HelperEntity;

class WidgetList extends \Larakit\Base\Widget {

    function setBaseRoute($route = null, $entity = null) {
        if ($route) {
            $parsed = HelperEntity::parseBaseRoute($route);
            return $this->set('vendor', Arr::get($parsed, 'vendor'))
                        ->set('section', Arr::get($parsed, 'section'))
                        ->set('route', $route)
                        ->set('base_url', \URL::route($route))
                        ->set('entity', $entity ? $entity : Arr::get($parsed, 'entity'));
        }
        return $this;
    }

    function setRowType($val) {
        return $this->set('row_type', $val);
    }

    function setVendor($val) {
        return $this->_(__FUNCTION__, $val);
    }

    function setEntity($val) {
        return $this->_(__FUNCTION__, $val);
    }

    function setInsertMode($val) {
        return $this->_(__FUNCTION__, $val);
    }

    function setInsertModeAppend() {
        return $this->setInsertMode('append');
    }

    function setInsertModePrepend() {
        return $this->setInsertMode('prepend');
    }

    function setMaxItems($val) {
        return $this->_(__FUNCTION__, (int)$val);
    }

    function setJsFilters($val) {
        return $this->set('js_filters', $val);
    }

    function setModels($models) {
        return $this->set('models', $models);
    }

    function with($relations) {
        return $this->set('relations', $relations);
    }

    function tpl() {
        return 'larakit::!.widgets.list';
    }

}