<?php
$user->allowAccess(7);
$user->allowAccess(225);
/* End Session Control */
if($_SESSION["search"]["year"])
{
    $year = $_SESSION["search"]["year"];
}
else
{

    $year = date("Y");
}
$departamentos = $departamentos->Enumerate();
$smarty->assign("departamentos", $departamentos);

$personal->isShowAll();
$personal->setLevelRol(3);
$gerentes = $personal->Enumerate();
$personal->setLevelRol(4);
$subgerentes = $personal->Enumerate();
$smarty->assign("personals", $gerentes);

$smarty->assign("result",$result);
$smarty->assign("year", $year);
$smarty->assign('mainMnu','reportes');
