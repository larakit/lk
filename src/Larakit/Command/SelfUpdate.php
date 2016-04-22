<?php
namespace Larakit\Command;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Larakit\NiceSSH;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputOption;

class SelfUpdate extends Command {

    protected $name = 'self:update';

    protected $description = 'Обновление системы';

    protected $root = "";

    protected $svn_version = 0;

    /**
     * @var NiceSSH|null
     */
    protected $connection = null;

    function __construct() {
        parent::__construct();
        $this->root = storage_path() . '/self-update';
        if (!is_dir($this->root)) {
            mkdir($this->root, 0777, true);
        }
    }

    protected function getArguments() {
        return [
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return [
            [
                'force',
                null,
                InputOption::VALUE_OPTIONAL,
                'Форсированный запуск.',
                null
            ],
        ];
    }


    public function fire() {
        $loc_file     = $this->root . '/compile.loc';
        $queue_file   = $this->root . '/queue.txt';
        $version_file = $this->root . '/svn_version.txt';


        $option = $this->option('force');
        if ($option && file_exists($loc_file)) {
            unlink($loc_file);
        }
        if ($option && file_exists($version_file)) {
            unlink($version_file);
        }
        if ($option && file_exists($queue_file)) {
            unlink($queue_file);
        }

        $this->checkLocFile();


        $this->checkLoc($loc_file, $queue_file);
        $this->checkVersion($version_file);
        $this->checkRemoteVersion($version_file, $loc_file);
        $this->remoteSatisUpdate();
        $this->remoteComposerUpdate();
        $this->remoteStaticDeploy();
        $this->removeLocFile($loc_file);
        $this->checkQueue($queue_file);
    }

    protected function checkQueue($queue_file) {
        if (file_exists($queue_file)) {
            unlink($queue_file);
            $this->fire();
        }
    }

    protected function removeLocFile($loc_file) {
        $this->info('Удаляем файл блокировки');
        exec('rm ' . $loc_file);
    }

    protected function remoteStaticDeploy() {
        $this->info('Публикаем статику');
        exec('/usr/bin/php ' . base_path() . '/artisan larastatic:deploy');
    }

    protected function remoteSatisUpdate() {
        $this->info('Запуск обновление сатиса');
        $socket = fsockopen('packagist.larakit.ru', 80, $errno, $errstr, 90);

        $put = 'GET /compile.php HTTP/1.1' . PHP_EOL;
        $put .= "Host: packagist.larakit.ru" . PHP_EOL;
        $put .= "Connection: Close" . PHP_EOL . PHP_EOL;
        fputs($socket, $put);
        while ($f = fread($socket, 1024)) {
            $content = explode(PHP_EOL, trim($f));
            foreach ($content as $k => $c) {
                if ($k % 2) {
                    $this->writeFromSatis($c);
                    //                    $this->info($c);
                }
            }
            //            $first = array_shift($content);
            //            dump($content);
            //            $content = trim(implode(PHP_EOL, $content));
            //            $this->info($content);
        }
        fclose($socket);
        //        exit;
        //        echo file_get_contents('http://packagist.larakit.ru/compile.php');
        //=======
        //        $context = [
        //            'http' => [
        //                'timeout' => 600
        //            ]
        //        ];
        //
        //        echo file_get_contents('http://packagist.larakit.ru/compile.php', false, stream_context_create($context));
        //>>>>>>> .r1784
        $this->checkLocFile();
    }

    protected function writeFromSatis($c) {
        $type   = 'question';
        $string = $c;
        $content = $string;
        if (false !== strpos($c, 'Executing command (CWD):')) {
            $type   = 'error';
            $string = 'Executing command (CWD):';

            $content = '<' . $type . '>' . $string . '</' . $type . '>';
            $content = str_replace($string, $content, $c);
        }
        if (false !== strpos($c, 'Selected')) {
            $type   = 'info';
            $string = 'Selected';

            $content = '<' . $type . '>' . $string . '</' . $type . '>';
            $content = str_replace($string, $content, $c);
        }
        if (false !== strpos($c, 'Importing')) {
            $type   = 'info';
            $string = $c;

            $content = '<' . $type . '>' . $string . '</' . $type . '>';
            $content = str_replace($string, $content, $c);
        }
        if (false !== strpos($c, 'Reading')) {
            $type   = 'comment';
            $string = 'Reading';

            $content = '<' . $type . '>' . $string . '</' . $type . '>';
            $content = str_replace($string, $content, $c);
        }

//        $content = '<' . $type . '>' . $string . '</' . $type . '>';
//        $content = str_replace($string, $content, $c);

        $this->output->writeln($content);
    }

    protected function checkLocFile() {
        $file = 'http://packagist.larakit.ru/compile.loc';
        //        $file = 'http://packagist.larakit.ru/test.php';
        try {
            $data = file_get_contents($file);
        } catch (\Exception $e) {
            return false;
        }
        $date = Carbon::parse($data);
        $diff = (int)Carbon::now()
                           ->addHours(2)
                           ->diffInSeconds($date);
        $time = 3 * 60 - $diff;

        $this->info('Подождите пока satis обновится ' . $time);
        $progress = new ProgressBar($this->getOutput(), (int)$time);
        $progress->setFormat('debug');
        $progress->start();
        while ($time) {
            $progress->advance();
            sleep(1);
            $time--;
        }
        $progress->finish();
        $this->info('');
    }

    protected function remoteComposerUpdate() {
        $this->info('Запускаем обновление композера');
        exec('composer update -vvv --prefer-dist --profile  --working-dir=' . base_path());

    }

    protected function checkRemoteVersion($version_file, $loc_file) {
        exec('svn up ' . base_path(), $version);
        $version = (array)$version;
        $version = end($version);
        preg_match('|At revision ([\d]+)\.|Umsi', $version, $matches);
        $version = Arr::get($matches, 1, false);
        if ($version == $this->svn_version) {
            $this->info('Версия не изменилась (' . $version . ')');
            $this->removeLocFile($loc_file);
            exit;
        }
        else {
            file_put_contents($version_file, $version);
        }
    }

    protected function getPassword($password_file) {
        return trim(file_get_contents($password_file));
    }

    protected function checkVersion($version_file) {
        if (file_exists($version_file)) {
            $this->svn_version = intval(file_get_contents($version_file));
        }
    }

    protected function checkLoc($loc_file, $queue_file) {
        if (file_exists($loc_file)) {
            file_put_contents($queue_file, date('d.m.Y H:i:s'));
            die('Процесс уже запущен, задание добавлено в очередь');
        }
        else {
            file_put_contents($loc_file, date('d.m.Y H:i:s'));
        }
    }

    function checkFiles($files) {
        $files = (array)$files;
        foreach ($files as $f) {
            $this->checkFile($f);
        }
    }

    protected function checkFile($file) {
        if (!file_exists($file)) {
            throw new \Exception('Отсутствует файл: ' . $file);
        }
    }

}


