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

$sql = "select vencimiento, no_serie, marca, modelo, no_licencia, codigo_activacion
        from office_resource where tipo_recurso = 'software' ";
$db->setQuery($sql);
$result = $db->GetResult();
$softwareVencido = [];
foreach($result as $key => $var) {
 $dataVencimiento  = $inventory->calculateVencimientoSoftware($var);
 $var['dataVencimiento'] = $dataVencimiento;
 if ($dataVencimiento['vencido'] === null)
     continue;

 if($dataVencimiento['vencido'] || $dataVencimiento['diasxvencer'] >= 15) {
    array_push($softwareVencido, $var);
 }
}
if(count($softwareVencido) > 0) {

}
