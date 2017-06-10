<?php
$user->allowAccess('personal');	
$tipoArchivo->SetPage($_GET["p"]);
$resTipoArchivo = $tipoArchivo->Enumerate();
$smarty->assign("resTipoArchivo", $resTipoArchivo);
//print_r($resTipoArchivo);
	$smarty->assign('mainMnu','catalogos');

?>