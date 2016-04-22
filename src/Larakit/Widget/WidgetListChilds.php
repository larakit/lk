<?php
namespace Larakit\Widget;

class WidgetListChilds extends \Larakit\Base\Widget {
    /**
     * @param $val
     *
     * @return $this
     */
    function setSortable($val) {
        return $this->set('sortable', (bool)$val);
    }

    /**
     * @param $val
     *
     * @return $this
     */
    function setVendor($val) {
        return $this->set('vendor', $val);
    }

    /**
     * @param $val
     *
     * @return $this
     */
    function setEntity($val) {
        return $this->set('entity', $val);
    }

    /**
     * @param $val
     *
     * @return $this
     */
    function setRowType($val) {
        return $this->set('row_type', $val);
    }

    /**
     * @param $val
     *
     * @return $this
     */
    function setRoute($val) {
        return $this->set('route', $val);
    }

    /**
     * @param $val
     *
     * @return $this
     */
    function setFilter($val) {
        return $this->set('filter', $val);
    }

    function setCanAdd($val) {
        return $this->set('can_add', $val);
    }

    /**
     * @param $models
     *
     * @return $this
     */
    function setModels($models) {
        $this->models = $models;

        return $this;
    }

    function tpl() {
        return 'larakit::!.widgets.list_childs';
    }

    function init() {

    }

    function toHtml() {
        $this->init();

        return parent::toHtml();
    }

}