<?php
    /* Star Session Control Modules*/
    $user->allowAccess(7);  //level 1
    $user->allowAccess(215);//level 2
    /* end Session Control Modules*/
	//Obtenemos los Tipos de Contrato
    $personals = $personal->EnumerateGerenteDepartamento('juridico');
    $smarty->assign("personals", $personals);
    $smarty->assign("year", date('Y'));
	$smarty->assign('mainMnu','reportes');

?>