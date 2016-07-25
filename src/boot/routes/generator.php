<?php
\Larakit\CRUD\CrudRow::register(\Larakit\Models\Entity::class, '/admincp/entities');

define('ROUTE_ADMIN_CODEGEN', 'larakit::admin.codegen');
\Larakit\Route\Route::item(ROUTE_ADMIN_CODEGEN)
    ->setBaseUrl('/admincp/generator')
    ->put()
    ->addSegment('{model}')
    ->put()
;
define('ROUTE_ADMIN', 'larakit::admin');
\Larakit\Route\Route::item(ROUTE_ADMIN)
    ->setBaseUrl('/admincp/')
    ->put()
;

\Adminlte\Widget\WidgetSidebarMenu::group('ГЕНЕРАТОР КОДА')
    ->addItem('codegen', 'Модели', ROUTE_ADMIN_CODEGEN)
;
return;
$ret = [];
foreach(Schema::getColumnListing('bmmaket-core__recommend_groups') as $name){
    $ret[$name] = Schema::getColumnType('bmmaket-core__recommend_groups', $name);
}
dd($ret);
dd(DB::table('bmmaket-core__recommend_groups'));
