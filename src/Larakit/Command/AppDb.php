<?php
namespace Larakit\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Larakit\Model\LangItem;
use Larakit\Model\LangItemValue;

class AppDb extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larakit:app-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Настройки подключения к БД';

    /**
     * Пусть для сохранения сгенерированных данных
     * @var string
     */
    protected $path;

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
        $ret                = [];
        $config_default     = \Config::get('database.default');
        $config_connections = \Config::get('database.connections');
        foreach ($config_connections as $name => $config_connection) {
            $this->comment(str_pad('#', 80, '#', STR_PAD_BOTH));
            $this->comment('#' . str_pad('Соединение ' . $name, 88, ' ', STR_PAD_BOTH) . '#');
            $this->comment(str_pad('#', 80, '#', STR_PAD_BOTH));
            $ret = [
                Arr::get($config_connection, 'driver'),
                Arr::get($config_connection, 'database'),
                Arr::get($config_connection, 'read.host'),
                Arr::get($config_connection, 'write.host'),
                Arr::get($config_connection, 'username'),
                Arr::get($config_connection, 'password'),
                Arr::get($config_connection, 'charset'),
                Arr::get($config_connection, 'collation'),
                Arr::get($config_connection, 'prefix'),
            ];
            $this->table(
                [
                    'Driver',
                    'Database',
                    'Read Host',
                    'Write Host',
                    'Username',
                    'Password',
                    'Charset',
                    'Collation',
                    'Prefix',
                ], [$ret]
            );
        }
    }

    public function getArguments() {
        return [];
    }


}