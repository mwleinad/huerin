<?php
$user->allowAccess('personal');	
$tipoDocumento->SetPage($_GET["p"]);
$resTipoDocumento = $tipoDocumento->Enumerate();
$smarty->assign("resTipoDocumento", $resTipoDocumento);

	$smarty->assign('mainMnu','catalogos');

?>