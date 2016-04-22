<?php
namespace Larakit\Base;

use Illuminate\Support\Arr;
use Larakit\Helper\HelperText;

trait TraitEntity {

    static function getEntityPrefix() {
        return '';
    }

    static function getEntitySuffix() {
        return '';
    }


    static function getEntity() {
        $class = get_called_class();
        $r     = new \ReflectionClass($class);

        $str = \Str::snake(str_replace($r->getNamespaceName() . '\\', '', $class));

        if (static::getEntityPrefix()) {
            $str = mb_substr($str, mb_strlen(static::getEntityPrefix()) + 1);
        }
        if (static::getEntitySuffix()) {
            $str = mb_substr($str, 0, 0 - mb_strlen(static::getEntitySuffix()) - 1);
        }

        return trim($str, '_');
    }

    static function getVendor() {
        $class = get_called_class();
        $class = str_replace('\Controller\\', '\\', $class);
        $data  = explode('\\', $class);


        $count = substr_count($class, '\\');
        if ($count >= 3) {
            return \Str::snake(Arr::get($data, 0) . Arr::get($data, 1), '-');
        }

        return \Str::snake(Arr::get($data, 0));
    }

    static function getStudlyVendor() {
        $class = get_called_class();
        $class = str_replace('\Controller\\', '\\', $class);
        $data  = explode('\\', $class);
        $count = substr_count($class, '\\');
        if ($count >= 3) {
            return Arr::get($data, 0) . '\\' . Arr::get($data, 1);
        }

        return \Str::studly(Arr::get($data, 0));
    }

    static function makeStudlyVendor($vendor_snake) {
        return \Str::studly(str_replace('-', '\_', $vendor_snake));
    }


    static function makeModelClass($vendor, $entity) {
        $class = $vendor;
        $data  = explode('\\', $class);
        $count = substr_count($class, '\\');
        if ($count >= 3) {
            $class = \Str::snake(Arr::get($data, 0) . Arr::get($data, 1), '-');
        } else {
            $class = \Str::snake(Arr::get($data, 0));
        }

        return \Str::studly($class) . '\Model\\' . \Str::studly($entity);
    }

    static function getClassAccessor() {
        return static::getStudlyVendor() . '\Accessor\Accessor' . \Str::studly(static::getEntity());
    }

    static function getClassAcl() {
        return static::getStudlyVendor() . '\Acl\Acl' . \Str::studly(static::getEntity());
    }

    static function getAcl($id = 0) {
        return Acl::factory(static::getClassModel(), $id);
    }

    //    static function getClassController() {
    //        return \Str::studly(static::getVendor()) . '\Controller\Controller' . \Str::studly(static::getEntity());
    //    }


    /**
     * @param      $data
     * @param null $form
     *
     * @return DataValidator
     */
    static function getDataValidator($data, $form = null) {
        $v = static::getVendor();
        if ($v) {
            $v .= '::';
        }
        $filters = (array)\Config::get($v . 'models/' . static::getEntity() . '/filters');
        $rules   = (array)\Config::get($v . 'models/' . static::getEntity() . '/rules');
        $rules = Arr::only($rules, array_keys($data));
//        dump($data);
//        dd($rules);





        //        dump($rules);
        //        \Debugbar::addMessage($rules);
        \Debugbar::addMessage($data);
        $labels   = static::getEntityLabels();
        $messages = static::getEntityValidationMessage();
        \Debugbar::addMessage($labels);
        \Debugbar::addMessage($messages);
        foreach ($filters as $field => $field_filters) {
            //            $field_filters = ['trim'] + $field_filters;
            foreach ($field_filters as $field_filter) {
                $elements  = $form ? $form->getElementsByName($field) : [];
                $options   = [];
                $options[] = Arr::get($data, $field);
                if ($field_filter instanceof \Closure) {
                    $options[] = $data;
                    $options[] = $elements;
                }
                if (isset($data[$field])) {
                    $data[$field] = call_user_func_array($field_filter, $options);
                }
            }
        }
        $class     = static::getClassDataValidator();
        $validator = new $class;

        return $validator->setData((array)$data)->setRules((array)$rules)->setLabels((array)$labels)->setMessages((array)$messages);
    }

    static function getClassDataValidator() {
        $class = static::getStudlyVendor() . '\DataValidator\\DataValidator' . \Str::studly(static::getEntity());
        if (!class_exists($class)) {
            $class = '\Larakit\Base\DataValidator';
        }

        return $class;
    }

    static function getClassForm() {
        return static::getStudlyVendor() . '\Form\Form' . \Str::studly(static::getEntity());
    }

    static function getSection() {
        return '';
    }

    static function getClassFormFilter() {
        return static::getStudlyVendor() . '\FormFilter\FormFilter' . \Str::studly(static::getEntity()) . \Str::studly(static::getSection());
    }

    static function getFormFilter($model) {
        return FormFilter::factory($model);
    }

    static function getClassModel() {
        return static::getStudlyVendor() . '\Model\\' . \Str::studly(static::getEntity());
    }

    static function getModel($id = null) {
        $class = static::getClassModel();

        return $class::find($id);
    }


    static function getEntityLabel($field) {
        return Arr::get(static::getEntityLabels(), $field);
    }

    static function getEntityExample($field) {
        $ex = Arr::get(static::getEntityExamples(), $field);
        if ([''] == $ex) {
            return null;
        }
        return $ex;
    }

    static function getEntityDesc($field) {
        return Arr::get(static::getEntityDescriptions(), $field);
    }

    static function getEntityValidationMessage() {
        $tmp = static::translate('messages');
        if (is_string($tmp)) {
            $tmp = [];
        }
        $messages = [];
        foreach ($tmp as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $_k => $_v) {
                    $messages[$k . '.' . $_k] = $_v;
                }
            } else {
                $messages[$k] = $v;
            }
        }

        return $messages;
    }

    static function getEntityLabels() {

        return static::translate('labels');
    }

    static function getEntityExamples() {
        return static::translate('examples');
    }

    static function getEntityDescriptions() {
        return static::translate('descriptions');
    }

    static function getEntityNamePlural($cnt) {
        return HelperText::plural($cnt,
            static::translate('plurals.1'),
            static::translate('plurals.2'),
            static::translate('plurals.5'),
            static::translate('plurals.0'));
    }

    static function getEntityName($cnt, $case = Model::NAME_CASE_I) {
        if (!$cnt)
            return '';
        if (1 == $cnt) {
            return static::getEntityNameCaseSingular($case);
        } else {
            return static::getEntityNameCasePlural($case);
        }
    }

    static function getEntityNameCaseSingular($case = Model::NAME_CASE_I) {
        return static::translate('cases/singular.' . $case);
    }

    static function getEntityNameCasePlural($case = Model::NAME_CASE_I) {
        return static::translate('cases/plural.' . $case);
    }

    static function translate($context, $params = []) {
        return laralang(static::getVendor() . '::models/' . static::getEntity() . '/' . $context, $params);
    }

    static function translateAction($context, $params = []) {
        $section = static::getSection();
        if ($section) {
            $section .= '/';
        }
        $path = static::getVendor() . '::models/' . $section . 'actions/' . static::getEntity() . '.' . $context;
        return laralang($path, $params);
    }

    static function config($path, $default = null) {
        $v = self::getVendor();
        if ($v) {
            $v = $v . '::';
        }
        return \Config::get($v . 'models/' . static::getEntity() . '/' . $path, $default);
    }

    static function isSoftDelete(){
        return method_exists(static::getClassModel(), 'withTrashed');
    }

}