<?php
/* Star Session Control Modules*/
$user->allowAccess(1);  //level 1
$user->allowAccess(37);//level 2
/* end Session Control Modules*/
$tipoDocumento->SetPage($_GET["p"]);
$resTipoDocumento = $tipoDocumento->Enumerate();
$smarty->assign("resTipoDocumento", $resTipoDocumento);

	$smarty->assign('mainMnu','catalogos');

?>