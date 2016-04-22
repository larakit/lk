<?php
namespace Larakit\Base;

use Illuminate\Support\Arr;

class DataValidator {
    use TraitEntity;

    /**
     * Массив данных для проверки
     *
     * @var array
     */
    protected $data;

    /**
     * Массив названий полей
     *
     * @var array
     */
    protected $labels = [];

    /**
     * Массив кастомных сообщений об ошибке
     *
     * @var array
     */
    protected $messages = [];
    protected $validator;

    /**
     * Массив правил валидации
     *
     * @var array
     */
    protected $rules    = [];
    protected $field_id = 'id';


    /**
     * @var \Eloquent
     */

    function __construct() {
        $class   = new \ReflectionClass(get_called_class());
        $methods = $class->getMethods();
        foreach ($methods as $method) {
            $name = $method->getName();
            if ('rule' == mb_substr($name, 0, 4)) {
                $rule   = [];
                $rule[] = static::getVendor();
                $rule[] = static::getEntity();
                $rule[] = \Str::snake(mb_substr($name, 4));
                $ext    = [];
                $ext[]  = $class->getName();
                $ext[]  = $name;
                $rule   = implode('_', $rule);
                $ext    = implode('@', $ext);
                \Validator::extend($rule, $ext);
            }
        }
    }

    /**
     * Передать данные на проверку
     *
     * @param $data
     *
     * @return $this
     */
    function setData($data) {
        $this->data = $data;

        return $this;
    }

    function getErrors($as_string = false) {
        if (!$this->validator->fails()) {
            return null;
        }
        $errors = $this->validator->errors()
                                  ->getMessages();
        if (!$as_string) {
            $ret = $errors;
        }
        else {
            foreach ($this->getErrorFields() as $field) {
                $e       = $this->getError($field);
                $ret[$e] = $e;
            }

            $ret = implode('<br />', array_unique($ret));
        }

        return $ret;
    }

    function getData() {
        return $this->data;
    }

    function getErrorFields() {
        return array_keys($this->getErrors());
    }

    function getError($name) {
        return $this->validator->errors()
                               ->first($name);
    }


    function setLabels($labels) {
        $this->labels = $labels;

        return $this;
    }

    function setLabel($k, $v) {
        $this->labels[$k] = $v;

        return $this;
    }

    /**
     * Проверка данных
     *
     * @return bool
     */
    function validate() {
        $rules = [];
        foreach ($this->rules as $field => $_rules) {
            $this->rules[$field] = array_values($_rules);
        }

        $replacers = [];
        $id        = (int)Arr::get($this->data, $this->field_id);

        foreach ($this->rules as $field => $_rules) {
            foreach ($_rules as $rule) {
                if (is_string($rule)) {
                    $rules[$field][]           = str_replace('{id}', $id, $rule);
                    $replacer_name             = explode(":", $rule);
                    $replacer_rule             = array_shift($replacer_name);
                    $replacers[$replacer_rule] = [];
                }
                else {
                    $rules[$field][] = $rule;
                }

            }
        }
        if (count($replacers)) {
            /*foreach ($replacers as $rule => $vb) {
//                dd($replacers);
                \Validator::replacer(
                    $rule,
                    function ($message, $attribute, $rule, $parameters) {
                        $fields    = array_keys($this->data);
                        $replacers = array_map(
                            function ($value) {
                                return ':value=' . $value;
                            },
                            $fields
                        );
                        $replacers = array_merge(
                            $replacers,
                            array_map(
                                function ($value) {
                                    return ':label=' . $value;
                                },
                                $fields
                            )
                        );
                        return str_replace($replacers, $this->data + $this->labels, $message);
                    }
                );
            }*/
        }
        //        dd($this->messages);
        //        dd($this->labels);
        //        dump($this->labels);
        $this->validator = \Validator::make($this->data, $rules, $this->messages, $this->labels);
        foreach ($this->data as $k => $v) {
            $values['value__' . $k] = $v;
        }
        $this->validator->setValueNames($values);
        //        dd($this->validator);

        //        $this->validator->addReplacer()
        return !$this->validator->fails();
    }

    /**
     * @param $field
     * @param $callback
     *
     * @return $this
     */
    function addRule($field, $callback) {
        if (isset($this->rules[$field])) {
            array_unshift($this->rules[$field], $callback);
        }

        return $this;
    }

    /**
     * @param $field
     *
     * @return $this
     */
    function addRuleSometimes($field) {
        return $this->addRule($field, 'sometimes');
    }

    /**
     * Кастомное сообщение об ошибке
     *
     * @param      $rule
     * @param      $message
     * @param null $element
     *
     * @return $this
     */
    function addMessage($rule, $message, $element = null) {
        $this->messages[$rule . ($element ? '.' . $element : '')] = $message;

        return $this;
    }

    function setMessages($messages) {
        $this->messages = $messages;

        return $this;
    }

    function setRules($rules) {
        $this->rules = (array)$rules;

        return $this;
    }

    //   function ruleExtendRule(){
    //       dd();
    //    }
}