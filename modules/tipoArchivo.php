<?php

/* Star Session Control Modules*/
$user->allowAccess(1);  //level 1
$user->allowAccess(47);//level 2
/* end Session Control Modules*/
$tipoArchivo->SetPage($_GET["p"]);
$resTipoArchivo = $tipoArchivo->Enumerate();
$smarty->assign("resTipoArchivo", $resTipoArchivo);
//print_r($resTipoArchivo);
	$smarty->assign('mainMnu','catalogos');

?>