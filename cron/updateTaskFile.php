<?php

error_reporting(0);

if (!$_SERVER["DOCUMENT_ROOT"])
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . '/..');

if ($_SERVER['DOCUMENT_ROOT'] != "/var/www/html")
    $docRoot = $_SERVER['DOCUMENT_ROOT'];

define('DOC_ROOT', $docRoot);

session_save_path("/tmp");

include_once(DOC_ROOT . '/init.php');
include_once(DOC_ROOT . '/config.php');
include_once(DOC_ROOT . '/libraries.php');

$docs = "/var/www/";

$argv = $_SERVER['argv'];

$filename = $argv[1];

echo $filename;

$file_open = date("Y/m/d H:i:s.", filemtime($filename));

$file = split("/", $filename);

print_r($file);


//$this->Util()->DB()->setQuery("
//                                INSERT INTO `taskFile` 
//                                (
//                                `servicioId`, 
//                                `stepId`, 
//                                `taskId`, 
//                                `control`, 
//                                `version`, 
//                                `ext`, 
//                                `date`,
//                                `mime`
//                                ) 
//                                VALUES 
//                                (
//                                '" . $_POST["servicioId"] . "', 
//                                '" . $_POST["stepId"] . "', 
//                                '" . $_POST["taskId"] . "', 
//                                '" . $_POST["control"] . "', 
//                                '" . $version . "', 
//                                '" . $ext . "', 
//                                '" . date("Y-m-d") . "', 
//                                '" . $_FILES["file"]["type"] . "'
//                                );"
//                            );
//$this->Util()->DB()->InsertData();
//
//$result = $this->StatusById($this->instanciaServicioId);
//$this->Util()->DB()->setQuery("UPDATE instanciaServicio SET class = '" . $result["class"] . "' WHERE instanciaServicioId = '" . $this->instanciaServicioId . "'");
//$this->Util()->DB()->UpdateData();
//
////enviar al jefe inmediato
//if ($version > 1) {
//    $user = new User;
//    $user->setUserId($User["userId"]);
//    $userInfoPrev = $user->Info();
//    //a quien
//    if ($userInfo['jefeContador'] != 0) {
//        $enviarA = $userInfo['jefeContador'];
//    } elseif ($userInfo['jefeSupervisor'] != 0) {
//        $enviarA = $userInfo['jefeSupervisor'];
//    } elseif ($userInfo['jefeGerente'] != 0) {
//        $enviarA = $userInfo['jefeGerente'];
//    } elseif ($userInfo['jefeSocio'] != 0) {
//        $enviarA = $userInfo['jefeSocio'];
//    }
//
//    $user->setUserId($enviarA);
//    $userInfo = $user->Info();
//    $subject = "El contador " . $userInfoPrev["name"] . " ha actualizado un archivo";
//    $body = "El archivo anterior y nuevo va adjunto en este correo.";
//    $sendmail = new SendMail;
//
//    $to = $userInfo["email"];
//    $to = "diego@avantika.com.mx";
//    $toName = $userInfo["name"];
//
//    $versionAnt = $version - 1;
//
//    $attachment = DOC_ROOT . "/tasks/" . $_POST["servicioId"] . "_" . $_POST["stepId"] . "_" . $_POST["taskId"] . "_" . $_POST["control"] . "_" . $version . "." . $ext;
//    $fileName = "ArchivoActualizado." . $ext;
//
//    $attachment2 = DOC_ROOT . "/tasks/" . $_POST["servicioId"] . "_" . $_POST["stepId"] . "_" . $_POST["taskId"] . "_" . $_POST["control"] . "_" . $versionAnt . "." . $ext;
//    $fileName2 = "ArchivoAnterior." . $ext;
//
//    $sendmail->Prepare($subject, $body, $to, $toName, $attachment, $fileName, $attachment2, $fileName2, "admin@avantikdads.com", "Administrador del Sistema");
//}