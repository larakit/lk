<?php
namespace Larakit\Command;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Larakit\Base\Model;
use Larakit\Exception;
use Larakit\Manager\ManagerMigrator;
use Symfony\Component\Console\Helper\TableSeparator;

class Migrator extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larakit:migrator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Автоматическое приведение моделей к зарегистрированным типам (дерево, аттач)';

    /**
     * Пусть для сохранения сгенерированных данных
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $models = ManagerMigrator::get();
        foreach ($models as $class => $migrators) {
            foreach ($migrators as $data) {
                $this->rows = [];
                //            $this->error(str_repeat('=', 80));
                $this->error(str_pad(' ' . $class . ' ', 80, '=', STR_PAD_BOTH));
                //            $this->error(str_repeat('=', 80));
                $vendor = Arr::get($data, 'vendor');
                $type = Arr::get($data, 'type');
                $v = $vendor ? $vendor . '::' : '';
                $key = $v . 'migrator/' . $type;
                $config = \Config::get($key, false);
                if(!$config) {
                    $this->error('Не обнаружен конфиг мигратора ' . $key);
                    continue;
                }
                $indexes = Arr::get($config, 'indexes', []);
                $fields = Arr::get($config, 'fields', []);
                /** @var Model $model */
                $model = new  $class;
                $table_name = $model->getTable();
                $this->syncFields($table_name, $fields);
                $this->rows[] = new TableSeparator();
                $this->syncIndexes($table_name, $indexes);
                $this->table([
                    '[#]',
                    'Тип',
                    'Имя',
                    'Дополнительные характеристики',
                    'Результат',
                ], $this->rows
                );
            }
        }

        echo PHP_EOL;
        $this->question('Синхронизация зарегистрированных типов структур завершена');
    }

    protected $rows;

    public function syncFields($table_name, $fields)
    {
        //        $table_name = getenv('db.prefix') . $table_name;
        //        $this->comment('Синхронизация полей таблицы ' . $table_name);
        foreach ($fields as $field => $callback) {
            //            dump($table_name, $field);

            if(!\Schema::hasColumn($table_name, $field)) {
                \Schema::table($table_name, $callback);
                $this->rows[] = [
                    '<error>[+]</error>',
                    'добавлен',
                    '<comment>FIELD</comment>',
                    $field,
                    '',
                ];
            } else {
                $this->rows[] = [
                    '<info>[=]</info>',
                    'уже было',
                    '<comment>FIELD</comment>',
                    $field,
                    '',
                ];
            }
        }
    }


    public function syncIndexes($table_name, $indexes)
    {
        $table_name = $table_name;
        //        $this->comment('Синхронизация индексов таблицы ' . $table_name);
        $res = \DB::select('SHOW INDEX from `' . getenv('db.prefix') . $table_name . '`');
        $exists_index = [];
        foreach ($res as $idx) {
            $idx_name = $idx->Key_name;
            $exists_index[$idx_name] = $idx_name;
        }
        \Schema::table($table_name, function (Blueprint $table) use ($table_name, $exists_index, $indexes) {

            foreach ($indexes as $idx_name => $columns) {
                if(!in_array($idx_name, $exists_index)) {
                    $table->index($columns, $idx_name);
                    $this->rows[] = [
                        '<error>[+]</error>',
                        'добавлен',
                        '<comment>INDEX</comment>',
                        $idx_name,
                        implode(', ', $columns),
                    ];
                } else {
                    $this->rows[] = [
                        '<info>[=]</info>',
                        'уже был',
                        '<comment>INDEX</comment>',
                        $idx_name,
                        implode(', ', $columns),
                    ];
                }
            }
        }
        );
    }

    public function getArguments()
    {
        return [];
    }


}