<?php

/* Star Session Control Modules*/
$user->allowAccess(1);  //level 1
$user->allowAccess(182);//level 2
/* end Session Control Modules*/
$expediente->SetPage($_GET["p"]);
$expedientes = $expediente->Enumerate();
$smarty->assign("expedientes", $expedientes);
//print_r($resTipoArchivo);
$smarty->assign('mainMnu','catalogos');

?>