<?php
$user->allowAccess(7);  //level 1
$user->allowAccess(281);  //level 1
/* end Session Control Modules*/


$year = date("Y");
$smarty->assign("year", $year);
$personals = $personal->Enumerate();
$departamentos = $departamentos->Enumerate();

$smarty->assign("personals", $personals);
$smarty->assign("departamentos", $departamentos);
$smarty->assign('mainMnu', 'reportes');

?>
