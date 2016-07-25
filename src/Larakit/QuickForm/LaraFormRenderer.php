<?php
namespace Larakit\QuickForm;

class LaraFormRenderer extends \HTML_QuickForm2_Renderer_Array {

    /**#@+
     * Implementations of abstract methods from {@link HTML_QuickForm2_Renderer}
     */
    public function renderElement(\HTML_QuickForm2_Node $element) {
        $ary = $this->buildCommonFields($element) + [
                'html'              => $element->__toString(),
                'value'             => $element->getValue(),
                'type'              => $element->getType(),
                'label'             => $element->getLabel(),
                'desc'              => $element->getDesc(),
                'example'           => $element->getExample(),
                'placeholder'       => $element->getPlaceholder(),
                'example_is_append' => $element->getExampleIsAppend(),
                'prepend'           => $element->getPrepend(),
                'append'            => $element->getAppend(),
                'tpl'               => $element->getTpl(),
                'required'          => $element->isRequired(),
                'is_inline'         => $element->getIsInline(),
                'wrap_class'        => $element->getWrapClass(),
                'checked'           => $element->getAttribute('checked'),
            ];
        $this->pushScalar($ary);
    }

    public function startContainer(\HTML_QuickForm2_Node $container) {
        $ary = $this->buildCommonContainerFields($container) + [
                'required'   => $container->isRequired(),
                'label'      => $container->getLabel(),
                'desc'       => $container->getDesc(),
                'type'       => $container->getType(),
                'wrap_class' => $container->getWrapClass(),
                'tpl'        => $container->getTpl(),
                'is_inline'  => $container->getIsInline(),
                'class'      => $container->getAttribute('class'),
            ];
        $this->pushContainer($ary);
    }

    /**
     * Stores an array representing "scalar" element in the form array
     *
     * @param array $element
     */
    public function pushScalar(array $element) {
        if(!empty($element['required'])) {
            $this->hasRequired = true;
        }
        if(empty($this->containers)) {
            $this->array += $element;
        } else {
            $this->containers[count($this->containers) - 1][\Illuminate\Support\Arr::get($element, 'id')] = $element;
        }
    }

    public function startGroup(\HTML_QuickForm2_Node $group) {
        $ary = $this->buildCommonContainerFields($group) + [
                'label'      => $group->getLabel(),
                'desc'       => $group->getDesc(),
                'required'   => $group->isRequired(),
                'type'       => $group->getType(),
                'tpl'        => $group->getTpl(),
                'is_inline'  => $group->getIsInline(),
                'wrap_class' => $group->getWrapClass(),
                'class'      => $group->getAttribute('class'),
            ];
        if($separator = $group->getSeparator()) {
            $ary['separator'] = [];
            for($i = 0, $count = count($group); $i < $count - 1; $i++) {
                if(!is_array($separator)) {
                    $ary['separator'][] = (string) $separator;
                } else {
                    $ary['separator'][] = $separator[$i % count($separator)];
                }
            }
        }
        $this->pushContainer($ary);
    }

    /**
     * Stores an array representing a Container in the form array
     *
     * @param array $container
     */
    public function pushContainer(array $container) {
        if(!empty($container['required'])) {
            $this->hasRequired = true;
        }
        if(empty($this->containers)) {
            $this->array += $container;
            $this->containers = [&$this->array['elements']];
        } else {
            $cntIndex                              = count($this->containers) - 1;
            $myIndex                               = count($this->containers[$cntIndex]);
            $myIndex                               = \Illuminate\Support\Arr::get($container, 'id');
            $this->containers[$cntIndex][$myIndex] = $container;
            $this->containers[$cntIndex + 1]       =& $this->containers[$cntIndex][$myIndex]['elements'];
        }
    }

}