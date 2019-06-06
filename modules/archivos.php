<?php

    /* Star Session Control Modules*/
    $user->allowAccess(6);  //level 1
	$resDepartamentos = $departamentos->Enumerate();
	$smarty->assign("resDepartamentos", $resDepartamentos);
	$smarty->assign("id", $_GET["id"]);

	$departamentos->setDepartamentoId($_GET["id"]);
	$departamento = $departamentos->Info();

    //comprobar si el departamento pasado tiene permiso el rol
    $permisoId = $rol->GetPermisoByTitulo($departamento['departamento']);
    if($permisoId<=0)
        $permisoId=-1;

    $user->allowAccess($permisoId);

    $smarty->assign("departamento", $departamento);

	$archivos = $departamentos->Archivos();
	$smarty->assign("archivos", $archivos);
	
	$smarty->assign('mainMnu','archivos');