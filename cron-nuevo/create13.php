<?php

if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}

if($_SERVER['DOCUMENT_ROOT'] != "/var/www/html")
{
	$docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin_test";
}
else
{
	$docRoot = $_SERVER['DOCUMENT_ROOT'];
}

	define('DOC_ROOT', $docRoot);

	session_save_path("/tmp");

include_once(DOC_ROOT.'/init_cron.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/libraries33.php');

include_once(DOC_ROOT.'/services/month13.class.php');
$month13 = new Month13;

	
	if (!isset($_SESSION)) 
	{
	  session_start();
	}
	
	$_SESSION['empresaId'] = 15;
	$month13->CreateMonth13();

	$time = date("d-m-Y").' a las '.date('H:i:s');
	$entry = "Cron ejecutado el $time Hrs.";
	$file = DOC_ROOT."/cron/create13.txt";
	$open = fopen($file,"w");

	if ( $open ) {		
    	fwrite($open,$entry);
	    fclose($open);
	}

?>
