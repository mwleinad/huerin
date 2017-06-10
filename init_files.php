<?php 

if (!isset($_SESSION)){
  session_start();
}

ini_set("display_errors", 0); 
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

if(function_exists('xdebug_disable'))
	xdebug_disable();
	
date_default_timezone_set('America/Los_Angeles');

?>