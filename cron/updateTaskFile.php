<?php

error_reporting(0);

$docs = "/var/www/";

$argv = $_SERVER['argv'];

$filename = $argv[1];

//$filename = str_replace("../", "", $filename);

//$filename = $docs.$filename;

$file_open = date("Y/m/d H:i:s.", filemtime($filename));

echo $file_open;