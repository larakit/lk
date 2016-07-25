<?php
namespace Larakit\QuickForm;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Larakit\QuickForm\Register;

class CommandQuickformIde extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larakit:ide:quickform';
    const NOT_CALLABLE = 'no-callable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерация автокомплита для магических методов LaraForm';

    /**
     * Пусть для сохранения сгенерированных данных
     *
     * @var string
     */
    protected $path;

    protected $form_element_methods_get = [
        'getLabel',
        'getDesc',
        'getExample',
        'getExampleIsAppend',
        'getPlaceholder',
        'getPrepend',
        'getAppend',
        'getWrapClass',
        'getIsInline',
        'getTpl',
    ];
    protected $form_element_methods_set = [
        'setLabel',
        'setDesc',
        'setExample',
        'setExampleIsAppend',
        'setPlaceholder',
        'setPrepend',
        'setAppend',
        'setAppendClear',
        'setWrapClass',
        'setIsInline',
        'setTpl',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    protected $ret     = [];
    protected $classes = [];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        if(is_dir(app_path('Http/Forms'))){
            foreach(\File::allFiles(app_path('Http/Forms')) as $f){
                $class = \App::getNamespace().'Http\Forms\\'.str_replace('.php', '', $f->getFilename());
                Register::container($class);
            }
        }
        //добавим геттеры в контейнеры
        //добавим сеттеры в контейнеры
        foreach (Register::$containers as $container) {
            $this->info($container);
            foreach ($this->form_element_methods_get as $method) {
                $params = $this->getMethodParams($container, $method);
                $this->putPhpDoc($container, 'string', $method, $params);
            }
            foreach ($this->form_element_methods_set as $method) {
                $params = $this->getMethodParams($container, $method);
                $this->putPhpDoc($container, $container, $method, $params);
            }
        }
        //добавим элементы в контейнеры
        $i=0;
        foreach (Register::$elements as $name => $data) {
            $this->info((++$i).') '.$name);
            $class = Arr::get($data, 'class');
            foreach (Register::$containers as $container) {
                $params = $this->getMethodParams($class, 'laraform');
                $method = \Illuminate\Support\Str::camel('put_' . $name);
                $this->putPhpDoc($container, $class, $method, $params);
            }
            foreach ($this->form_element_methods_get as $method) {
                $params = $this->getMethodParams($class, $method);
                $this->putPhpDoc($class, 'string', $method, $params);
            }
            foreach ($this->form_element_methods_set as $method) {
                $params = $this->getMethodParams($class, $method);
                $this->putPhpDoc($class, $class, $method, $params);
            }
        }
        $data    = $this->ret;
        $content = \View::make(
            'quickform::ide-helper', [
                'data' => $data,
                'tab'  => "\t",
            ]
        );
        file_put_contents(base_path() . '/_ide_helper_laraform.php', $content);
        $this->info('IDE helper сгенерирован для ' . count($this->classes).' элементов');
    }

    /**
     * Через рефлексию получаем список и порядок параметров с дефолтными значениями
     *
     * @param $class
     * @param $method
     *
     * @return string
     */
    function getMethodParams($class, $method) {
        if(!is_callable([$class, $method])){
            return self::NOT_CALLABLE;
        }
        $reflectMethod = new \ReflectionMethod($class, $method);
        $params        = [];
        foreach ($reflectMethod->getParameters() as $param) {
            $_param = '$' . $param->getName();
            if ($param->isDefaultValueAvailable()) {
                $default = $param->getDefaultValue();
                switch (mb_strtolower(gettype($default))) {
                    case 'null':
                        $default = 'null';
                        break;
                    case 'array':
                        $default = '[]';
                        break;
                    case 'string':
                        $default = '\'' . $default . '\'';
                        break;
                    case 'boolean':
                        $default = $default ? 'true' : 'false';
                        break;
                    default:
                        $default = $default;
                        break;
                }
                $_param .= ' = ' . $default;
            }
            $params[] = $_param;
        }
        return implode(', ', $params);

    }

    function putPhpDoc($class, $return, $method, $params) {
        if(self::NOT_CALLABLE==$params){
            return false;
        }
        $class = trim($class, '\\');
        $this->classes[$class] = $class;
        $namespace             = '';
        if (mb_strpos($class, '\\')!==false) {
            $namespace = explode('\\', trim($class, '\\'));
            $class     = array_pop($namespace);
            $namespace = implode('\\', $namespace);
        }
        if (mb_strpos($return, '\\')!==false) {
            $return_namespace = explode('\\', trim($return, '\\'));
            $return_class     = array_pop($return_namespace);
            $return_namespace = implode('\\', $return_namespace);
            if (trim($return_namespace, '\\') == trim($namespace, '\\')) {
                $return = $return_class;
            }
        }
        $ret                             = [];
        $ret[]                           = '* @method';
//        $ret[]                           = str_pad($return, 45, ' ', STR_PAD_RIGHT);
        $ret[]                           = $return;
        $ret[]                           = $method;
        $ret[]                           = '(' . $params . ')';
        $this->ret[$namespace][trim($class, '\\')][$method] = implode(' ', $ret);
    }

    public function getArguments() {
        return [];
    }


}