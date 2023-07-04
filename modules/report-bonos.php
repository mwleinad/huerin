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
$rol->setRolId($User['roleId']);
$unlimited = $rol->ValidatePrivilegiosRol(array('gerente','supervisor','contador','auxiliar','cliente'));
$smarty->assign('unlimited',$unlimited);
?>
