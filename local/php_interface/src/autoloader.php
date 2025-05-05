<?php
// declare(strict_types=1);

// if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
//     die();
// }

spl_autoload_register(function (string $class):void {
    // if(!str_contains($class, 'Otus')) {
    //     return;
    // }
    // $class = str_replace('\\', '/', $class);
    // $path = __DIR__ . '/' . $class . '.php';
    // pr($path);
    // if(is_file($path)) {
    //     file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/upload/sync_logs/class - " . date("Y-m-d") . ".json", print_r(($path), true) . PHP_EOL, FILE_APPEND);
    //     require_once $path;
    // }
    $classPath = str_replace('\\', '/', $class);
    // pr($classPath);
    $file = __DIR__."/$classPath.php";
    // pr( $file);
    if (file_exists($file)) {
        include_once $file;
    }
});