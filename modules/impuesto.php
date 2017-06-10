<?php
$user->allowAccess('personal');	
$impuesto->SetPage($_GET["p"]);
$resImpuesto = $impuesto->Enumerate();
$smarty->assign("resImpuesto", $resImpuesto);

	$smarty->assign('mainMnu','catalogos');
?>