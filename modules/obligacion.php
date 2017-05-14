<?php
$user->allowAccess('personal');	
$obligacion->SetPage($_GET["p"]);
$resObligacion = $obligacion->Enumerate();
$smarty->assign("resObligacion", $resObligacion);

	$smarty->assign('mainMnu','catalogos');
?>