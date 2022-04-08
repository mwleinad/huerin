<?php
$user->allowAccess(7);  //level 1
$user->allowAccess(276);//level 2


$departamentos = $departamentos->Enumerate();
$smarty->assign("departamentos", $departamentos);
$personal->isShowAll();
$personal->setLevelRol(2);
$gerentes = $personal->Enumerate();
$personal->setLevelRol(3);
$subgerentes = $personal->Enumerate();
$smarty->assign("personals", array_merge($gerentes, $subgerentes));

$smarty->assign("year", date('Y'));
$smarty->assign('mainMnu','reportes');
?>
