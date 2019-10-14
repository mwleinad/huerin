<?php
include_once('init_files.php');
include_once('config.php');
include_once('libraries.php');
include_once("properties/errors.es.php");
$ext = @strtolower(end(explode('.', $_GET["file"])));

error_reporting(E_ALL);
ini_set('display_errors', 1);

$db->setQuery("SELECT taskFile.*, tipoServicio.nombreServicio, task.nombreTask, contract.name, instanciaServicio.date FROM taskFile
    LEFT JOIN task ON task.taskId = taskFile.taskId
    LEFT JOIN instanciaServicio ON instanciaServicio.instanciaServicioId = taskFile.servicioId
    LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
    LEFT JOIN contract ON contract.contractId = servicio.contractId
    LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
     WHERE taskFile.servicioId = ".$_GET["id"]);
$files = $db->GetResult();

$nombreServicio = $files[0]["nombreServicio"];
$nombreServicio = str_replace(" ", "_", $nombreServicio);
$nombreServicio = str_replace(".", "_", $nombreServicio);
$nombreServicio = str_replace(",", "_", $nombreServicio);
$nombreServicio = str_replace("/", "", $nombreServicio);
$nombreCliente = $files[0]["name"];
$nombreCliente = str_replace(" ", "_", $nombreCliente);
$nombreCliente = str_replace("'", "_", $nombreCliente);
$nombreCliente = str_replace(".", "_", $nombreCliente);
$nombreCliente = str_replace(",", "_", $nombreCliente);
$nombreCliente = str_replace("/", "", $nombreCliente);
$fecha = $files[0]["date"];
$fecha = str_replace("-", "_", $fecha);
$zip = DOC_ROOT."/archivos/".$fecha."_".$nombreCliente."_".$nombreServicio."_".$_GET["id"].".zip";

@unlink($zip);
$util->ZipTasks($zip, $files);
$mime = $mime_types["zip"];
$file = explode("/", $zip);
$filename = @end($file);
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"".$filename."\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($zip));
$file = $zip;
ob_clean();
flush();
readfile($file);
ob_clean();
flush();
?>
