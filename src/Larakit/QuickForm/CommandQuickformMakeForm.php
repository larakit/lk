<?php
namespace Larakit\QuickForm;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class CommandQuickformMakeForm extends GeneratorCommand {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larakit:make:quickform';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new quickform class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Form';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() {
//        DummyName
        return __DIR__ . '/stubs/quickform.stub';
    }

    protected function replaceClass($stub, $name) {
        $stub = parent::replaceClass($stub, $name);

        return str_replace('DummyName', trim(Str::snake($this->argument('name'))), $stub);
    }

    protected function getNameInput() {
        return Str::studly(parent::getNameInput() . '_form');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace) {
        return $rootNamespace . '\Quickforms';
    }

    public function fire(){
        parent::fire();
        $this->call('larakit:ide:quickform');
    }

}