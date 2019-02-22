<?php
$user->allowAccess(3);  //level 1

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
$unlimited = $rol->ValidatePrivilegiosRol(array('supervisor','contador','auxiliar','cliente'));
$smarty->assign('unlimited',$unlimited);

?>