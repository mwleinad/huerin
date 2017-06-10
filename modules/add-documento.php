<?php

if($_POST)
{
	$documento->setContractId($_POST["contractId"]);
	$documento->setTipoDocumentoId($_POST["tipoDocumentoId"]);
	$documento->Save();
}
$smarty->assign("contractId", $_GET["id"]);

$tiposDocumento = $tipoDocumento->Enumerate();
$smarty->assign("tiposDocumento", $tiposDocumento);
?>