<?php
$user->allowAccess('personal');	
$tipoDocumento->SetPage($_GET["p"]);
$resTipoRequerimiento = $tipoRequerimiento->Enumerate();
$smarty->assign("resTipoRequerimiento", $resTipoRequerimiento);

	$smarty->assign('mainMnu','catalogos');

?>