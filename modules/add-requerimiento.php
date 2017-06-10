<?php

if($_POST)
{
	$requerimiento->setContractId($_POST["contractId"]);
	$requerimiento->setTipoRequerimientoId($_POST["tipoRequerimientoId"]);
	$requerimiento->Save();
}
$smarty->assign("contractId", $_GET["id"]);

$tiposRequerimiento = $tipoRequerimiento->Enumerate();
$smarty->assign("tiposRequerimiento", $tiposRequerimiento);
?>