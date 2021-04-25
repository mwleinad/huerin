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

echo "Cron sin funcion";
exit;

$file_temp = DOC_ROOT."/documento.csv";
$fp = fopen($file_temp,'r');
$fila= 1;
$update = 0;
$noupdate = 0;
while(($row=fgetcsv($fp,4096,","))==true) {
    if($fila == 1) {
        $fila++;
        continue;
    }
    $name_file =  $row[1]."_".$row[4];
    if(is_file(DOC_ROOT."/documentos/".$name_file)) {
        $sql = "update documento set path = '".$row[4]."' where documentoId = '".$row[0]."' ";
        $db->setQuery($sql);
        $row = $db->UpdateData();
        if($row)
            $update++;
    } else {
        $noupdate++;
        echo $row[0].",".$row[1].",".$row[2].",".$row[3].",".$row[4].chr(10).chr(13)."<br>";
    }
    $fila++;
}
echo "Total actualizado".$update.chr(10).chr(13)."<br>";
echo "Total no actualizado".$noupdate.chr(10).chr(13)."<br>";
