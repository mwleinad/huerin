<?php
//exit;
if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}
if($_SERVER['DOCUMENT_ROOT'] != "/var/www/html")
{
	$docRoot = $_SERVER['DOCUMENT_ROOT'];
}
else
{
	$docRoot = $_SERVER['DOCUMENT_ROOT'];
}

	define('DOC_ROOT', $docRoot);

	session_save_path("/tmp");

	include_once(DOC_ROOT.'/init_cron.php');
    include_once(DOC_ROOT.'/constants.php');
	include_once(DOC_ROOT.'/config.php');
	include_once(DOC_ROOT.'/libraries.php');

	$timeStart = date("d-m-Y").' a las '.date('H:i:s');
	
	if (!isset($_SESSION)) 
	{
	  session_start();
	}
	$_SESSION['empresaId'] = 21;

	$mask = DOC_ROOT.'/temp/15_A_*.*';
	$array = glob($mask);
	array_map('unlink', glob($mask));
	$mask = DOC_ROOT.'/temp/20_B_*.*';
	$array = glob($mask);
	array_map('unlink', glob($mask));
	//solo se crearan instancias para servicios en status activo o bajaParcial
	$cronServicio->CreateWorkflow();
	//exit;
	$time = date("d-m-Y").' a las '.date('H:i:s');
	$entry = "Cron ejecutado desde ".$timeStart." el $time Hrs.".chr(13);
	$file = DOC_ROOT."/cron/facturas.txt";
	$open = fopen($file,"w");

	if ( $open ) {		
    	fwrite($open,$entry);
	    fclose($open);
	}
	echo $entry.chr(13);

?>