<?php
namespace Larakit\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Larakit\Base\Model;
use Larakit\Event;
use Larakit\Thumb;
use Larakit\Route\RouteThumb;
use Larakit\Webconfig;
use Symfony\Component\Finder\SplFileInfo;

class Sync extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larakit:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Синхронизация базы данных с production';

    /**
     * Пусть для сохранения сгенерированных данных
     *
     * @var string
     */
    protected $path;

    protected $tables          = [];
    protected $connection_name = 'sync';
    protected $tmp_dir         = '';

    protected $ignore_tables = [
        'migrations'
    ];

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


        $this->createTmpDir();
        $this->getTables();
        $this->dumpTables();
        $this->importTables();
        $this->clearTmpDir();
        $this->importThumbs();
        $this->staticDeploy();

    }

    protected function importThumbs() {
        $this->info('Начало импорта превьюшек');

        $list  = RouteThumb::$list;
        $files = [];
        foreach ($list as $item) {
            /* @var $model Model */
            $model = $this->parseModel($item);
            if (!$model) {
                continue;
            }

            $items = $model::all();
            foreach ($items as $model_item) {
                $config_names = \Config::get(Arr::get($item, 'vendor') . '::models/' . Arr::get($item,
                        'package') . '/thumbs');
                $sizes        = (array)array_keys(Arr::get($config_names, 'sizes', []));
                foreach ($sizes as $size) {
                    $thumb       = Thumb::fromModel($model_item, $size);
                    $thumb_sizes = (array)array_keys($thumb->getConfig());

                    foreach ($thumb_sizes as $s) {
                        $url         = $thumb->makeUrl($s);
                        $files[$url] = [
                            'url'   => $url,
                            'thumb' => $thumb,
                            'size'  => $s
                        ];
                    }
                    // Загруженная картинка
                    $url         = $thumb->makeUrl();
                    $files[$url] = [
                        'url'   => $url,
                        'thumb' => $thumb,
                        'size'  => ''
                    ];
                }
            }
        }

        $this->downloadFiles($files);

        $this->info('Превьюшки успешно импортированы');
    }

    protected function downloadFiles(array $files) {
        $progress = new \Symfony\Component\Console\Helper\ProgressBar($this->output, sizeof($files));
        $progress->setFormat('debug');
        $progress->start();

        $production_url = Webconfig::get('larakit.sync_url');//'http://bmmaket.bmdemo.ru';
        //        dd($production_url);
        $path = public_path();
        foreach ($files as $file) {
            try {
                $url = trim(Arr::get($file, 'url'), '/');
                if ($url) {
                    $thumb = Arr::get($file, 'thumb');
                    $size  = Arr::get($file, 'size');
                    if (!file_exists(dirname($path . '/' . $url))) {
                        mkdir(dirname($path . '/' . $url), 0777, true);
                    }
                    //                    $this->info('');
                    //                    $this->info('-----------');
                    //                    $this->info(trim($production_url, '/') . '/' . $url);
                    //                    $this->info($path . '/' . $url);
                    //                    $this->info('-----------');
                    copy(trim($production_url, '/') . '/' . $url, $path . '/' . $url);

                    $selector   = [];
                    $selector[] = 'img[data-entity=' . $thumb->entity() . '][data-vendor=' . $thumb->vendor() . '][data-thumb-size=' . $size . '][data-thumb-name=' . $thumb->getName() . '],';
                    $selector[] = '.js-list[data-entity=' . $thumb->entity() . '][data-vendor=' . $thumb->vendor() . ']';
                    $selector[] = '.js-item[data-id=' . $thumb->getId() . ']';
                    $selector[] = '.js-btn[data-thumb-size=' . $size . '][data-thumb-name=' . $thumb->getName() . ']';
                    $selector[] = 'img';

                    $data = [
                        'url'      => $thumb->getUrl($size) . '?' . microtime(true),
                        'selector' => implode(' ', $selector),
                    ];
                    Event::notify('larakit::thumb_size', $data);
                }
                //                $this->info($url);

            } catch (\Exception $e) {
                //                $this->error(trim($e->getMessage()) . ':' . $e->getLine());
            }
            $progress->advance();
        }

        $progress->finish();
        $this->info('');
    }

    protected function parseModel(array $thumb_route) {
        $vendor  = Arr::get($thumb_route, 'vendor');
        $package = Arr::get($thumb_route, 'package');
        $class   = '\\' . \Str::studly(str_replace('-', '\_', $vendor)) . '\Model\\' . \Str::studly($package);
        if (!class_exists($class)) {
            return false;
        }

        return $class;
    }

    protected function importTables() {
        $this->info('Начало импорта таблиц');

        $files = \File::AllFiles($this->tmp_dir . 'sql');

        $progress = new \Symfony\Component\Console\Helper\ProgressBar($this->output, sizeof($files));
        $progress->setFormat('debug');
        $progress->start();

        foreach ($files as $file) {
            /* @var $file SplFileInfo */
            $table = $file->getFilename();
            $data  = include $file->getRealPath();
            try {
                \DB::table($table)->truncate();

                if (is_array($data)) {
                    foreach ($data as $insert) {
                        \DB::table($table)->insert($insert);
                    }
                }
            } catch (\Exception $e) {
                $this->info('');
                $this->error('Ошибка синхронизации таблицы: ' . $table);
                $this->error($e->getMessage());
            }
            $progress->advance();
        }
        $progress->finish();

        $this->info('');
        $this->info('Таблицы успешно импортированы');
    }

    protected function createTmpDir() {
        $this->tmp_dir = storage_path('sync/' . date('Y-m-d-his') . '/');
        if (!is_dir($this->tmp_dir)) {
            mkdir($this->tmp_dir, 0777, true);
        }
        $this->info('Создана временная дирректория ' . ($this->tmp_dir));
    }

    protected function clearTmpDir() {
        if (is_dir($this->tmp_dir)) {
            \File::deleteDirectory($this->tmp_dir);
            $this->info('Удалена временная дирректория');
        }
    }

    protected function dumpTables() {
        $this->info('Копирование информации из таблиц');

        $progress = new \Symfony\Component\Console\Helper\ProgressBar($this->output, sizeof($this->tables));
        $progress->setFormat('debug');
        $progress->start();

        foreach ($this->tables as $table) {
            $data = \DB::connection($this->connection_name)->table($table)->get();
            $this->saveTable($table, $data);
            $progress->advance();
        }
        $progress->finish();

        $this->info('');
        $this->info('Таблицы успешно скопированы');
    }

    protected function saveTable($table_name, $data) {
        $dir = $this->tmp_dir . 'sql/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if ($data) {
            $data = array_map(function ($value) {
                    return (array)$value;
                },
                (array)$data);
            /*$lines = [];
            foreach ($data as $d) {
                $lines[] = $this->createSqlInsert($table_name, (array)$d);
            }*/
            file_put_contents($dir . $table_name, '<?php ' . PHP_EOL . 'return ' . var_export($data, true) . ';');
        }
    }

    protected function createSqlInsert($table, array $info) {
        /*        $keys   = array_keys($info);
                $values = array_map(
                    function ($value) {
                        return addslashes($value);
                    },
                    array_values($info)
                );*/

        //        $insert[] = 'INSERT INTO `' . $table . '`';
        //        $insert[] = '(`' . implode('`, `', $keys) . '`)';
        //        $insert[] = 'VALUES (\'' . implode('\', \'', $values) . '\');';

        //        return implode(' ', $insert);
    }

    protected function getTables() {
        $this->info('Начало получение таблиц');
        //        $connection = \DB::connection($this->connection_name);
        $dbname = \DB::connection($this->connection_name)->getDatabaseName();
        $sql    = 'select TABLE_NAME AS t from information_schema.tables where table_schema=\'' . $dbname . '\'';
        $tables = [];
        $select = \DB::connection($this->connection_name)->select($sql, [], false);
        foreach ($select as $k) {
            $k          = (string)$k->t;
            $tables[$k] = $k;
        }
        $tables = Arr::except($tables, $this->ignore_tables);

        $this->tables = $tables;
    }

    protected function staticDeploy() {
        $this->call('larastatic:deploy');
    }

    public function getArguments() {
        return [
        ];
    }

    public function getOptions() {
        return [];
    }


}