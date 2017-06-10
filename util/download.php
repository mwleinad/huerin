<?php
	include_once('../init_files.php');
	include_once('../config.php');
include_once(DOC_ROOT."/properties/errors.es.php");

if(!$_GET["file"])
{
	$_GET["file"] = $_GET["path"]."/".$_GET["secPath"]."/".$_GET["filename"];
}

$ext = @strtolower(end(explode('.', $_GET["file"])));
$mime = $mime_types[$ext];
$file = explode("/", $_GET["file"]);
header('Content-disposition: attachment; filename='.end($file));
header('Content-type:'.$mime);
//readfile(urldecode($_GET["file"]));
//echo $_GET["file"];
$_GET["file"] = str_replace(WEB_ROOT,"", $_GET["file"]);
//echo DOC_ROOT."/".$_GET["file"];
readfile(urldecode(DOC_ROOT."/".$_GET["file"]));

?>