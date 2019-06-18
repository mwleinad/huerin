<?php
$user->allowAccess(7);
$user->allowAccess(215);
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
$personal->setRole(2);
$personals = $personal->Enumerate();
$smarty->assign("personals", $personals);

$smarty->assign("result",$result);
$smarty->assign("year", $year);
$smarty->assign('mainMnu','reportes');