<?php

/* Star Session Control Modules*/
$user->allowAccess(1);  //level 1
$user->allowAccess(24);//level 2
/* end Session Control Modules*/
$tipoServicio->SetPage($_GET["p"]);
$resTipoServicio = $tipoServicio->EnumerateOnePage();
$smarty->assign("resTipoServicio", $resTipoServicio);
$smarty->assign('mainMnu','catalogos');

?>