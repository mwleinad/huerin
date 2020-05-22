<?php 

if (!isset($_SESSION)){
  session_start();
}

ini_set("display_errors", "ON");
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_WARNING);

date_default_timezone_set('America/Mexico_City');
header('Content-type: text/html; charset=iso-8859-1');

?>