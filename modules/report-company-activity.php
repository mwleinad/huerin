<?php
$user->allowAccess(7);  //level 1
$user->allowAccess(282);  //level 1
/* end Session Control Modules*/


$year = date("Y");
$smarty->assign("year", $year);
$personal->isShowAll();
$personal->setLevelRol(2);
$personals = $personal->Enumerate();
$smarty->assign("personals", $personals);
$departamentos = $departamentos->Enumerate();

$sectores = $catalogue->ListSectores();
$smarty->assign("sectores", $sectores);

$smarty->assign("departamentos", $departamentos);
$smarty->assign('mainMnu', 'reportes');

?>
