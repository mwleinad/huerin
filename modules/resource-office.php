<?php
$user->allowAccess(1);  //level 1
$user->allowAccess(251);//level 2
$smarty->assign("registros",$inventory->enumerateResource());
$smarty->assign("empleados",$personal->EnumerateAll());
$smarty->assign('mainMnu','catalogos');