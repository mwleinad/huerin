<?php

switch($_SERVER['HTTP_HOST'])
{
	case "localhost": //Configuracion Local

			if(strpos($_SERVER['REQUEST_URI'],'huerin_test')){
				$webRoot = "http://".$_SERVER['HTTP_HOST']."/huerin_test";
				$docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin_test";
				$sqlUser = "root";
				$sqlPw = "admonavanti";
				$sqlHost = "52.7.45.195:63306";
				$projectStatus = "test";
				$servicioContabilidad = 1;
			}else{
				$webRoot = "http://".$_SERVER['HTTP_HOST'];
				$docRoot = $_SERVER['DOCUMENT_ROOT'];
				$sqlPw = "root";
				$sqlHost = "localhost";
				$projectStatus = "produccion";
				$servicioContabilidad = 2;
			}

		break;

	case "52.7.45.195": //Server de Pruebas
			$webRoot = "http://".$_SERVER['HTTP_HOST']."/huerin_test";
			$docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin_test";
			$sqlUser = "root";
			$sqlPw = "admonavanti";
			$sqlHost = "52.7.45.195:63306";
			$projectStatus = "test";
			$servicioContabilidad = 1;
		break;

	default:	 //Server de Produccion
			$webRoot = "http://".$_SERVER['HTTP_HOST'];
			$docRoot = $_SERVER['DOCUMENT_ROOT'];
			$sqlPw = "root";
			$sqlHost = "localhost";
			$projectStatus = "produccion";
			$servicioContabilidad = 2;
		break;
}

define('DOC_ROOT', $docRoot);
define('WEB_ROOT', $webRoot);
define('PROJECT_STATUS', $projectStatus);

define('SQL_HOST', $sqlHost);
define('SQL_DATABASE', 'huerin');
define('SQL_USER', 'root');
define('SQL_PASSWORD', $sqlPw);

define("SQL_DATABASE_REMOTE", 'pascacio_general');
define("SQL_USER_REMOTE",'admin');
define("SQL_PASSWORD_REMOTE",'Strong47');
define("SQL_HOST_REMOTE", "74.200.73.174:3306");

?>
