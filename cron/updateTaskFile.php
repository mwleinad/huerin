<?php

error_reporting(0);

if(!$_SERVER["DOCUMENT_ROOT"])
        $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');

if($_SERVER['DOCUMENT_ROOT'] != "/var/www/html")
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
else
        $docRoot = $_SERVER['DOCUMENT_ROOT'];

define('DOC_ROOT', $docRoot);

echo DOC_ROOT; exit(0);

session_save_path("/tmp");

include_once(DOC_ROOT.'/init.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/libraries.php');

$docs = "/var/www/";

$argv = $_SERVER['argv'];

$filename = $argv[1];

//$filename = str_replace("../", "", $filename);

//$filename = $docs.$filename;

$file_open = date("Y/m/d H:i:s.", filemtime($filename));

echo $file_open;