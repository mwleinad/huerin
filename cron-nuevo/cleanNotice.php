<?php
/**
 * Created by PhpStorm.
 * User: RAGNAR
 * Date: 13/08/2018
 * Time: 03:20 PM
 */
ini_set('memory_limit','3G');

if(!$_SERVER["DOCUMENT_ROOT"])
{
    $_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__).'/..');
}
if($_SERVER['DOCUMENT_ROOT'] != "/var/www/mainplatform/public_html")
{
    $docRoot = $_SERVER['DOCUMENT_ROOT']."/huerin";
}
else
{
    $docRoot = $_SERVER['DOCUMENT_ROOT'];
}
define('DOC_ROOT', $docRoot);
include_once(DOC_ROOT.'/init.php');
include_once(DOC_ROOT.'/config.php');
include_once(DOC_ROOT.'/libraries.php');

$sql =  "SELECT noticeId,fecha,url,status FROM notice WHERE status='vigente' AND (url='' OR UPPER(description) LIKE '%VERIFIQUEN SUS XML%' OR UPPER(description) LIKE '%ERRORES EN COI%')  ORDER BY fecha DESC";
$db->setQuery($sql);
$result = $db->GetResult();
$current = date('Y-m-d');
$count=0;
foreach($result as $key => $value){
    $fecha="";
    $fecha =  $value['fecha'];
    $fecha =  strtotime('+3 month',strtotime($fecha));
    $fecha = date('Y-m-d',$fecha);
    if($current>$fecha)
    {
        $count++;
        $db->setQuery("UPDATE notice SET status='obsoleta' WHERE noticeId='".$value['noticeId']."' ");
        $db->UpdateData();
    }
}
echo $count." avisos se movieron a obsoleto ".chr(10).chr(13);
