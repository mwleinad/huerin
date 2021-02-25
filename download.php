<?php
//include_once('init_files.php');
include_once('config.php');
include_once("properties/errors.es.php");
$ext = @strtolower(end(explode('.', $_GET["file"])));
$mime = $mime_types[$ext];
$file = explode("/", $_GET["file"]);
header('Content-Encoding: UTF-8');
header('Content-Disposition: attachment; filename='.@end($file));
header('Content-type:'.$mime." charset=utf-8");
//readfile(urldecode($_GET["file"]));
$_GET["file"] = str_replace(WEB_ROOT,"", $_GET["file"]);
$file = DOC_ROOT."/".$_GET["file"];
readfile($file);
?>
