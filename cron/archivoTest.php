<?php
//exit;
//if(!$_SERVER["DOCUMENT_ROOT"])
//{
//    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
//}
//
//if($_SERVER['DOCUMENT_ROOT'] != "/var/www/html")
//{
//	$docRoot = $_SERVER['DOCUMENT_ROOT'];
//}
//else
//{
//	$docRoot = $_SERVER['DOCUMENT_ROOT'];
//}
//
//	define('DOC_ROOT', $docRoot);

	session_save_path("/tmp");
        
        echo DOC_ROOT; exit(0);
	include_once(DOC_ROOT.'/init_cron.php');
	include_once(DOC_ROOT.'/config.php');
	include_once(DOC_ROOT.'/libraries.php');
//echo date("Y-m-d H:i:s");
//exit;
	$timeStart = date("d-m-Y").' a las '.date('H:i:s');
	
	if (!isset($_SESSION)) 
	{
	  session_start();
	}
	$_SESSION['empresaId'] = 15;
        
        $result = $archivos->creaEstructura();
?>