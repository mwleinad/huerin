<?php
/* Star Session Control Modules*/
$user->allowAccess(7);  //level 1
$user->allowAccess(249);//level 2
/* end Session Control Modules*/
$personals = $personal->Enumerate();
$smarty->assign("personals", $personals);
$smarty->assign('mainMnu', 'reportes');