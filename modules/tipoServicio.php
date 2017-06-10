<?php
$user->allowAccess('personal');	
$tipoServicio->SetPage($_GET["p"]);
$resTipoServicio = $tipoServicio->Enumerate();
$smarty->assign("resTipoServicio", $resTipoServicio);

	$smarty->assign('mainMnu','catalogos');

?>