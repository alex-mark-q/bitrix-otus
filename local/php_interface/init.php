<?php

//var_dump($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/src/autoloader.php');

if(file_exists($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/src/autoloader.php')) {
    require_once($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/src/autoloader.php');
}