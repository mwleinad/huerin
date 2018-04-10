<?php

/* Star Session Control Modules*/
$user->allowAccess(1);  //level 1
$user->allowAccess(111);//level 2

$roles = $rol->Enumerate();
$smarty->assign('roles',$roles);
$smarty->assign('mainMnu','catalogos');