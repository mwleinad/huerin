<?php

include_once('init_files.php');
include_once('config.php');
include_once('libraries.php');
include_once("properties/errors.es.php");

$ext = @strtolower(end(explode('.', $_GET["file"])));

/* if($ext == "pdf")
  {
  header("Location:".$_GET["file"]);
  exit();
  }
 */
$file = DOC_ROOT . "/" . $_GET["file"];

if (is_file($file)) {
    $mime = $mime_types[$ext];
    $file = explode("/", $_GET["file"]);
    header('Content-Disposition: attachment; filename=' . @end($file));
    header('Content-type:' . $mime);
    //readfile(urldecode($_GET["file"]));
    $_GET["file"] = str_replace(WEB_ROOT, "", $_GET["file"]);
    $file = DOC_ROOT . "/" . $_GET["file"];
} else {

    $explodedRoute = explode("/", $_GET['file']);
    $fileNoExt = rtrim($explodedRoute[1], ".zip");
    $explodedFileName = explode("_", $explodedRoute[1]);

    $query = "SELECT taskFile.*, 
    tipoServicio.nombreServicio, 
    contract.name, 
    instanciaServicio.date, 
    contract.customerId, 
    customer.nameContact as clientName,
    contract.rfc, 
    tipoServicio.nombreServicio, 
    task.nombreTask, 
    step.nombreStep
    FROM taskFile
    LEFT JOIN task ON task.taskId = taskFile.taskId
    LEFT JOIN step ON step.stepId = taskFile.stepId
    LEFT JOIN instanciaServicio ON instanciaServicio.instanciaServicioId = taskFile.servicioId
    LEFT JOIN servicio ON servicio.servicioId = instanciaServicio.servicioId
    LEFT JOIN contract ON contract.contractId = servicio.contractId
    LEFT JOIN tipoServicio ON tipoServicio.tipoServicioId = servicio.tipoServicioId
    LEFT JOIN customer ON customer.customerId = contract.customerId
    WHERE taskFile.stepId = " . $explodedFileName[1] . " AND taskFile.taskId = " . $explodedFileName[2] . " AND taskFile.servicioId = " . $explodedFileName[0];

    $db->setQuery($query);

    $result = $db->getRow();

    $fecha = $result['date'];
    $nombreCliente = str_replace(" ", "_", $result['clientName']);
    $nombreServicio = $result['nombreServicio'];
    $nombreStep = $result['nombreStep'];
    $nombreTask = $result['nombreTask'];
    $dateExploded = explode("-", $result['date']);
    $dateExploded[1] += 0;

    echo $dirName = FILES_ROOT . $nombreCliente . "_" . $result['customerId'] . "/" . $result['rfc'] . "/" . $dateExploded[0] . "/" . $dateExploded[1] . "/" . $result['servicioId'] . "_" . $result['nombreServicio'] . "/" . $result['stepId'] . "_" . $result['nombreStep'] . "/" . $result['taskId'] . "_" . $result['nombreTask'];
exit(0);
    $zipPath = DOC_ROOT . "/archivos/" . $fecha . "_" . $nombreCliente . "_" . $nombreServicio . "_" . $nombreStep . "_" . $nombreTask . ".zip";
    @unlink($zip);

    $zip = new ZipArchive();
    $res = $zip->open($zipPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
    if ($res === TRUE) {

        if (!is_dir($dirName)) {
            //throw new Exception('Directory ' . $dirName . ' does not exist');
            return false;
        }

        $dirName = realpath($dirName);
        if (substr($dirName, -1) != '/') {
            $dirName .= '/';
        }

        $dirStack = array($dirName);
        //Find the index where the last dir starts 
        $cutFrom = strrpos(substr($dirName, 0, -1), '/') + 1;

        while (!empty($dirStack)) {
            $currentDir = array_pop($dirStack);
            $filesToAdd = array();

            $dir = dir($currentDir);
            while (false !== ($node = $dir->read())) {
                if (($node == '..') || ($node == '.')) {
                    continue;
                }
                if (is_dir($currentDir . $node)) {
                    array_push($dirStack, $currentDir . $node . '/');
                }
                if (is_file($currentDir . $node)) {
                    $filesToAdd[] = $node;
                }
            }

            $localDir = substr($currentDir, $cutFrom);
            $zip->addEmptyDir($localDir);

            foreach ($filesToAdd as $file) {
                $zip->addFile($currentDir . $file, $localDir . $file);
            }
        }

        $zip->close();
    }
    $mime = $mime_types["zip"];
    $file = explode("/", $zip);
    header('Content-Disposition: attachment; filename=' . @end($file));
    header('Content-type:' . $mime);
    //readfile(urldecode($_GET["file"]));
    $_GET["file"] = str_replace(WEB_ROOT, "", $_GET["file"]);

    $file = $zip;
}

readfile($file);
?>
