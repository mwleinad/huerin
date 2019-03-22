<?php
$user->allowAccess(7);  //level 1
$user->allowAccess(161);//level 2
/* end Session Control Modules*/

$personals = $personal->Enumerate();
$departamentos = $departamentos->Enumerate();

$smarty->assign("personals", $personals);
$smarty->assign("departamentos", $departamentos);
$smarty->assign('mainMnu', 'reportes');

?>