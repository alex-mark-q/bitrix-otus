<?php

// var_dump($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/src/autoloader.php');

if(file_exists($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/src/autoloader.php')) { 
    require_once($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/src/autoloader.php');
}

function pr($var, $type = false) {
    echo '<pre style="font-size:10px; border:1px solid #000; background:#FFF; text-align:left; color:#000;">';
    if ($type)
        var_dump($var);
    else
        print_r($var);
    echo '</pre>';
}