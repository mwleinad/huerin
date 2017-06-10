<?php

if($_POST)
{
	$impuesto->setContractId($_POST["contractId"]);
	$impuesto->setImpuestoId($_POST["impuestoId"]);
	$impuesto->SaveToContract();
}
$smarty->assign("contractId", $_GET["id"]);

$impuestos = $impuesto->Enumerate();
$smarty->assign("impuestos", $impuestos);
?>