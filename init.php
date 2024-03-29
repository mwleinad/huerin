<?php
session_start();
ini_set("display_errors", "ON");
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED & ~E_NOTICE ^ E_WARNING);
if (isset($_GET['page']) && ($_GET['page'] == 'cfdi33-generate-pdf' || $_GET['page'] == 'vp_menu' || $_GET['page'] == 'resource-office-pdf' || $_GET['page'] == 'prospect-offer-pdf')) {
    date_default_timezone_set('America/Mexico_City');
    header('Content-type: text/html; charset=iso-8859-1');
} else {
    ini_set("memory_limit", "2048M");
    ini_set("max_execution_time", "7200");
    if (PHP_MAJOR_VERSION >= 7) {
        set_error_handler(function ($errno, $errstr) {
            return strpos($errstr, 'Declaration of') === 0;
        }, E_WARNING);
    }

    date_default_timezone_set('America/Mexico_City');
    header('Content-type: text/html; charset=utf-8');
    mb_internal_encoding('UTF-8');
    mb_http_output('UTF-8');
    mb_http_input('UTF-8');
    mb_language('uni');
    mb_regex_encoding('UTF-8');
    ob_start('mb_output_handler');
}
