<?php
namespace Larakit\QuickForm;

use Larakit\Base\Acl;
use Larakit\Base\Model;
use Larakit\Base\TraitEntity;

class LaraFormBuilder {
    use TraitEntity;

    public $namespace = 'larakit';
    /**
     * @var LaraForm
     */
    public $form;
    /**
     * @var Model
     */
    protected $model;

    /**
     * @param $model
     */
    function __construct(Model $model) {
        $this->model = $model;
        $this->form  = new LaraForm($this->model->getEntity());
//        $this->namespace  = $this->getPackageNamespace();
        $this->namespace  = static::getVendor();
    }

    /**
     * @param        $id
     * @param string $method
     * @param null   $attributes
     * @param bool   $trackSubmit
     *
     * @return LaraFormBuilder
     */
    static function factory($model) {
        $class = get_called_class();

        return new $class($model);
    }

    function isFieldEnabled($element_name) {
        return Acl::factory($this->model)->reason('field_' . $element_name) ? false : true;
    }

    /**
     * @param \HTML_QuickForm2_Node $element
     *
     * @return \HTML_QuickForm2_Node
     */
    function initElement(\HTML_QuickForm2_Node $element) {
        \Debugbar::addMessage(($this->namespace ? $this->namespace . '::' : '') . 'models/' . $this->model->getEntity() . '.' . $element->getName());
        $config = \Config::get(($this->namespace ? $this->namespace . '::' : '') . 'models/' . $this->model->getEntity() . '.' . $element->getName());
        if (is_array($config)) {
            foreach ($config as $k => $v) {
                $method = \Str::camel('set_' . $k);
                if (is_callable([$element, $method])) {
                    call_user_func([$element, $method], $v);
                }
            }
        }
        return $element;
    }

    /**
     * @param      $name
     * @param null $container
     *
     * @return ElementTbText
     */
    function add($name, $container = null) {
        $method = \Str::studly('add_' . $name);
        if (!$container) {
            $container = $this->form;
        }

        $element = call_user_func([
            $this,
            $method
        ], $container);
        /* @var ElementTbText $element */
        $element->toggleFrozen(!$this->isFieldEnabled($name));
        if (!$element->getLabel()) {
            $element->setLabel($this->model->getEntityLabel($name));
        }
        return $element;
    }

    /**
     * @return LaraForm
     */
    function build() {
        $rfl = new \ReflectionClass($this);
        foreach ($rfl->getMethods() as $method) {
            $name = $method->getName();
            if (0 === mb_strpos($name, 'add') && ($name != 'add')) {
                $field = \Str::snake(mb_substr($name, 3));
                $this->add($field);
            }
        }

        return $this->form;
    }

}