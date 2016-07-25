<?php
/**
 * Created by Larakit.
 * Link: http://github.com/larakit
 * User: Alexey Berdnikov
 * Date: 16.06.16
 * Time: 10:41
 */

namespace Adminlte\Controllers;

use App\Models\RecommendGroup;
use Larakit\Controller as LarakitController;
use Larakit\CRUD\Entity;
use Larakit\CRUD\TraitEntity;
use Larakit\QuickForm\LaraForm;

class AdminController extends LarakitController {
    use TraitEntity;

    protected $layout = 'lk-adminlte::!.layouts.default';

    function __construct() {
        \LaraPage::html()->setAttribute('ng-app', "larakit");
        \LaraPage::addBreadCrumb(ROUTE_ADMIN);
    }

//    static function getEntityPrefix() {
//        return 'AdminCont';
//    }

//    static function getEntitySuffix() {
//        return 'Controller';
//    }

    function dashboard() {
        \LaraPage::html()->ngApp();
        $form = new LaraForm('test');
        $form->putEmailTwbs('email')->setLabel('E-mail');
        $form->putSubmitTwbs('Отправить')->addClass('btn-success');
        return $this->setLayout('lk-adminlte::admin')->response([
            'content' => $form,
        ]);
    }
}