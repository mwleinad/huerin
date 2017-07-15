<?php
include_once('init_files.php');
include_once('config.php');
include_once('libraries.php');
include_once("properties/errors.es.php");
$ext = @strtolower(end(explode('.', $_GET["file"])));

error_reporting(E_ALL);
ini_set('display_errors', 1);

$db->setQuery("SELECT taskFile.*, 
    tipoServicio.nombreServicio, 
    task.nombreTask, 
    contract.name, 
    instanciaServicio.date, 
    contract.customerId, 
    customer.nameContact as clientName,
    contract.rfc, 
    tipoServicio.nombreServicio
    FROM taskFile
    LEFT JOIN task ON task.taskId = taskFile.taskId
    LEFT JOIN instanciaServicio ON instanciaServicio.instanciaServicioId = taskFile.servicioId
    LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
    LEFT JOIN contract ON contract.contractId = servicio.contractId
    LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
    LEFT JOIN customer ON customer.customerId = contract.customerId
     WHERE taskFile.servicioId = ".$_GET["id"]);
$files = $db->GetResult();

$nombreServicio = $files[0]["nombreServicio"];
$nombreServicio = str_replace(" ", "_", $nombreServicio);
$nombreCliente = $files[0]["name"];
$nombreCliente = str_replace(" ", "_", $nombreCliente);
$fecha = $files[0]["date"];
$fecha = str_replace("-", "_", $fecha);
$zip = DOC_ROOT."/archivos/".$fecha."_".$nombreCliente."_".$nombreServicio."_".$_GET["id"].".zip";

@unlink($zip);
$util->ZipTasks($zip, $files);

$mime = $mime_types["zip"];
$file = explode("/", $zip);
header('Content-Disposition: attachment; filename='.@end($file));
header('Content-type:'.$mime);
//readfile(urldecode($_GET["file"]));
$_GET["file"] = str_replace(WEB_ROOT,"", $_GET["file"]);

$file = $zip;

readfile($file);

?>
