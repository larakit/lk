<?php
namespace Larakit\Helper;

use Illuminate\Support\Arr;

class HelperModel {
    static function getBelongsTo($class_model) {
        $o       = new $class_model;
        $methods = self::getMethods($o);
        $ret     = [];
        foreach ($methods as $method_name => $method) {
            $source = Arr::get($method, 'source');
            if ('belongsTo' != $method_name) {
                if (false !== mb_strpos($source, 'belongsTo(')) {
                    if ('__call' != $method_name) {
                        $ret[$method_name] = get_class($o->{$method_name}()
                                                         ->getRelated());
                    }
                }
            }
        }
        return $ret;
    }

    static function getBelongsToMany($class_model) {
        $o       = new $class_model;
        $methods = self::getMethods($o);
        $ret     = [];
        foreach ($methods as $method_name => $method) {
            $source = Arr::get($method, 'source');
            if ('belongsToMany' != $method_name) {
                if (false !== mb_strpos($source, 'belongsToMany(')) {
                    $ret[$method_name] = get_class($o->{$method_name}()
                                                     ->getRelated());
                }
            }
        }
        return $ret;
    }

    static function getHasMany($class_model) {
        $o       = new $class_model;
        $methods = self::getMethods($o);
        $ret     = [];
        foreach ($methods as $method_name => $method) {
            $source = Arr::get($method, 'source');
            if ('hasMany' != $method_name) {
                if (false !== mb_strpos($source, 'hasMany(')) {
                    $ret[$method_name] = get_class($o->{$method_name}()
                                                     ->getRelated());
                }
            }
        }
        return $ret;
    }

    static function getMethods($model) {
        $ret  = [];
        $refl = new \ReflectionClass($model);
        foreach ($refl->getMethods() as $method) {
            $ret[$method->getName()]['method'] = $method;
            $relf_text                         = file($method->getFileName());

            $source = Arr::only($relf_text, range($method->getStartLine() - 1, $method->getEndLine() - 1));

            $source                            = array_map('trim', $source);
            $ret[$method->getName()]['source'] = implode('', $source);
            $ret[$method->getName()]['start']  = $method->getStartLine();
            $ret[$method->getName()]['end']    = $method->getEndLine();
        }
        return $ret;
    }
}