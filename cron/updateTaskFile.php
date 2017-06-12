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
$fileUpdated = date("Y/m/d H:i:s.", filemtime($filename));
$ext = pathinfo($filename, PATHINFO_EXTENSION );
$finfo = finfo_open(FILEINFO_MIME_TYPE); // devuelve el tipo mime de su extensión
$mimeType = finfo_file($finfo, $filename);
$file = split("/", $filename);

//(
//    [0] =>
//    [1] => var
//    [2] => www
//    [3] => sistema
//    [4] => AAA070222AN6
//    [5] => 2017
//    [6] => 6
//    [7] => 325342_CERO        ***** INSTANCIA SERVICIO *****
//    [8] => 104_CONTABILIDAD   ***** STEP *****
//    [9] => 149_ACUSE          ***** TASK *****
//    [10] => .testfile.txt
//)

$count = count($file);

$splitVar = split("_", $file[$count-2]);
$taskId = $splitVar[0];

$splitVar = split("_", $file[$count-3]);
$stepId = $splitVar[0];

$splitVar = split("_", $file[$count-4]);
$instanciaServicioId = $splitVar[0];

$query = "SELECT MAX(version) FROM taskFile WHERE 
                        servicioId = ".$instanciaServicioId." AND
                        stepId = '".$stepId."' AND
                        taskId = '".$taskId."' AND
                        control = '1' AND
                        uploaded = '".$fileUpdated."'";
$db->setQuery($query);
$version = $db->GetSingle();

if($version == 0){

    $query = "INSERT INTO `taskFile` 
            (
            `servicioId`, 
            `stepId`, 
            `taskId`, 
            `control`, 
            `version`, 
            `ext`, 
            `date`,
            `mime`,
            `uploaded`
            ) 
            VALUES 
            (
            '" . $instanciaServicioId . "', 
            '" . $stepId . "', 
            '" . $taskId . "', 
            '1', 
            '" . $version . "', 
            '" . $ext . "', 
            '" . date("Y-m-d") . "', 
            '" . $mimeType . "',
            '".$fileUpdated."'
            );";
    $db->setQuery($query);
    $db->InsertData();
}

$result = $this->StatusById($this->instanciaServicioId);
$db->setQuery("UPDATE instanciaServicio SET class = '" . $result["class"] . "' WHERE instanciaServicioId = '" . $this->instanciaServicioId . "'");
$db->UpdateData();

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