<?php
$user->allowAccess(7);  //level 1
$user->allowAccess(163);//level 2

$departamentos = $departamentos->Enumerate();
$smarty->assign("departamentos", $departamentos);
$personal->isShowAll();

$personal->setLevelRol(2);
$directores = $personal->Enumerate();

$personal->setLevelRol(3);
$gerentes = $personal->Enumerate();

$personal->setLevelRol(4);
$subgerentes = $personal->Enumerate();

$personas = array_merge($gerentes, $subgerentes);
$smarty->assign("personals", $personas);

$smarty->assign("year", date('Y'));
$smarty->assign('mainMnu','reportes');

$unlimited = $rol->accessAnyContract();
$smarty->assign('unlimited',$unlimited);
?>
