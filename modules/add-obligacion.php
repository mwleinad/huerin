<?php

if($_POST)
{
	$obligacion->setContractId($_POST["contractId"]);
	$obligacion->setObligacionId($_POST["obligacionId"]);
	$obligacion->SaveToContract();
}
$smarty->assign("contractId", $_GET["id"]);

$obligaciones = $obligacion->Enumerate();
$smarty->assign("obligaciones", $obligaciones);
?>