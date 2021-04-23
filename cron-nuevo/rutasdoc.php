<?php
if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}

$docRoot = $_SERVER['DOCUMENT_ROOT'];
define('DOC_ROOT', $docRoot);
include_once(DOC_ROOT.'/init.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/libraries.php');

$file_temp = DOC_ROOT."/documento.csv";

if(is_file($file_temp))
    echo "archivo existe";
else
    echo "arhco no esiste";

$fp = fopen($file_temp,'r');
$fila= 1;
while(($row=fgetcsv($fp,4096,","))==true) {
    if($fila == 1) {
        $fila++;
        continue;
    }
    $name_file =  $row[1]."_".$row[4];
    if(is_file(DOC_ROOT."/documentos/".$name_file)) {
        echo "todo en orden fila ". $fila."\n";
    } else {
        echo "el archivo. ".$row[4]." de la fila ". $fila." no existe\n";
    }
    $fila++;
}
