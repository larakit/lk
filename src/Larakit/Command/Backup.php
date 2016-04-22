<?php
namespace Larakit\Command;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Larakit\Base\Model;
use Larakit\Exception;
use Larakit\Manager\ManagerMigrator;
use Symfony\Component\Console\Helper\TableSeparator;

class Backup extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'larakit:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Бэкап БД';

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
        //
        $connection = \Config::get('database.default');
        $u        = \Config::get('database.connections.' . $connection . '.username');
        $p        = \Config::get('database.connections.' . $connection . '.password');
        $d        = \Config::get('database.connections.' . $connection . '.database');
        $dir      = storage_path('backups/' . date('Y/m/Y-m-d_H-i-s'));
        $f = date('Y-m-d_H-i-s') . '.sql';
        $sql_file = $dir . '/' . $f;
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $command = sprintf('mysqldump -u%s %s %s > %s',
            $u,
            $p ? '-p' . $p : '',
            $d,
            $sql_file);
        exec($command);
        $zip_file = storage_path('backups/' . date('Y/m/Y-m-d_H-i-s')) . '.zip';
        $zip      = new \ZipArchiveEx();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString($f, file_get_contents($sql_file));
        $zip->close();
        unlink($sql_file);
        rmdir($dir);

        $this->question('Бэкап создан!');
        $this->question($zip_file);
    }

}