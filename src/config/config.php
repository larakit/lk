<?php
$build_url  = env('larastatic.build_url', '/!/build/');
return [
    //в эту директорию будут складываться билды
    'body_class' => env('ADMINLTE.BODY_CLASS', 'skin-blue sidebar-mini'),
];