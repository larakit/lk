<?php
namespace Larakit\QuickForm;

trait TraitContainer {

    /**
     * Appends an element to the container, creating it first
     *
     * The element will be created via {@link HTML_QuickForm2_Factory::createElement()}
     * and then added via the {@link appendChild()} method.
     * The element type is deduced from the method name.
     * This is a convenience method to reduce typing.
     *
     * @param string $m Method name
     * @param array  $a Method arguments
     *
     * @return   LaraFormNode     Added element
     * @throws   \HTML_QuickForm2_InvalidArgumentException
     * @throws   \HTML_QuickForm2_NotFoundException
     */
    public function __call($m, $a) {
        if(preg_match('/^(add)([a-zA-Z0-9_]+)$/', $m, $match)) {
            if($match[1] == 'add') {
                $type = strtolower($match[2]);
                $name = isset($a[0]) ? $a[0] : null;
                $attr = isset($a[1]) ? $a[1] : null;
                $data = isset($a[2]) ? $a[2] : [];

                return $this->addElement($type, $name, $attr, $data);
            }
        }
        if(preg_match('/^(put)([a-zA-Z0-9_]+)$/', $m, $match)) {
            if($match[1] == 'put') {
                $type     = \Illuminate\Support\Str::snake($match[2]);
                $callback = \Larakit\QuickForm\Register::callback($type);
                if(!is_callable($callback)) {
                    throw new \Exception('Элемент с типом ' . $type . ' не зарегистрирован');
                }
                $el = $this->addElement(call_user_func_array($callback, $a));

                return $el;
            }
        }
        throw new \Exception("Fatal error: Call to undefined method " . get_class($this) . "::" . $m . "()",
            E_USER_ERROR);
    }

    final public function setErrors($errors) {
        foreach($errors as $field => $messages) {
//            dump($field, $messages);
            $field = $this->getName() ? $this->getName() . '['.$field.']' : $field;
            if(is_array($messages)) {
//                dump($this->getName());
                $groups = $this->getElementsByName($field);
                foreach($groups as $group) {
                    $group->setErrors($messages);
                }
            } else {
                $elements = $this->getElementsByName($field);
                foreach($elements as $el) {
                    if(!$el->getError()) {
                        $f = str_replace(['][', '[', ']', '_'],['.', '.', '', ' '], $field);
                        $el->setError(str_replace($f,'"'.$el->getLabel().'"', $messages));
                    }
                }
            }
        }
    }

}