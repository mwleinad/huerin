<?php

if($_POST)
{
	$archivo->setContractId($_POST["contractId"]);
	$archivo->setTipoArchivoId($_POST["tipoArchivoId"]);
	$archivo->setDate($_POST["datef"]);
	$archivo->Save();
}
$smarty->assign("contractId", $_GET["id"]);

$tiposArchivo = $tipoArchivo->Enumerate();
$smarty->assign("tiposArchivo", $tiposArchivo);
?>