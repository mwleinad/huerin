<?php

	include_once('init.php');
	include_once('config.php');
	include_once(DOC_ROOT.'/libraries.php');
	
	if (!isset($_SESSION)) 
	{
	  session_start();
	}
	$workflow->setInstanciaServicioId($_GET["id"]);
	$workflow->DeleteControl($_GET["delete"]);
	header("Location:".WEB_ROOT."/report-servicio-drill");
?>