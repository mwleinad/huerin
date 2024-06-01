<?php
$user->allowAccess(7);  //level 1
$user->allowAccess(163);//level 2

$departamentos = $departamentos->Enumerate();
$smarty->assign("departamentos", $departamentos);
$personal->isShowAll();
$personal->setLevelRol(3);
$gerentes = $personal->Enumerate();
$personal->setLevelRol(4);
$subgerentes = $personal->Enumerate();
$smarty->assign("personals", $gerentes);

$smarty->assign("year", date('Y'));
$smarty->assign('mainMnu','reportes');

$unlimited = $rol->accessAnyContract();
$smarty->assign('unlimited',$unlimited);
?>
