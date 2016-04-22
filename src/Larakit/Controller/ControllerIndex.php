<?php
namespace Larakit\Controller;

use Bmmaket\Core\Model\Target;
use Illuminate\Database\Schema\Blueprint;
use Larakit\Attach;
use Larakit\Base\Controller;
use Larakit\Page\Page;
use Larakit\Route\Route;
use Larakit\Model\User;
use Larakit\Thumb;
use Larakit\User\Me;
use Larakit\Webconfig;
use Larakit\Widget\WidgetFlash;

class ControllerIndex extends Controller {
    protected $layout = 'larakit::!.layouts.public';

    function __construct() {
        Page::body()->addClass(Webconfig::get('app.body_class'));
        $this->addBreadCrumb('home');
    }

    function index() {
        //        for ($i = 1; $i < 6; $i++) {
        //            $column = 'col_' . $i;
        //            if (!\Schema::hasColumn('tests', $column)) {
        //                \Schema::table('tests',
        //                    function (Blueprint $table) use ($column) {
        //                        $table->string($column);
        //                        dump($column);
        //                    });
        //            }
        //        }
        //        $indexes      = [
        //            'idx_1_2' => [
        //                'col_1',
        //                'col_2'
        //            ],
        //            'idx_2_4' => [
        //                'col_2',
        //                'col_4'
        //            ],
        //        ];
        //        $res          = \DB::select('SHOW INDEX from tests');
        //        $exists_index = [];
        //        foreach ($res as $idx) {
        //            $idx_name                = $idx->Key_name;
        //            $exists_index[$idx_name] = $idx_name;
        //        }
        //        \Schema::table('tests',
        //            function (Blueprint $table) use ($exists_index, $indexes) {
        //                foreach ($indexes as $idx_name => $columns) {
        //                    if(!in_array($idx_name, $exists_index)){
        //                        $table->index($columns, $idx_name);
        //                    }
        //                }
        //            });
        //
        //        exit;

        //        $info = \Oembed::get('https://www.youtube.com/watch?v=PP1xn5wHtxE');
        //        dd($info);
        //        $parent_vendor = 'larakit';
        //        $parent_entity = 'attach';
        //        $parent_id     = 100;

        //        $vendor = 'larakit';
        //        $entity = 'attach';
        //        $id            = 100;
        //        $files  = [
        //            public_path() . '/' . 'logo.png',
        //            public_path() . '/' . 'london.mp3',
        //            public_path() . '/' . 'qwer.swf',
        //            public_path() . '/' . 'ChromeSetup.exe',
        //            public_path() . '/' . '16.gif',
        //            public_path() . '/' . 'kev.mp4',
        //            public_path() . '/' . '50.mid',
        //            public_path() . '/' . 'golovolomka.mp4',
        //            'http://cs5.pikabu.ru/images/big_size_comm_an/2015-10_1/1443881715159155152.gif',
        //        ];
        //        $errors = [];
        //        $user   = User::find(Me::id());
        //        foreach ($files as $file) {
        //            if (0) {
        //                $model = new \Larakit\Model\Attach;
        //                $res   = $model->attachFile($file);
        //                if (true === $res) {
        //                    $model->attachToObject($user);
        //                } else {
        //                    $errors[] = $res;
        //                }
        //            }
        //        }
        //
        //        if ($errors) {
        //            WidgetFlash::danger('<p>' . implode('</p><p>', $errors) . '</p>');
        //        }

        //        dd($user);
        //        $attach = Attach::factory('larakit', 'attach', 2);
        //        $attach->processing(public_path().'/ChromeSetup.exe');
        //        $attach = Attach::factory('larakit', 'attach', 1);
        //        $attach->processing(public_path().'/beer-on-a-white-background-08.jpg');
        //        $attach = Attach::factory('larakit', 'attach', 3);
        //        $attach->processing(public_path().'/logo.png');
        //        return $this->response([
        //            'attaches' => $user->attaches
        //        ]);
        return $this->response();
    }
}

