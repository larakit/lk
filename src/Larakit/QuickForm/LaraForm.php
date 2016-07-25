<?php
namespace Larakit\QuickForm;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Larakit\Event\Event;
use Larakit\StaticFiles\Js;

class LaraForm extends \HTML_QuickForm2 {

    use TraitNode, TraitContainer;

    protected $tpl             = 'quickform::laraform';
    protected $validator_class = null;

    public function __construct($id = null, $method = 'post', $attributes = null, $trackSubmit = true) {
        $attributes['action'] = Arr::get($attributes, 'action', \URL::current());
        parent::__construct($id, $method, $attributes, $trackSubmit);
        if(method_exists($this, 'build')) {
            $this->build();
        }
        $this->initValidator();
        $this->addHidden('_token', ['value' => csrf_token()]);
    }

    protected $validator_rules;
    protected $validator_messages = [];

    function getValidatorClass() {
        $class = Event::filter('validator::' . get_called_class(), $this->validator_class);
        if(!is_a($class, \Larakit\ValidateBuilder::class, true)) {
            return null;
        }

        return $class;
    }

    protected function initValidator() {
        $class = $this->getValidatorClass();
        if($class) {
            $validator                = new $class;
            $this->validator_messages = $validator->messages();
            $this->validator_rules    = $validator->rules();
            foreach($this->validator_rules as $element_name => $element_rules) {
                if(false !== mb_strpos($element_name, '.')) {
                    $element_name = str_replace('.', '][', $element_name) . ']';
                    $e            = explode(']', $element_name, 2);
                    $element_name = implode('', $e);
                }
                foreach($this->getElementsByName($element_name) as $el) {
                    $element_rules = explode('|', $element_rules);
                    foreach($element_rules as $rule) {
                        $_rule       = explode(':', $rule);
                        $rule_name   = Arr::get($_rule, 0);
                        $rule_params = Arr::get($_rule, 1);
                        $rule_params = explode(',', $rule_params);
                        $method      = Str::camel('rule_' . $rule_name);
                        if(method_exists($el, $method)) {
                            call_user_func_array([$el, $method], $rule_params);
                        }
                    }
                }
            }
        }

        return $this;
    }

    function ruleApply(\HTML_QuickForm2_Node &$el, $qf_rule, $laravel_rule, $params = null, $replaces = []) {
        switch(true) {
            case isset($this->validator_messages[$el->getNameDot() . '.' . $laravel_rule]):
                $message = $this->validator_messages[$el->getNameDot() . '.' . $laravel_rule];
                break;
            case isset($this->validator_messages[$laravel_rule]):
                $message = $this->validator_messages[$laravel_rule];
                break;
            default:
                $message = \Lang::get('validation.' . $laravel_rule);
                break;
        }
        $message = str_replace(':attribute', '"' . $el->getLabel() . '"', $message);
        $message = str_replace(array_keys($replaces), array_values($replaces), $message);
        $el->addRule($qf_rule, $message, $params, \HTML_QuickForm2_Rule::ONBLUR_CLIENT);
    }

    static function getNameFromDot($dot) {
        $element_name = str_replace('.', '][', $dot) . ']';
        $e            = explode(']', $element_name, 2);

        return implode('', $e);
    }

    public function validate() {
        $res = $this->isSubmitted() && parent::validate();
        if($res) {
            $rules = [];
            $names = [];
            if($this->validator_rules) {
                foreach($this->validator_rules as $f => $element_rules) {
                    $name     = LaraForm::getNameFromDot($f);
                    $elements = $this->getElementsByName($name);
                    if(count($elements)) {
                        $rules[$f] = $element_rules;
                        $names[$f] = '"' . Arr::get($elements, 0)->getLabel() . '"';
                    }
                }
                $validator = \Validator::make($this->getValue(), $rules, $this->validator_messages, $names);
                if($validator->fails()) {
                    $errors = [];
                    foreach($validator->errors()->toArray() as $k => $v) {
                        Arr::set($errors, $k, Arr::get($v, 0));
                    }
                    //dd($validator->errors()->toArray(), $errors);
                    $this->setErrors($errors);
                    $res = false;
                }
            }
        }

        return $res;
    }

    protected $larakit_values = [];

    protected function _initValues() {
        if(count($this->larakit_values)) {
            // data source with default values:
            $this->addDataSource(new \HTML_QuickForm2_DataSource_Array($this->larakit_values));
            $this->larakit_values = [];
        }
    }

    function setAction($action) {
        return $this->setAttribute('action', $action);
    }

    protected function setMethod($method) {
        $this->addHidden('_method', ['value' => $method])->setAttribute('method', 'post');

        return $this;
    }

    function setMethodDelete() {
        return $this->setMethod('DELETE');
    }

    function setMethodPatch() {
        return $this->setMethod('PATCH');
    }

    function setMethodPut() {
        return $this->setMethod('PUT');
    }

    function initValues($values) {
        $this->larakit_values = array_merge($this->larakit_values, $values);

        return $this;
    }

    function getFieldValue($name) {
        return Arr::get($this->getValue(), $name);
    }

    public function render(\HTML_QuickForm2_Renderer $renderer) {
        $this->_initValues();

        return parent::render($renderer);
    }

    function __toString() {
        try {
            return $this->toString($this->tpl) . '';
        }
        catch(\Exception $e) {
            return $e->getMessage();
        }
    }

    function toString($tpl) {
        $renderer = \HTML_QuickForm2_Renderer::factory('larakit_form');
        $this->render($renderer);
        larajs()->addInline($renderer->getJavascriptBuilder()->getFormJavascript($this->getId(), false));

        return \View::make($tpl,
            [
                'is_submited' => (bool) $this->isSubmitted(),
                'form'        => $renderer->toArray(),
                'meta'        => \Larakit\QuickForm\Register::$elements,
            ])->__toString();
    }

    function onSubmit($code) {
        Js::instance()->addOnload('
        $("#' . $this->getId() . '").on("submit",function(){
            '.$code.'
            return false;
        });
        ');

        return $this;
    }
    function jsSubmit($callback) {
        Js::instance()->addOnload('
        $("#' . $this->getId() . '").on("submit",function(){
            var form = $(this);
            $.post(form.attr("action"), form.serialize(), function (data) {
                if ("success" == data.result) {
                    '.$callback.'
                } else {
                    toaster("error", data.message);
                }
            });
            return false;
        });
        ');

        return $this;
    }

}