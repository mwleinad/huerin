<?php
$user->allowAccess(7);  //level 1
$user->allowAccess(276);//level 2


$departamentos = $departamentos->Enumerate();
$smarty->assign("departamentos", $departamentos);
$personal->isShowAll();
$personal->setLevelRol(2);
$personals = $personal->Enumerate();
$smarty->assign("personals", $personals);

$smarty->assign("year", date('Y'));
$smarty->assign('mainMnu','reportes');
?>
