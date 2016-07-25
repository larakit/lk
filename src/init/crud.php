<?php
\Larakit\Twig::register_function('crud_row', function ($model, $name='admin', $tpl = null) {
    return new \Larakit\CRUD\CrudRow($model, $name, $tpl);
});