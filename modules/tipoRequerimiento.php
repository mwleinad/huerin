<?php

/* Star Session Control Modules*/
$user->allowAccess(1);  //level 1
$user->allowAccess(42);//level 2
/* end Session Control Modules*/
$tipoDocumento->SetPage($_GET["p"]);
$resTipoRequerimiento = $tipoRequerimiento->Enumerate();
$smarty->assign("resTipoRequerimiento", $resTipoRequerimiento);

	$smarty->assign('mainMnu','catalogos');

?>