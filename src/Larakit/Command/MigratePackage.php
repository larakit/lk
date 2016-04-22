<?php
namespace Larakit\Command;

use Illuminate\Console\Command;
use Larakit\Manager\ManagerPackageMigration;


class MigratePackage extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larakit:migrate-package';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Запустить миграции подключенных larakit-пакетов ';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire() {
        foreach (ManagerPackageMigration::get() as $package) {
            $this->comment('Package "'.$package.'"');
            $this->call('migrate', ['--package' => $package]);
        }
    }

    public function getArguments() {
        return [];
    }


}