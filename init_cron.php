<?php 

ini_set("session.cookie_lifetime","86400");
ini_set("session.gc_maxlifetime","86400");
ini_set("memory_limit","2048M");
ini_set("max_execution_time","0");

setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], time()+86400); 

if (!isset($_SESSION)) 
{
  session_start();
}

ini_set("display_errors", 0); 

date_default_timezone_set('America/Mexico_City');
header('Content-type: text/html; charset=utf-8');

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');

?>
