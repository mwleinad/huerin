<?php

define('DOC_ROOT', '/opt/lampp/htdocs');

	include_once(DOC_ROOT.'/init.php');
	include_once(DOC_ROOT.'/config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	if (!isset($_SESSION)) 
	{
	  session_start();
	}
	
	$_SESSION['empresaId'] = 15;
	$servicio->CreateServiceInstances();

?>