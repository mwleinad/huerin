<?php
$user->allowAccess(7);  //level 1
$user->allowAccess(163);//level 2

$departamentos = $departamentos->Enumerate();
$smarty->assign("departamentos", $departamentos);

$personals = $personal->Enumerate();
$smarty->assign("personals", $personals);

if($_SESSION["search"]["year"])
{
    $year = $_SESSION["search"]["year"];
}
else
{
    $year = date("Y");
}
$smarty->assign("year", $year);
$smarty->assign('mainMnu','reportes');
$rol->setRolId($User['roleId']);
$unlimited = $rol->ValidatePrivilegiosRol(array('gerente','supervisor','contador','auxiliar','cliente'));
$smarty->assign('unlimited',$unlimited);

?>